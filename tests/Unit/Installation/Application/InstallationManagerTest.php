<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Installation\Application;

use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\PasswordHasher;
use WebWMS\Installation\Application\InstallationManager;
use WebWMS\Installation\Application\V2DemoDataInstaller;
use WebWMS\Installation\Application\V3DemoConfigurationInstaller;

final class InstallationManagerTest extends TestCase
{
    public function testDatabaseDefaultsAreExtractedFromTheEffectiveDatabaseUrl(): void
    {
        $previous = $_SERVER['DATABASE_URL'] ?? null;
        $_SERVER['DATABASE_URL'] = 'mysql://installer%40webwms:p%40ss%3Aword@database:3307/webWMS?serverVersion=10.11.2-MariaDB&charset=utf8mb4';

        try {
            $manager = new InstallationManager(
                __DIR__,
                new class implements PasswordHasher {
                    public function hash(string $plainPassword): string
                    {
                        return $plainPassword;
                    }
                },
                new V2DemoDataInstaller(__DIR__),
                new V3DemoConfigurationInstaller(),
            );

            self::assertSame([
                'database_host' => 'database',
                'database_port' => 3307,
                'database_name' => 'webWMS',
                'database_user' => 'installer@webwms',
                'database_password' => 'p@ss:word',
                'database_version' => '10.11.2-MariaDB',
            ], $manager->databaseDefaults());
        } finally {
            if ($previous === null) {
                unset($_SERVER['DATABASE_URL']);
            } else {
                $_SERVER['DATABASE_URL'] = $previous;
            }
        }
    }
}
