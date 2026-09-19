<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Integration\Application\CarrierGateway;
use WebWMS\Integration\Application\ChangeCarrierConnectionStatusCommand;
use WebWMS\Integration\Application\ChangeCarrierConnectionStatusHandler;
use WebWMS\Integration\Application\RegisterCarrierConnectionCommand;
use WebWMS\Integration\Application\RegisterCarrierConnectionHandler;
use WebWMS\Inventory\Application\RegisterShipmentLabelCommand;
use WebWMS\Inventory\Application\RegisterShipmentLabelHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_carrier_')]
final class CarrierApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly RegisterCarrierConnectionHandler $register,
        private readonly ChangeCarrierConnectionStatusHandler $changeStatus,
        private readonly CarrierGateway $gateway,
        private readonly RegisterShipmentLabelHandler $registerLabel
    ) {
    }

    #[Route('/carrier-connections', name: 'connections', methods: ['GET'])]
    #[IsGranted('integration.carrier_connection.read')]
    public function connections(): JsonResponse
    {
        $data = $this->queries->carrierConnections($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/carrier-connections', name: 'create_connection', methods: ['POST'])]
    #[IsGranted('integration.carrier_connection.write')]
    public function createConnection(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $connection = ($this->register)(new RegisterCarrierConnectionCommand(
            Uuid::v7()->toRfc4122(), $this->user()->tenantId(), $this->string($payload, 'name'),
            $this->string($payload, 'carrierCode'), $this->string($payload, 'endpointUrl'),
            $this->string($payload, 'credentialEnv'), $this->boolean($payload, 'active', true),
            $this->user()->actorId(), new DateTimeImmutable(),
        ));

        return new JsonResponse(['data' => ['id' => $connection->id, 'carrierCode' => $connection->carrierCode, 'active' => $connection->active]], Response::HTTP_CREATED);
    }

    #[Route('/carrier-connections/{connectionId}/status', name: 'connection_status', methods: ['PATCH'])]
    #[IsGranted('integration.carrier_connection.write')]
    public function connectionStatus(string $connectionId, Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        ($this->changeStatus)(new ChangeCarrierConnectionStatusCommand($connectionId, $this->user()->tenantId(), $this->boolean($payload, 'active'), $this->user()->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => $this->queries->carrierConnection($this->user()->tenantId(), $connectionId)]);
    }

    #[Route('/carriers/{carrierCode}/products', name: 'products', methods: ['GET'])]
    #[IsGranted('integration.carrier.read')]
    public function products(string $carrierCode): JsonResponse
    {
        return new JsonResponse(['data' => $this->gateway->products($this->user()->tenantId(), $carrierCode)]);
    }

    #[Route('/shipments/{shipmentId}/carrier-label', name: 'label', methods: ['POST'])]
    #[IsGranted('integration.carrier.execute')]
    public function label(string $shipmentId, Request $request): JsonResponse
    {
        $shipment = $this->shipment($shipmentId);
        if (($shipment['status'] ?? null) !== 'prepared') {
            throw new \DomainException('Only a prepared shipment can receive a carrier label.');
        }
        $payload = $this->payload($request);
        $now = new DateTimeImmutable();
        $result = $this->gateway->createLabel($this->user()->tenantId(), $this->string($shipment, 'carrier'), $shipment, $this->string($payload, 'requestId'), $this->user()->actorId(), $now);
        ($this->registerLabel)(new RegisterShipmentLabelCommand($shipmentId, $this->user()->tenantId(), $result['trackingNumber'], $result['labelReference'], $this->user()->actorId(), $now));

        return new JsonResponse(['data' => $this->shipment($shipmentId)]);
    }

    #[Route('/shipments/{shipmentId}/carrier-tracking', name: 'tracking', methods: ['POST'])]
    #[IsGranted('integration.carrier.execute')]
    public function tracking(string $shipmentId, Request $request): JsonResponse
    {
        $shipment = $this->shipment($shipmentId);
        $tracking = $shipment['tracking_number'] ?? null;
        if (!is_string($tracking) || $tracking === '') {
            throw new \DomainException('The shipment has no tracking number.');
        }
        $payload = $this->payload($request);
        $data = $this->gateway->tracking($this->user()->tenantId(), $this->string($shipment, 'carrier'), $shipmentId, $tracking, $this->string($payload, 'requestId'), $this->user()->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['shipmentId' => $shipmentId, 'trackingNumber' => $tracking] + $data]);
    }

    #[Route('/loading-manifests/{manifestId}/carrier-handover', name: 'manifest', methods: ['POST'])]
    #[IsGranted('integration.carrier.execute')]
    public function manifest(string $manifestId, Request $request): JsonResponse
    {
        $manifest = $this->queries->loadingManifest($this->user()->tenantId(), $manifestId);
        if ($manifest === null || ($manifest['status'] ?? null) !== 'completed') {
            throw new \DomainException('Only a completed loading manifest can be handed over.');
        }
        $payload = $this->payload($request);
        $result = $this->gateway->handoverManifest($this->user()->tenantId(), $this->string($payload, 'carrierCode'), $manifest, $this->string($payload, 'requestId'), $this->user()->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['manifestId' => $manifestId] + $result]);
    }

    /** @return array<string, mixed> */
    private function shipment(string $id): array
    {
        $shipment = $this->queries->shipment($this->user()->tenantId(), $id);
        if ($shipment === null) {
            throw $this->createNotFoundException('The shipment does not exist.');
        }

        return $shipment;
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
        }

        return $user;
    }

    /** @return array<string, mixed> */
    private function payload(Request $request): array
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        return $payload;
    }

    /** @param array<string, mixed> $payload */
    private function string(array $payload, string $field): string
    {
        $value = $payload[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a non-empty string.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $payload */
    private function boolean(array $payload, string $field, ?bool $default = null): bool
    {
        $value = $payload[$field] ?? $default;
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be boolean.', $field));
        }

        return $value;
    }
}
