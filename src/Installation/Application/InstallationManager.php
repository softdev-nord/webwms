<?php

declare(strict_types=1);

namespace WebWMS\Installation\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use RuntimeException;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use WebWMS\Administration\Application\Access\PermissionCatalog;
use WebWMS\Administration\Domain\Access\PasswordHasher;

final readonly class InstallationManager
{
    public function __construct(
        private string $projectDir,
        private PasswordHasher $passwordHasher,
        private V2DemoDataInstaller $demoDataInstaller,
        private V3DemoConfigurationInstaller $v3DemoConfigurationInstaller,
    ) {
    }

    public function isInstalled(): bool
    {
        return is_file($this->projectDir . '/var/installation.lock');
    }

    /** @return array<string, int|string> */
    public function databaseDefaults(): array
    {
        $databaseUrl = $this->databaseUrlFromEnvironment();
        if ($databaseUrl === null) {
            return [];
        }

        $parts = parse_url($databaseUrl);
        if (!is_array($parts) || !in_array($parts['scheme'] ?? null, ['mysql', 'mariadb'], true)) {
            return [];
        }

        $query = [];
        parse_str((string) ($parts['query'] ?? ''), $query);

        return array_filter([
            'database_host' => isset($parts['host']) ? rawurldecode($parts['host']) : null,
            'database_port' => isset($parts['port']) ? (int) $parts['port'] : 3306,
            'database_name' => isset($parts['path']) ? rawurldecode(ltrim($parts['path'], '/')) : null,
            'database_user' => isset($parts['user']) ? rawurldecode($parts['user']) : null,
            'database_password' => isset($parts['pass']) ? rawurldecode($parts['pass']) : '',
            'database_version' => isset($query['serverVersion']) && is_string($query['serverVersion']) ? $query['serverVersion'] : null,
        ], static fn (int|string|null $value): bool => $value !== null && $value !== '');
    }

    /** @return array{database: string, tables: int} */
    public function inspectDatabase(InstallationConfiguration $configuration): array
    {
        $connection = $this->connect($configuration);

        try {
            $database = (string) $connection->fetchOne('SELECT DATABASE()');
            $tables = (int) $connection->fetchOne(
                'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :database',
                ['database' => $configuration->databaseName],
            );

            return ['database' => $database, 'tables' => $tables];
        } finally {
            $connection->close();
        }
    }

    public function install(InstallationConfiguration $configuration): InstallationResult
    {
        if ($this->isInstalled()) {
            throw new RuntimeException('WebWMS is already installed.');
        }
        if (!filter_var($configuration->adminEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('The administrator e-mail address is invalid.');
        }
        if (mb_strlen($configuration->adminPassword) < 12) {
            throw new RuntimeException('The administrator password must contain at least 12 characters.');
        }
        if (!in_array($configuration->siteTimezone, timezone_identifiers_list(), true)) {
            throw new RuntimeException('The selected site timezone is invalid.');
        }

        $database = $this->inspectDatabase($configuration);
        if ($database['database'] !== $configuration->databaseName) {
            throw new RuntimeException('The configured database could not be selected. Create it before running the installer.');
        }
        if ($database['tables'] !== 0) {
            throw new RuntimeException('The installation database must be empty.');
        }

        $this->writeEnvironment($configuration);
        $this->runMigrations($configuration);

        $connection = $this->connect($configuration);
        $locationCount = 0;

        try {
            $connection->transactional(function (Connection $connection) use ($configuration, &$locationCount): void {
                $now = new DateTimeImmutable();
                $date = $now->format('Y-m-d H:i:s.u');
                $connection->insert('wms_tenant', [
                    'id' => InstallationIdentifiers::TENANT, 'name' => $configuration->tenantName,
                    'status' => 'active', 'created_at' => $date, 'updated_at' => $date,
                ]);
                $connection->insert('wms_site', [
                    'id' => InstallationIdentifiers::SITE, 'tenant_id' => InstallationIdentifiers::TENANT,
                    'code' => mb_strtoupper($configuration->siteCode), 'name' => $configuration->siteName,
                    'timezone' => $configuration->siteTimezone, 'status' => 'active',
                    'created_at' => $date, 'updated_at' => $date,
                ]);
                $connection->insert('wms_role', [
                    'id' => InstallationIdentifiers::ADMIN_ROLE, 'tenant_id' => InstallationIdentifiers::TENANT,
                    'code' => 'ROLE_ADMIN', 'name' => 'Administrator', 'created_at' => $date, 'updated_at' => $date,
                ]);
                foreach (PermissionCatalog::ALL as $permission) {
                    $connection->insert('wms_role_permission', [
                        'role_id' => InstallationIdentifiers::ADMIN_ROLE, 'permission_key' => $permission,
                    ]);
                }
                $connection->insert('wms_user_account', [
                    'id' => InstallationIdentifiers::ADMIN_USER, 'tenant_id' => InstallationIdentifiers::TENANT,
                    'email' => mb_strtolower($configuration->adminEmail), 'display_name' => $configuration->adminName,
                    'password_hash' => $this->passwordHasher->hash($configuration->adminPassword),
                    'status' => 'active', 'created_at' => $date, 'updated_at' => $date,
                ]);
                $connection->insert('wms_user_role', [
                    'user_id' => InstallationIdentifiers::ADMIN_USER, 'role_id' => InstallationIdentifiers::ADMIN_ROLE,
                ]);
                $connection->update('wms_site', ['created_by' => InstallationIdentifiers::ADMIN_USER], ['id' => InstallationIdentifiers::SITE]);
                if ($configuration->demoData) {
                    $locationCount = $this->demoDataInstaller->install(
                        $connection,
                        InstallationIdentifiers::TENANT,
                        InstallationIdentifiers::SITE,
                        InstallationIdentifiers::ADMIN_USER,
                        $now,
                    );
                    $this->v3DemoConfigurationInstaller->install(
                        $connection,
                        InstallationIdentifiers::TENANT,
                        InstallationIdentifiers::ADMIN_USER,
                        $date,
                    );
                }
            });

            $checks = $this->readinessChecks($connection, $configuration->demoData);
            if (in_array(false, $checks, true)) {
                throw new RuntimeException('The final installation readiness check failed.');
            }
        } finally {
            $connection->close();
        }

        $result = new InstallationResult(
            InstallationIdentifiers::TENANT,
            InstallationIdentifiers::SITE,
            InstallationIdentifiers::ADMIN_USER,
            $locationCount,
            $checks,
        );
        $this->writeLock($configuration, $result);

        return $result;
    }

    private function connect(InstallationConfiguration $configuration): Connection
    {
        try {
            $parser = new DsnParser([
                'mysql' => 'pdo_mysql',
                'mariadb' => 'pdo_mysql',
            ]);

            return DriverManager::getConnection($parser->parse($configuration->databaseUrl()));
        } catch (\Throwable $exception) {
            throw new RuntimeException('The database connection failed: ' . $exception->getMessage(), 0, $exception);
        }
    }

    private function databaseUrlFromEnvironment(): ?string
    {
        $runtimeValue = $_SERVER['DATABASE_URL'] ?? $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
        if (is_string($runtimeValue) && trim($runtimeValue) !== '') {
            return $this->normalizeDatabaseUrl($runtimeValue);
        }

        $values = [];
        $dotenv = new Dotenv();
        foreach (['.env', '.env.local'] as $filename) {
            $path = $this->projectDir . '/' . $filename;
            if (!is_file($path)) {
                continue;
            }
            $contents = file_get_contents($path);
            if (is_string($contents)) {
                $values = array_replace($values, $dotenv->parse($contents, $path));
            }
        }

        $value = $values['DATABASE_URL'] ?? null;

        return is_string($value) && trim($value) !== '' ? $this->normalizeDatabaseUrl($value) : null;
    }

    private function normalizeDatabaseUrl(string $databaseUrl): string
    {
        return str_replace('\/', '/', trim($databaseUrl));
    }

    private function runMigrations(InstallationConfiguration $configuration): void
    {
        $phpExecutable = (new PhpExecutableFinder())->find(false);
        if (!is_string($phpExecutable) || $phpExecutable === '') {
            throw new RuntimeException('The PHP CLI executable required for database migrations could not be found.');
        }

        $process = new Process([
            $phpExecutable, '-d', 'variables_order=EGPCS', $this->projectDir . '/bin/console',
            '--env=prod', '--no-debug', 'doctrine:migrations:migrate',
            '--no-interaction', '--allow-no-migration',
        ], $this->projectDir, [
            'DATABASE_URL' => $configuration->databaseUrl(),
            'APP_ENV' => 'prod',
            'APP_DEBUG' => '0',
            'SHELL_VERBOSITY' => '0',
        ]);
        $process->setTimeout(600);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException('Database migration failed: ' . trim($process->getErrorOutput() . "\n" . $process->getOutput()));
        }
    }

    private function writeEnvironment(InstallationConfiguration $configuration): void
    {
        $path = $this->projectDir . '/.env';
        $existing = is_file($path) ? (string) file_get_contents($path) : '';
        $lines = preg_split('/\R/', $existing) ?: [];
        $lines = array_values(array_filter($lines, static fn (string $line): bool => !str_starts_with($line, 'DATABASE_URL=') && !str_starts_with($line, 'APP_SECRET=')));
        $lines[] = 'DATABASE_URL=' . json_encode(
            $configuration->databaseUrl(),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES,
        );
        $lines[] = 'APP_SECRET=' . bin2hex(random_bytes(32));
        $contents = implode("\n", $lines) . "\n";
        if (file_put_contents($path . '.tmp', $contents, LOCK_EX) === false || !rename($path . '.tmp', $path)) {
            throw new RuntimeException('The local environment configuration could not be written.');
        }

        $compiledEnvironment = $this->projectDir . '/.env.local.php';
        if (is_file($compiledEnvironment)) {
            $backup = $this->projectDir . '/var/installation.env.local.php.backup';
            if (is_file($backup)) {
                $backup .= '.' . (new DateTimeImmutable())->format('YmdHis');
            }
            if (!rename($compiledEnvironment, $backup)) {
                throw new RuntimeException('The compiled environment cache could not be invalidated.');
            }
        }
    }

    /** @return array<string, bool> */
    private function readinessChecks(Connection $connection, bool $demoData): array
    {
        return [
            'environment' => is_file($this->projectDir . '/.env.local'),
            'schema' => (int) $connection->fetchOne("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name LIKE 'wms_%'") > 0,
            'migrations' => (int) $connection->fetchOne('SELECT COUNT(*) FROM doctrine_migration_versions') > 0,
            'tenant' => (bool) $connection->fetchOne('SELECT 1 FROM wms_tenant WHERE id = ?', [InstallationIdentifiers::TENANT]),
            'site' => (bool) $connection->fetchOne('SELECT 1 FROM wms_site WHERE id = ?', [InstallationIdentifiers::SITE]),
            'administrator' => (bool) $connection->fetchOne('SELECT 1 FROM wms_user_account WHERE id = ?', [InstallationIdentifiers::ADMIN_USER]),
            'permissions' => (int) $connection->fetchOne('SELECT COUNT(*) FROM wms_role_permission WHERE role_id = ?', [InstallationIdentifiers::ADMIN_ROLE]) === count(PermissionCatalog::ALL),
            'demo_data' => !$demoData || (int) $connection->fetchOne('SELECT COUNT(*) FROM wms_storage_location') > 0,
            'v3_demo_configuration' => !$demoData || (int) $connection->fetchOne('SELECT COUNT(*) FROM wms_number_range WHERE tenant_id = ?', [InstallationIdentifiers::TENANT]) >= 3,
        ];
    }

    private function writeLock(InstallationConfiguration $configuration, InstallationResult $result): void
    {
        $path = $this->projectDir . '/var/installation.lock';
        $payload = json_encode([
            'installed_at' => (new DateTimeImmutable())->format(DATE_ATOM),
            'database' => $configuration->databaseName,
            'demo_data' => $configuration->demoData,
            'tenant_id' => $result->tenantId,
            'site_id' => $result->siteId,
            'admin_id' => $result->adminId,
            'checks' => $result->checks,
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        if (file_put_contents($path, $payload . "\n", LOCK_EX) === false) {
            throw new RuntimeException('The installation lock could not be written.');
        }
    }
}
