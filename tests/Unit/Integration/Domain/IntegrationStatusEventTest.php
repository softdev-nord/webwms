<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\IntegrationStatusEvent;

final class IntegrationStatusEventTest extends TestCase
{
    public function testItRetainsAStatusPayload(): void
    {
        $event = new IntegrationStatusEvent(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf501',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            'fulfillment.shipment.dispatched',
            'shipment',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf450',
            ['status' => 'dispatched', 'trackingNumber' => 'TRACK-1'],
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf302',
            new DateTimeImmutable(),
        );

        self::assertSame('dispatched', $event->payload['status']);
    }

    public function testItRejectsAnEmptyPayload(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new IntegrationStatusEvent(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf501',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            'fulfillment.shipment.dispatched',
            'shipment',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf450',
            [],
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf302',
            new DateTimeImmutable(),
        );
    }
}
