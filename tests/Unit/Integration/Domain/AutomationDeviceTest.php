<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\AutomationDevice;

final class AutomationDeviceTest extends TestCase
{
    public function testItAcceptsAStorageLiftWithCredentialReference(): void
    {
        $device = new AutomationDevice(
            'id',
            'tenant',
            'LIFT-01',
            'Lagerlift',
            'storage_lift',
            'https://lift.example/commands',
            'LIFT_TOKEN',
            true,
            'user',
            new DateTimeImmutable(),
        );

        self::assertSame('storage_lift', $device->type);
        self::assertSame('LIFT_TOKEN', $device->credentialEnv);
    }

    public function testItRejectsAnInsecureEndpoint(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new AutomationDevice(
            'id',
            'tenant',
            'LIFT-01',
            'Lagerlift',
            'storage_lift',
            'http://lift.example/commands',
            'LIFT_TOKEN',
            true,
            'user',
            new DateTimeImmutable(),
        );
    }
}
