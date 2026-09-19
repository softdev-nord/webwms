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
use WebWMS\Inventory\Application\CreateShipmentCommand;
use WebWMS\Inventory\Application\CreateShipmentHandler;
use WebWMS\Inventory\Application\DispatchShipmentCommand;
use WebWMS\Inventory\Application\DispatchShipmentHandler;
use WebWMS\Inventory\Application\RegisterShipmentLabelCommand;
use WebWMS\Inventory\Application\RegisterShipmentLabelHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_shipping_')]
final class ShippingApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreateShipmentHandler $createShipment,
        private readonly RegisterShipmentLabelHandler $registerLabel,
        private readonly DispatchShipmentHandler $dispatchShipment
    ) {
    }

    #[Route('/packing-orders/{packingOrderId}/shipments', name: 'create', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.write')]
    public function create(string $packingOrderId, Request $request): JsonResponse
    {
        if ($this->queries->packingOrder($this->apiUser()->tenantId(), $packingOrderId) === null) {
            throw $this->createNotFoundException('The packing order does not exist.');
        }
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $shipmentId = Uuid::v7()->toRfc4122();
        ($this->createShipment)(new CreateShipmentCommand(
            $shipmentId,
            $this->apiUser()->tenantId(),
            $packingOrderId,
            $this->string($payload, 'shipmentNumber'),
            $this->string($payload, 'carrier'),
            $this->string($payload, 'service'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data($this->requiredShipment($shipmentId), Response::HTTP_CREATED);
    }

    #[Route('/shipments/{shipmentId}', name: 'get', methods: ['GET'])]
    #[IsGranted('fulfillment.ship.read')]
    public function getShipment(string $shipmentId): JsonResponse
    {
        return $this->data($this->requiredShipment($shipmentId));
    }

    #[Route('/shipments/{shipmentId}/label', name: 'label', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.label')]
    public function registerLabel(string $shipmentId, Request $request): JsonResponse
    {
        $this->requiredShipment($shipmentId);
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $result = ($this->registerLabel)(new RegisterShipmentLabelCommand(
            $shipmentId,
            $this->apiUser()->tenantId(),
            $this->string($payload, 'trackingNumber'),
            $this->string($payload, 'labelReference'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $shipmentId,
            'status' => $result->status,
            'trackingNumber' => $result->trackingNumber,
        ]);
    }

    #[Route('/shipments/{shipmentId}/dispatch', name: 'dispatch', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.dispatch')]
    public function dispatch(string $shipmentId, Request $request): JsonResponse
    {
        $this->requiredShipment($shipmentId);
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $result = ($this->dispatchShipment)(new DispatchShipmentCommand(
            $shipmentId,
            $this->apiUser()->tenantId(),
            $this->string($payload, 'handoverReference'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $shipmentId,
            'status' => $result->status,
            'trackingNumber' => $result->trackingNumber,
        ]);
    }

    private function apiUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
        }

        return $user;
    }

    /** @return array<string, mixed> */
    private function requiredShipment(string $shipmentId): array
    {
        $shipment = $this->queries->shipment($this->apiUser()->tenantId(), $shipmentId);
        if ($shipment === null) {
            throw $this->createNotFoundException('The shipment does not exist.');
        }

        return $shipment;
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
    private function data(array $payload, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $payload], $status);
    }
}
