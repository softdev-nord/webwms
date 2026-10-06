<?php

declare(strict_types=1);

namespace WebWMS\Installation\Application;

final readonly class InstallationConfiguration
{
    public function __construct(
        public string $databaseHost,
        public int $databasePort,
        public string $databaseName,
        public string $databaseUser,
        public string $databasePassword,
        public string $databaseVersion,
        public bool $demoData,
        public string $tenantName,
        public string $siteCode,
        public string $siteName,
        public string $siteTimezone,
        public string $adminName,
        public string $adminEmail,
        public string $adminPassword,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            self::string($data, 'database_host'),
            self::integer($data, 'database_port'),
            self::string($data, 'database_name'),
            self::string($data, 'database_user'),
            (string) ($data['database_password'] ?? ''),
            self::string($data, 'database_version'),
            filter_var($data['demo_data'] ?? false, FILTER_VALIDATE_BOOL),
            self::string($data, 'tenant_name'),
            self::string($data, 'site_code'),
            self::string($data, 'site_name'),
            self::string($data, 'site_timezone'),
            self::string($data, 'admin_name'),
            self::string($data, 'admin_email'),
            self::string($data, 'admin_password'),
        );
    }

    public function databaseUrl(): string
    {
        $host = strtolower($this->databaseHost) === 'localhost' ? '127.0.0.1' : $this->databaseHost;

        return sprintf(
            'mysql://%s:%s@%s:%d/%s?serverVersion=%s&charset=utf8mb4',
            rawurlencode($this->databaseUser),
            rawurlencode($this->databasePassword),
            $host,
            $this->databasePort,
            rawurlencode($this->databaseName),
            rawurlencode($this->databaseVersion),
        );
    }

    /** @param array<string, mixed> $data */
    private static function string(array $data, string $field): string
    {
        $value = $data[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new \InvalidArgumentException(sprintf('Installation field "%s" must not be empty.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $data */
    private static function integer(array $data, string $field): int
    {
        $value = filter_var($data[$field] ?? null, FILTER_VALIDATE_INT);
        if (!is_int($value) || $value < 1 || $value > 65535) {
            throw new \InvalidArgumentException(sprintf('Installation field "%s" must be a valid port.', $field));
        }

        return $value;
    }
}
