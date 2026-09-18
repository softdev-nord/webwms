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
use WebWMS\Inventory\Application\AllocateStockCommand;
use WebWMS\Inventory\Application\AllocateStockHandler;
use WebWMS\Inventory\Application\CreateOutboundOrderCommand;
use WebWMS\Inventory\Application\CreateOutboundOrderHandler;
use WebWMS\Inventory\Application\ReleaseOutboundOrderCommand;
use WebWMS\Inventory\Application\ReleaseOutboundOrderHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_outbound_')]
final class OutboundOrderApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreateOutboundOrderHandler $createOrder,
        private readonly ReleaseOutboundOrderHandler $releaseOrder,
        private readonly AllocateStockHandler $allocateStock
    ) {
    }

    #[Route('/orders', name: 'create', methods: ['POST'])]
    #[IsGranted('outbound.order.write')]
    public function create(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $items = $this->items($payload['items'] ?? null);
        $orderId = Uuid::v7()->toRfc4122();
        $result = ($this->createOrder)(new CreateOutboundOrderCommand(
            $orderId,
            $this->apiUser()->tenantId(),
            $this->string($payload, 'orderNumber'),
            $this->string($payload, 'customerReference'),
            $items,
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $orderId,
            'status' => $result->status,
            'lineCount' => $result->lineCount,
            'items' => $items,
        ], Response::HTTP_CREATED);
    }

    #[Route('/orders/{orderId}', name: 'get', methods: ['GET'])]
    #[IsGranted('outbound.order.read')]
    public function getOrder(string $orderId): JsonResponse
    {
        $order = $this->queries->outboundOrder($this->apiUser()->tenantId(), $orderId);
        if ($order === null) {
            throw $this->createNotFoundException('The outbound order does not exist.');
        }

        return $this->data($order);
    }

    #[Route('/orders/{orderId}/release', name: 'release', methods: ['POST'])]
    #[IsGranted('outbound.order.release')]
    public function release(string $orderId): JsonResponse
    {
        $order = $this->queries->outboundOrder($this->apiUser()->tenantId(), $orderId);
        if ($order === null) {
            throw $this->createNotFoundException('The outbound order does not exist.');
        }
        $reservationIds = [];
        foreach ($order['items'] as $item) {
            if (!is_array($item) || !is_string($item['id'] ?? null)) {
                throw new \LogicException('The outbound order item projection is invalid.');
            }
            $reservationIds[$item['id']] = Uuid::v7()->toRfc4122();
        }
        $result = ($this->releaseOrder)(new ReleaseOutboundOrderCommand(
            $orderId,
            $this->apiUser()->tenantId(),
            $reservationIds,
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $orderId,
            'status' => $result->status,
            'reservationCount' => $result->reservationCount,
            'reservationIdsByItem' => $reservationIds,
        ]);
    }

    #[Route('/reservations/{reservationId}', name: 'reservation', methods: ['GET'])]
    #[IsGranted('inventory.allocation.read')]
    public function getReservation(string $reservationId): JsonResponse
    {
        $reservation = $this->queries->reservation($this->apiUser()->tenantId(), $reservationId);
        if ($reservation === null) {
            throw $this->createNotFoundException('The stock reservation does not exist.');
        }

        return $this->data($reservation);
    }

    #[Route('/reservations/{reservationId}/allocations', name: 'allocate', methods: ['POST'])]
    #[IsGranted('inventory.allocation.write')]
    public function allocate(string $reservationId, Request $request): JsonResponse
    {
        $reservation = $this->queries->reservation($this->apiUser()->tenantId(), $reservationId);
        if ($reservation === null || !is_string($reservation['product_id'] ?? null)) {
            throw $this->createNotFoundException('The stock reservation does not exist.');
        }
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $allocationId = Uuid::v7()->toRfc4122();
        $result = ($this->allocateStock)(new AllocateStockCommand(
            $allocationId,
            $reservationId,
            $this->apiUser()->tenantId(),
            $reservation['product_id'],
            $this->string($payload, 'locationId'),
            $this->positiveInt($payload, 'quantity'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
            $this->optionalString($payload, 'status') ?? 'available',
            $this->optionalString($payload, 'batchNumber'),
            $this->optionalString($payload, 'serialNumber'),
            $this->optionalDate($payload, 'expiresAt'),
        ));

        return $this->data([
            'id' => $allocationId,
            'reservationId' => $reservationId,
            'allocatedQuantity' => $result->allocatedQuantity,
            'remainingQuantity' => $result->remainingQuantity,
            'availableQuantity' => $result->availableQuantity,
        ], Response::HTTP_CREATED);
    }

    private function apiUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
        }

        return $user;
    }

    /** @return list<array{id: string, productId: string, quantity: int}> */
    private function items(mixed $value): array
    {
        if (!is_array($value) || $value === []) {
            throw new \InvalidArgumentException('Field "items" must be a non-empty array.');
        }
        $items = [];
        foreach ($value as $item) {
            if (!is_array($item)) {
                throw new \InvalidArgumentException('Every order item must be an object.');
            }
            $items[] = [
                'id' => Uuid::v7()->toRfc4122(),
                'productId' => $this->string($item, 'productId'),
                'quantity' => $this->positiveInt($item, 'quantity'),
            ];
        }

        return $items;
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
    private function optionalString(array $payload, string $field): ?string
    {
        $value = $payload[$field] ?? null;
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a string.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $payload */
    private function positiveInt(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value) || $value <= 0) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a positive integer.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function optionalDate(array $payload, string $field): ?DateTimeImmutable
    {
        $value = $this->optionalString($payload, $field);

        return $value === null ? null : new DateTimeImmutable($value);
    }

    /** @param array<string, mixed> $payload */
    private function data(array $payload, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $payload], $status);
    }
}
