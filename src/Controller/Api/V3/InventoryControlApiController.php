<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inventory\Application\ApproveInventoryCountCommand;
use WebWMS\Inventory\Application\ApproveInventoryCountHandler;
use WebWMS\Inventory\Application\CreateCycleCountPlanCommand;
use WebWMS\Inventory\Application\CreateCycleCountPlanHandler;
use WebWMS\Inventory\Application\CreateInventoryCountCommand;
use WebWMS\Inventory\Application\CreateInventoryCountHandler;
use WebWMS\Inventory\Application\InventoryControlService;
use WebWMS\Inventory\Application\RecordInventoryCountCommand;
use WebWMS\Inventory\Application\RecordInventoryCountHandler;
use WebWMS\Inventory\Application\StartDueCycleCountCommand;
use WebWMS\Inventory\Application\StartDueCycleCountHandler;
use WebWMS\Inventory\Application\SubmitInventoryCountCommand;
use WebWMS\Inventory\Application\SubmitInventoryCountHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/inventory/control', name: 'api_v3_inventory_control_')]
final class InventoryControlApiController extends AbstractController
{
    public function __construct(
        private readonly InventoryControlService $control,
        private readonly CreateInventoryCountHandler $createCount,
        private readonly CreateCycleCountPlanHandler $createCyclePlan,
        private readonly StartDueCycleCountHandler $startCycleCount,
        private readonly RecordInventoryCountHandler $recordCount,
        private readonly SubmitInventoryCountHandler $submitCount,
        private readonly ApproveInventoryCountHandler $approveCount
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('inventory.control.read')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => $this->control->workspace($this->user()->tenantId())]);
    }

    #[Route('/hazard-classes', name: 'hazard_class', methods: ['POST'])]
    #[IsGranted('inventory.hazard.write')]
    public function hazardClass(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->control->createHazardClass($user->tenantId(), $user->actorId(), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'unClass'), new DateTimeImmutable());

        return $this->created($id);
    }

    #[Route('/hazardous-materials', name: 'hazardous_material', methods: ['POST'])]
    #[IsGranted('inventory.hazard.write')]
    public function hazardousMaterial(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->control->classifyMaterial($user->tenantId(), $user->actorId(), $this->string($payload, 'productId'), $this->string($payload, 'hazardClassId'), $this->string($payload, 'unNumber'), $this->optionalString($payload, 'packingGroup'), $this->string($payload, 'description'), new DateTimeImmutable());

        return $this->created($id);
    }

    #[Route('/storage-restrictions', name: 'restriction', methods: ['POST'])]
    #[IsGranted('inventory.hazard.write')]
    public function restriction(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->control->restrictStorage($user->tenantId(), $user->actorId(), $this->string($payload, 'hazardClassId'), $this->string($payload, 'locationPrefix'), $this->boolean($payload, 'allowed'), $this->optionalInteger($payload, 'maxQuantity'), new DateTimeImmutable());

        return $this->created($id);
    }

    #[Route('/boms', name: 'bom', methods: ['POST'])]
    #[IsGranted('inventory.bom.write')]
    public function bom(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $rawItems = $payload['items'] ?? null;
        if (!is_array($rawItems)) {
            throw new \InvalidArgumentException('Field "items" must be an array.');
        }
        $items = [];
        foreach ($rawItems as $item) {
            if (!is_array($item)) {
                throw new \InvalidArgumentException('Every item must be an object.');
            }
            $items[] = ['productId' => $this->string($item, 'productId'), 'quantity' => $this->integer($item, 'quantity')];
        }
        $user = $this->user();
        $id = $this->control->createBom($user->tenantId(), $user->actorId(), $this->string($payload, 'productId'), $this->string($payload, 'code'), $this->string($payload, 'version'), $items, new DateTimeImmutable());

        return $this->created($id);
    }

    #[Route('/requirements', name: 'requirement', methods: ['POST'])]
    #[IsGranted('inventory.bom.execute')]
    public function requirement(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->control->planRequirement($user->tenantId(), $user->actorId(), $this->string($payload, 'bomId'), $this->string($payload, 'reference'), $this->integer($payload, 'productionQuantity'), new DateTimeImmutable());

        return $this->created($id);
    }

    #[Route('/load-carrier-movements', name: 'load_carrier', methods: ['POST'])]
    #[IsGranted('inventory.load_carrier.write')]
    public function loadCarrier(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->control->bookLoadCarrier($user->tenantId(), $user->actorId(), $this->string($payload, 'partnerCode'), $this->string($payload, 'carrierType'), $this->integer($payload, 'quantity'), $this->string($payload, 'reference'), $this->optionalString($payload, 'note') ?? '', new DateTimeImmutable());

        return $this->created($id);
    }

    #[Route('/counts', name: 'count', methods: ['POST'])]
    #[IsGranted('inventory.count.write')]
    public function count(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        $result = ($this->createCount)(new CreateInventoryCountCommand($id, $user->tenantId(), $this->string($payload, 'warehouseId'), $this->string($payload, 'code'), $this->optionalString($payload, 'locationPrefix') ?? '', $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => ['id' => $id, 'status' => $result->status, 'lineCount' => $result->lineCount]], JsonResponse::HTTP_CREATED);
    }

    #[Route('/cycle-plans', name: 'cycle_plan', methods: ['POST'])]
    #[IsGranted('inventory.count.write')]
    public function cyclePlan(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        ($this->createCyclePlan)(new CreateCycleCountPlanCommand($id, $user->tenantId(), $this->string($payload, 'warehouseId'), $this->string($payload, 'code'), $this->optionalString($payload, 'locationPrefix') ?? '', $this->integer($payload, 'intervalDays'), new DateTimeImmutable($this->string($payload, 'nextDueAt')), $user->actorId(), new DateTimeImmutable()));

        return $this->created($id);
    }

    #[Route('/cycle-plans/{planId}/start', name: 'cycle_start', methods: ['POST'])]
    #[IsGranted('inventory.count.execute')]
    public function cycleStart(string $planId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        $result = ($this->startCycleCount)(new StartDueCycleCountCommand($planId, $id, $user->tenantId(), $this->string($payload, 'code'), $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => ['id' => $id, 'status' => $result->status, 'lineCount' => $result->lineCount]], JsonResponse::HTTP_CREATED);
    }

    #[Route('/counts/{countId}/lines/{lineId}', name: 'record', methods: ['PUT'])]
    #[IsGranted('inventory.count.execute')]
    public function record(string $countId, string $lineId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $result = ($this->recordCount)(new RecordInventoryCountCommand($countId, $lineId, $user->tenantId(), $this->integer($payload, 'quantity'), $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => ['status' => $result->status, 'differenceCount' => $result->differenceCount]]);
    }

    #[Route('/counts/{countId}/submit', name: 'submit', methods: ['POST'])]
    #[IsGranted('inventory.count.execute')]
    public function submit(string $countId): JsonResponse
    {
        $user = $this->user();
        $result = ($this->submitCount)(new SubmitInventoryCountCommand($countId, $user->tenantId(), $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => ['status' => $result->status, 'differenceCount' => $result->differenceCount]]);
    }

    #[Route('/counts/{countId}/approve', name: 'approve', methods: ['POST'])]
    #[IsGranted('inventory.count.approve')]
    public function approve(string $countId): JsonResponse
    {
        $user = $this->user();
        $result = ($this->approveCount)(new ApproveInventoryCountCommand($countId, $user->tenantId(), $this->control->differenceLedgerIds($user->tenantId(), $countId), $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => ['status' => $result->status, 'adjustedCount' => $result->adjustedCount]]);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
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

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    /** @param array<string, mixed> $payload */
    private function integer(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be an integer.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function optionalInteger(array $payload, string $field): ?int
    {
        $value = $payload[$field] ?? null;

        return is_int($value) ? $value : null;
    }

    /** @param array<string, mixed> $payload */
    private function boolean(array $payload, string $field): bool
    {
        $value = $payload[$field] ?? null;
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a boolean.', $field));
        }

        return $value;
    }

    private function created(string $id): JsonResponse
    {
        return new JsonResponse(['data' => ['id' => $id]], JsonResponse::HTTP_CREATED);
    }
}
