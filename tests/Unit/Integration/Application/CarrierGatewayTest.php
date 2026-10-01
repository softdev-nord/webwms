<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\CarrierGateway;
use WebWMS\Integration\Domain\CarrierConnection;
use WebWMS\Integration\Domain\CarrierConnectionRepository;
use WebWMS\Integration\Domain\CarrierTransport;

class CarrierGatewayTest extends TestCase
{
    public function testItReturnsAnExistingIdempotentLabelWithoutCallingTheCarrier(): void
    {
        $repository = $this->createMock(CarrierConnectionRepository::class);
        $repository->expects($this->once())->method('successfulRequest')
            ->with('tenant-id', 'request-id', 'label', 'shipment-id')
            ->willReturn(['trackingNumber' => 'TRACK-1', 'labelReference' => 'LABEL-1']);
        $repository->expects($this->never())->method('active');
        $transport = $this->createMock(CarrierTransport::class);
        $transport->expects($this->never())->method('createLabel');

        $result = new CarrierGateway($repository, $transport)->createLabel(
            'tenant-id',
            'DHL',
            ['id' => 'shipment-id'],
            'request-id',
            'user-id',
            new DateTimeImmutable(),
        );

        self::assertSame(['trackingNumber' => 'TRACK-1', 'labelReference' => 'LABEL-1'], $result);
    }

    public function testItHandsACompletedManifestToTheConfiguredCarrier(): void
    {
        $connection = new CarrierConnection(
            'connection-id',
            'tenant-id',
            'DHL Production',
            'DHL',
            'https://carrier.example.com',
            'DHL_TOKEN',
            true,
            'user-id',
            new DateTimeImmutable(),
        );
        $repository = $this->createMock(CarrierConnectionRepository::class);
        $repository->method('successfulRequest')->willReturn(null);
        $repository->expects($this->once())->method('active')->with('tenant-id', 'DHL')->willReturn($connection);
        $repository->expects($this->once())->method('recordRequest');
        $transport = $this->createMock(CarrierTransport::class);
        $transport->expects($this->once())->method('handoverManifest')
            ->with($connection, ['id' => 'manifest-id'], 'request-id')
            ->willReturn(['handoverReference' => 'HANDOVER-1']);

        $result = new CarrierGateway($repository, $transport)->handoverManifest(
            'tenant-id',
            'DHL',
            ['id' => 'manifest-id'],
            'request-id',
            'user-id',
            new DateTimeImmutable(),
        );

        self::assertSame(['handoverReference' => 'HANDOVER-1'], $result);
    }
}
