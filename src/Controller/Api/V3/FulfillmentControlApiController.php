<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Fulfillment\Application\AdvancedPickingService;
use WebWMS\Fulfillment\Application\InternalTransportService;
use WebWMS\Inventory\Application\ConfirmPickTaskCommand;
use WebWMS\Inventory\Application\ConfirmPickTaskHandler;
use WebWMS\Inventory\Application\ConfirmReplenishmentCommand;
use WebWMS\Inventory\Application\ConfirmReplenishmentHandler;
use WebWMS\Inventory\Application\CreateReplenishmentOrderCommand;
use WebWMS\Inventory\Application\CreateReplenishmentOrderHandler;
use WebWMS\Inventory\Application\CreateReplenishmentPolicyCommand;
use WebWMS\Inventory\Application\CreateReplenishmentPolicyHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/fulfillment-control', name: 'api_v3_fulfillment_control_')]
final class FulfillmentControlApiController extends AbstractController
{
    public function __construct(
        private readonly AdvancedPickingService $picking,
        private readonly InternalTransportService $transport,
        private readonly ConfirmPickTaskHandler $confirmPickTask,
        private readonly CreateReplenishmentPolicyHandler $createReplenishmentPolicy,
        private readonly CreateReplenishmentOrderHandler $createReplenishmentOrder,
        private readonly ConfirmReplenishmentHandler $confirmReplenishment,
    ) {
    }

    #[Route('/replenishment-policies', name: 'replenishment_policy_create', methods: ['POST'])]
    #[IsGranted('fulfillment.replenishment.write')]
    public function createReplenishmentPolicy(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        ($this->createReplenishmentPolicy)(new CreateReplenishmentPolicyCommand($id, $user->tenantId(), $this->string($payload, 'warehouseId'), $this->string($payload, 'productId'), $this->string($payload, 'targetLocationId'), $this->string($payload, 'code'), $this->string($payload, 'sourceLocationPrefix'), $this->integer($payload, 'minimumQuantity', true), $this->integer($payload, 'targetQuantity'), $this->integer($payload, 'priority'), $user->actorId(), new DateTimeImmutable()));

        return $this->data(['id' => $id], Response::HTTP_CREATED);
    }

    #[Route('/replenishment-policies/{policyId}/orders', name: 'replenishment_order_create', methods: ['POST'])]
    #[IsGranted('fulfillment.replenishment.write')]
    public function createReplenishmentOrder(string $policyId): JsonResponse
    {
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        $result = ($this->createReplenishmentOrder)(new CreateReplenishmentOrderCommand($id, $user->tenantId(), $policyId, $user->actorId(), new DateTimeImmutable()));

        return $this->data(['id' => $id, 'status' => $result->status], Response::HTTP_CREATED);
    }

    #[Route('/replenishment-orders/{orderId}/completion', name: 'replenishment_complete', methods: ['POST'])]
    #[IsGranted('fulfillment.replenishment.execute')]
    public function completeReplenishment(string $orderId): JsonResponse
    {
        $user = $this->user();
        $result = ($this->confirmReplenishment)(new ConfirmReplenishmentCommand($orderId, Uuid::v7()->toRfc4122(), Uuid::v7()->toRfc4122(), Uuid::v7()->toRfc4122(), $user->tenantId(), $user->actorId(), new DateTimeImmutable()));

        return $this->data([
            'id' => $orderId,
            'status' => $result->status,
            'sourceLocationId' => $result->sourceLocationId,
            'targetLocationId' => $result->targetLocationId,
            'quantity' => $result->quantity,
            'destinationQuantity' => $result->destinationQuantity,
        ]);
    }

    #[Route('/picking', name: 'picking', methods: ['GET'])]
    #[IsGranted('fulfillment.pick.control')]
    public function picking(): JsonResponse
    {
        return $this->data($this->picking->workspace($this->user()->tenantId()));
    }

    #[Route('/pick-waves', name: 'wave_create', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.wave.write')]
    public function createWave(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $planned = $this->nullableString($payload, 'plannedStartAt');
        $id = $this->picking->createWave($user->tenantId(), $user->actorId(), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'strategy'), $this->string($payload, 'selectionType'), $this->nullableString($payload, 'selectionValue'), $this->integer($payload, 'priority'), $planned === null ? null : new DateTimeImmutable($planned), $this->strings($payload, 'pickListIds'), new DateTimeImmutable());

        return $this->data(['id' => $id], Response::HTTP_CREATED);
    }

    #[Route('/pick-waves/{waveId}/release', name: 'wave_release', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.wave.write')]
    public function releaseWave(string $waveId): JsonResponse
    {
        $user = $this->user();
        $this->picking->releaseWave($user->tenantId(), $user->actorId(), $waveId, new DateTimeImmutable());

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/pick-waves/{waveId}/pick-lists/{pickListId}/consolidation', name: 'wave_consolidate', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.wave.write')]
    public function consolidateWave(string $waveId, string $pickListId): JsonResponse
    {
        $user = $this->user();
        $this->picking->consolidate($user->tenantId(), $user->actorId(), $waveId, $pickListId, new DateTimeImmutable());

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/pick-lists/{pickListId}/optimize', name: 'optimize', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.optimize')]
    public function optimize(string $pickListId): JsonResponse
    {
        $user = $this->user();
        $this->picking->optimizeRoute($user->tenantId(), $user->actorId(), $pickListId, new DateTimeImmutable());

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/pick-tasks/{taskId}/scan', name: 'scan', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.execute')]
    public function scan(string $taskId, Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $result = $this->picking->validateScan($user->tenantId(), $user->actorId(), $taskId, $this->string($payload, 'location'), $this->string($payload, 'product'), $this->nullableString($payload, 'batch'), $this->nullableString($payload, 'serial'), $this->integer($payload, 'quantity'), new DateTimeImmutable());
        if (!$result['valid']) {
            return $this->data($result, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $confirmation = ($this->confirmPickTask)(new ConfirmPickTaskCommand($taskId, $user->tenantId(), 'picked', Uuid::v7()->toRfc4122(), 'Scannerbestätigt', $user->actorId(), new DateTimeImmutable()));

        return $this->data($result + ['taskStatus' => $confirmation->taskStatus, 'pickListStatus' => $confirmation->pickListStatus]);
    }

    #[Route('/transport', name: 'transport', methods: ['GET'])]
    #[IsGranted('fulfillment.transport.read')]
    public function transport(): JsonResponse
    {
        return $this->data($this->transport->workspace($this->user()->tenantId()));
    }

    #[Route('/transport-orders', name: 'transport_create', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.write')]
    public function createTransport(Request $request): JsonResponse
    {
        $user = $this->user();
        $id = $this->transport->createOrder($user->tenantId(), $user->actorId(), $this->payload($request), new DateTimeImmutable());

        return $this->data(['id' => $id], Response::HTTP_CREATED);
    }

    #[Route('/transport-orders/from-rule/{triggerType}', name: 'transport_rule_create', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.write')]
    public function createTransportFromRule(string $triggerType, Request $request): JsonResponse
    {
        $user = $this->user();
        $id = $this->transport->createOrderFromRule($user->tenantId(), $user->actorId(), $triggerType, $this->payload($request), new DateTimeImmutable());

        return $this->data(['id' => $id], Response::HTTP_CREATED);
    }

    #[Route('/transport-orders/{orderId}/{transition}', name: 'transport_transition', requirements: ['transition' => 'assign|start|complete'], methods: ['POST'])]
    public function transition(string $orderId, string $transition, Request $request): JsonResponse
    {
        $user = $this->user();
        $permission = $transition === 'assign' ? 'fulfillment.transport.assign' : 'fulfillment.transport.execute';
        if (!$user->hasPermission($permission)) {
            throw $this->createAccessDeniedException();
        }
        $now = new DateTimeImmutable();
        if ($transition === 'assign') {
            $this->transport->assign($user->tenantId(), $user->actorId(), $orderId, $this->string($this->payload($request), 'forkliftId'), $now);
        } elseif ($transition === 'start') {
            $this->transport->start($user->tenantId(), $user->actorId(), $orderId, $now);
        } else {
            $this->transport->complete($user->tenantId(), $user->actorId(), $orderId, $now);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/transport-resources/{resource}', name: 'resource_create', requirements: ['resource' => 'forklift|rule|station|milk_run'], methods: ['POST'])]
    #[IsGranted('fulfillment.transport.configure')]
    public function createResource(string $resource, Request $request): JsonResponse
    {
        $user = $this->user();
        $id = $this->transport->createResource($user->tenantId(), $user->actorId(), $resource, $this->payload($request), new DateTimeImmutable());

        return $this->data(['id' => $id], Response::HTTP_CREATED);
    }

    #[Route('/milk-runs/{milkRunId}/stops', name: 'milk_run_stop_create', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.configure')]
    public function addMilkRunStop(string $milkRunId, Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $id = $this->transport->addMilkRunStop($user->tenantId(), $user->actorId(), $milkRunId, $this->string($payload, 'stationId'), $this->integer($payload, 'sequenceNumber'), $this->integer($payload, 'dwellMinutes'), new DateTimeImmutable());

        return $this->data(['id' => $id], Response::HTTP_CREATED);
    }

    #[Route('/milk-runs/{milkRunId}/dispatch', name: 'milk_run_dispatch', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.execute')]
    public function dispatchMilkRun(string $milkRunId): JsonResponse
    {
        $user = $this->user();
        $orderIds = $this->transport->dispatchMilkRun($user->tenantId(), $user->actorId(), $milkRunId, new DateTimeImmutable());

        return $this->data(['transportOrderIds' => $orderIds], Response::HTTP_CREATED);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }

    /** @return array<string, mixed> */
    private function payload(Request $request): array
    {
        return $request->toArray();
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
    private function nullableString(array $payload, string $field): ?string
    {
        $value = $payload[$field] ?? null;

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    /** @param array<string, mixed> $payload */
    private function integer(array $payload, string $field, bool $allowZero = false): int
    {
        $value = filter_var($payload[$field] ?? null, FILTER_VALIDATE_INT);
        if (!is_int($value) || $value < ($allowZero ? 0 : 1)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a positive integer.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload @return list<string> */
    private function strings(array $payload, string $field): array
    {
        $values = $payload[$field] ?? null;
        if (!is_array($values)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be an array.', $field));
        }

        return array_values(array_filter($values, 'is_string'));
    }

    /** @param array<string, mixed> $payload */
    private function data(array $payload, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $payload], $status);
    }
}
