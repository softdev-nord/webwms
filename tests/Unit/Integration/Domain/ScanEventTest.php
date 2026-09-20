<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\ScanEvent;

final class ScanEventTest extends TestCase
{
    public function testItCapturesARejectedProcessScan(): void
    {
        $event = $this->event(ScanEvent::STATUS_REJECTED);

        self::assertSame('picking', $event->processType);
        self::assertSame(ScanEvent::STATUS_REJECTED, $event->status);
    }

    public function testItRejectsAnUnsupportedScanType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ScanEvent('id', 'tenant', 'device', 'unknown', 'value', 'picking', 'PICK-1', 'request', ScanEvent::STATUS_ACCEPTED, null, 'user', new DateTimeImmutable());
    }

    private function event(string $status): ScanEvent
    {
        return new ScanEvent('id', 'tenant', 'device', 'product', 'SKU-1', 'picking', 'PICK-1', 'request', $status, 'Wrong product', 'user', new DateTimeImmutable());
    }
}
