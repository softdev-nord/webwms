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
use WebWMS\Inventory\Application\WarehouseTopologyService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/inventory', name: 'api_v3_inventory_')]
final class WarehouseTopologyApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly WarehouseTopologyService $topology,
    ) {
    }

    #[Route('/topology', name: 'topology', methods: ['GET'])]
    #[IsGranted('inventory.topology.read')]
    public function topology(): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->warehouseTopology($this->user()->tenantId())]);
    }

    #[Route('/overview', name: 'overview', methods: ['GET'])]
    #[IsGranted('inventory.overview.read')]
    public function overview(): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->warehouseOverview($this->user()->tenantId())]);
    }

    #[Route('/occupancy', name: 'occupancy', methods: ['GET'])]
    #[IsGranted('inventory.overview.read')]
    public function occupancy(Request $request): JsonResponse
    {
        $warehouse = trim((string) $request->query->get('warehouse'));

        return new JsonResponse(['data' => $this->queries->warehouseOccupancy(
            $this->user()->tenantId(),
            $warehouse === '' ? null : $warehouse,
        )]);
    }

    #[Route('/topology/{type}', name: 'topology_create', requirements: ['type' => 'sites|warehouses|areas|aisles|bins'], methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function create(string $type, Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        $now = new DateTimeImmutable();
        switch ($type) {
            case 'sites':
                $this->topology->createSite($id, $user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'timezone'), $user->actorId(), $now);

                break;
            case 'warehouses':
                $this->topology->createWarehouse($id, $user->tenantId(), $this->string($payload, 'siteId'), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'warehouseType'), $user->actorId(), $now);

                break;
            case 'areas':
                $this->topology->createArea($id, $user->tenantId(), $this->string($payload, 'warehouseId'), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'areaType'), $user->actorId(), $now);

                break;
            case 'aisles':
                $this->topology->createAisle($id, $user->tenantId(), $this->string($payload, 'areaId'), $this->string($payload, 'code'), $this->string($payload, 'name'), $user->actorId(), $now);

                break;
            case 'bins':
                $this->topology->createBin($id, $user->tenantId(), $this->string($payload, 'warehouseId'), $this->string($payload, 'areaId'), $this->string($payload, 'aisleId'), $this->string($payload, 'code'), $this->string($payload, 'levelCode'), $this->string($payload, 'binCode'), $this->string($payload, 'locationType'), $this->integer($payload, 'capacityQuantity'), $user->actorId(), $now);

                break;
        }

        return new JsonResponse(['data' => ['id' => $id, 'type' => $type]], Response::HTTP_CREATED);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
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
    private function integer(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value) || $value < 0) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a non-negative integer.', $field));
        }

        return $value;
    }
}
