<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\CarrierConnectionRepository;
use WebWMS\Integration\Domain\CarrierTransport;

final readonly class CarrierGateway
{
    public function __construct(
        private CarrierConnectionRepository $connections,
        private CarrierTransport $transport
    ) {
    }

    /** @return list<array{code: string, name: string}> */
    public function products(string $tenantId, string $carrierCode): array
    {
        return $this->transport->products($this->connections->active($tenantId, $carrierCode));
    }

    /** @param array<string, mixed> $shipment @return array{trackingNumber: string, labelReference: string} */
    public function createLabel(string $tenantId, string $carrierCode, array $shipment, string $requestId, string $actorId, DateTimeImmutable $at): array
    {
        $shipmentId = $this->string($shipment, 'id');
        $existing = $this->connections->successfulRequest($tenantId, $requestId, 'label', $shipmentId);
        if ($existing !== null) {
            return ['trackingNumber' => $this->string($existing, 'trackingNumber'), 'labelReference' => $this->string($existing, 'labelReference')];
        }
        $connection = $this->connections->active($tenantId, $carrierCode);
        $response = $this->transport->createLabel($connection, $shipment, $requestId);
        $this->connections->recordRequest(Uuid::v7()->toRfc4122(), $tenantId, $connection->id, 'label', $requestId, 'shipment', $shipmentId, 'succeeded', $response, $actorId, $at);

        return $response;
    }

    /** @param array<string, mixed> $manifest @return array{handoverReference: string} */
    public function handoverManifest(string $tenantId, string $carrierCode, array $manifest, string $requestId, string $actorId, DateTimeImmutable $at): array
    {
        $manifestId = $this->string($manifest, 'id');
        $existing = $this->connections->successfulRequest($tenantId, $requestId, 'manifest', $manifestId);
        if ($existing !== null) {
            return ['handoverReference' => $this->string($existing, 'handoverReference')];
        }
        $connection = $this->connections->active($tenantId, $carrierCode);
        $response = $this->transport->handoverManifest($connection, $manifest, $requestId);
        $this->connections->recordRequest(Uuid::v7()->toRfc4122(), $tenantId, $connection->id, 'manifest', $requestId, 'loading_manifest', $manifestId, 'succeeded', $response, $actorId, $at);

        return $response;
    }

    /** @return array{status: string, occurredAt: string, description?: string} */
    public function tracking(string $tenantId, string $carrierCode, string $shipmentId, string $trackingNumber, string $requestId, string $actorId, DateTimeImmutable $at): array
    {
        $existing = $this->connections->successfulRequest($tenantId, $requestId, 'tracking', $shipmentId);
        if ($existing !== null) {
            return $this->trackingResponse($existing);
        }
        $connection = $this->connections->active($tenantId, $carrierCode);
        $response = $this->transport->tracking($connection, $trackingNumber);
        $this->connections->recordRequest(Uuid::v7()->toRfc4122(), $tenantId, $connection->id, 'tracking', $requestId, 'shipment', $shipmentId, 'succeeded', $response, $actorId, $at);

        return $response;
    }

    /** @param array<string, mixed> $payload */
    private function string(array $payload, string $field): string
    {
        if (!is_string($payload[$field] ?? null)) {
            throw new \LogicException(sprintf('Persisted carrier response field "%s" is invalid.', $field));
        }

        return $payload[$field];
    }

    /** @param array<string, mixed> $payload @return array{status: string, occurredAt: string, description?: string} */
    private function trackingResponse(array $payload): array
    {
        $response = ['status' => $this->string($payload, 'status'), 'occurredAt' => $this->string($payload, 'occurredAt')];
        if (is_string($payload['description'] ?? null)) {
            $response['description'] = $payload['description'];
        }

        return $response;
    }
}
