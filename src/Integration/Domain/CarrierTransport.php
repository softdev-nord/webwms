<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

interface CarrierTransport
{
    /** @return list<array{code: string, name: string}> */
    public function products(CarrierConnection $connection): array;

    /** @param array<string, mixed> $shipment @return array{trackingNumber: string, labelReference: string} */
    public function createLabel(CarrierConnection $connection, array $shipment, string $idempotencyKey): array;

    /** @param array<string, mixed> $manifest @return array{handoverReference: string} */
    public function handoverManifest(CarrierConnection $connection, array $manifest, string $idempotencyKey): array;

    /** @return array{status: string, occurredAt: string, description?: string} */
    public function tracking(CarrierConnection $connection, string $trackingNumber): array;
}
