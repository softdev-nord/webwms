<?php

declare(strict_types=1);

namespace WebWMS\Warehouse\Presentation\Api;

use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Security\V3\TenantPermissionUser;
use WebWMS\Warehouse\Application\Query\WarehouseQueryService;
use WebWMS\Warehouse\Topology\Application\WarehouseTopologyService;

#[Route('/api/v3/inventory', name: 'api_v3_inventory_')]
class WarehouseTopologyApiController extends AbstractController
{
    public function __construct(
        private readonly WarehouseQueryService $warehouseQueries,
        private readonly WarehouseTopologyService $topology,
    ) {
    }

    #[Route('/topology', name: 'topology', methods: ['GET'])]
    #[IsGranted('inventory.topology.read')]
    public function topology(): JsonResponse
    {
        return new JsonResponse(['data' => $this->warehouseQueries->warehouseTopology($this->user()->tenantId())]);
    }

    #[Route('/overview', name: 'overview', methods: ['GET'])]
    #[IsGranted('inventory.overview.read')]
    public function overview(): JsonResponse
    {
        return new JsonResponse(['data' => $this->warehouseQueries->warehouseOverview($this->user()->tenantId())]);
    }

    #[Route('/occupancy', name: 'occupancy', methods: ['GET'])]
    #[IsGranted('inventory.overview.read')]
    public function occupancy(Request $request): JsonResponse
    {
        $warehouse = trim((string) $request->query->get('warehouse'));
        $aisle = trim((string) $request->query->get('aisle'));

        return new JsonResponse(['data' => $this->warehouseQueries->warehouseOccupancy(
            $this->user()->tenantId(),
            $warehouse === '' ? null : $warehouse,
            $aisle === '' ? null : $aisle,
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
        match ($type) {
            'sites' => $this->topology->createSite($id, $user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'timezone'), $user->actorId(), $now),
            'warehouses' => $this->topology->createWarehouse($id, $user->tenantId(), $this->string($payload, 'siteId'), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'warehouseType'), $user->actorId(), $now),
            'areas' => $this->topology->createArea($id, $user->tenantId(), $this->string($payload, 'warehouseId'), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'areaType'), $user->actorId(), $now),
            'aisles' => $this->topology->createAisle($id, $user->tenantId(), $this->string($payload, 'areaId'), $this->string($payload, 'code'), $this->string($payload, 'name'), $user->actorId(), $now),
            'bins' => $this->createBin($id, $payload, $user, $now),
            default => new JsonResponse(['data' => ['id' => $id, 'type' => $type]], Response::HTTP_CREATED),
        };

        return new JsonResponse(['data' => ['id' => $id, 'type' => $type]], Response::HTTP_CREATED);
    }

    #[Route('/topology/grid', name: 'topology_grid', methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function grid(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $user = $this->user();
        $result = $this->topology->generateGrid($user->tenantId(), $this->string($payload, 'warehouseId'), $this->string($payload, 'areaId'), $this->string($payload, 'aisleId'), $this->integer($payload, 'warehouseNumber'), $this->integer($payload, 'levels'), $this->integer($payload, 'slots'), $this->integer($payload, 'depths'), $this->string($payload, 'description'), $this->string($payload, 'zoneCode'), $this->string($payload, 'locationType'), $this->decimal($payload, 'widthMm'), $this->decimal($payload, 'physicalDepthMm'), $this->decimal($payload, 'heightMm'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $result], Response::HTTP_CREATED);
    }

    #[Route('/topology/import', name: 'topology_import', methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function import(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $user = $this->user();
        $result = $this->topology->importCsv(
            $user->tenantId(),
            $this->string($payload, 'warehouseId'),
            $this->string($payload, 'csv'),
            filter_var($payload['dryRun'] ?? true, FILTER_VALIDATE_BOOL),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $result], Response::HTTP_OK);
    }

    /** @param array<string, mixed> $payload */
    private function createBin(string $id, array $payload, TenantPermissionUser $user, DateTimeImmutable $now): void
    {
        $warehouseNumber = $this->integer($payload, 'warehouseNumber');
        $levelNumber = $this->integer($payload, 'levelNumber');
        $slotNumber = $this->integer($payload, 'slotNumber');
        $depthNumber = $this->integer($payload, 'depthNumber');
        $coordinate = sprintf('%03d%04d%04d%04d', $warehouseNumber, $levelNumber, $slotNumber, $depthNumber);
        $this->topology->createBin($id, $user->tenantId(), $this->string($payload, 'warehouseId'), $this->string($payload, 'areaId'), $this->string($payload, 'aisleId'), $coordinate, (string) $levelNumber, sprintf('%04d-%04d', $slotNumber, $depthNumber), $this->string($payload, 'locationType'), 0, $user->actorId(), $now, $warehouseNumber, $levelNumber, $slotNumber, $depthNumber, $this->string($payload, 'description'), $this->string($payload, 'zoneCode'), $this->decimal($payload, 'widthMm'), $this->decimal($payload, 'physicalDepthMm'), $this->decimal($payload, 'heightMm'));
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
            throw new InvalidArgumentException(sprintf('Field "%s" must be a non-empty string.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $payload */
    private function integer(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value) || $value < 0) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be a non-negative integer.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function decimal(array $payload, string $field): float
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value) && !is_float($value)) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be numeric.', $field));
        }

        return (float) $value;
    }
}
