<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\WcsConnection;

final class WcsConnectionTest extends TestCase
{
    public function testItAcceptsAMaterialFlowController(): void
    {
        $connection = new WcsConnection('id', 'tenant', 'MFR-01', 'Materialfluss', 'mfr', 'https://wcs.example/commands', 'WCS_TOKEN', true, 'user', new DateTimeImmutable());

        self::assertSame('mfr', $connection->systemType);
    }

    public function testItRejectsAnInsecureEndpoint(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new WcsConnection('id', 'tenant', 'WCS-01', 'WCS', 'wcs', 'http://wcs.example/commands', 'WCS_TOKEN', true, 'user', new DateTimeImmutable());
    }
}
