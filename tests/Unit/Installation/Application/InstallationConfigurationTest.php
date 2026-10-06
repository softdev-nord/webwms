<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Installation\Application;

use PHPUnit\Framework\TestCase;
use WebWMS\Installation\Application\InstallationConfiguration;

final class InstallationConfigurationTest extends TestCase
{
    public function testItBuildsAnEncodedMysqlConnectionUrl(): void
    {
        $configuration = InstallationConfiguration::fromArray([
            'database_host' => 'database', 'database_port' => 3306, 'database_name' => 'webWMS',
            'database_user' => 'installer@example', 'database_password' => 'secret:/value',
            'database_version' => '8.0', 'demo_data' => '1', 'tenant_name' => 'Example',
            'site_code' => 'MAIN', 'site_name' => 'Main warehouse', 'site_timezone' => 'Europe/Berlin',
            'admin_name' => 'Administrator', 'admin_email' => 'admin@example.test',
            'admin_password' => 'a-secure-password',
        ]);

        self::assertTrue($configuration->demoData);
        self::assertSame(
            'mysql://installer%40example:secret%3A%2Fvalue@database:3306/webWMS?serverVersion=8.0&charset=utf8mb4',
            $configuration->databaseUrl(),
        );
    }

    public function testItRejectsAnInvalidPort(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        InstallationConfiguration::fromArray([
            'database_host' => 'database', 'database_port' => 0, 'database_name' => 'webWMS',
            'database_user' => 'installer', 'database_password' => '', 'database_version' => '8.0',
            'tenant_name' => 'Example', 'site_code' => 'MAIN', 'site_name' => 'Main',
            'site_timezone' => 'UTC', 'admin_name' => 'Admin', 'admin_email' => 'admin@example.test',
            'admin_password' => 'a-secure-password',
        ]);
    }
}
