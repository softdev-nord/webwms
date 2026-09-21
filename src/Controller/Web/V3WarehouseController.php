<?php

declare(strict_types=1);

namespace WebWMS\Controller\Web;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Integration\Application\StockMovementCriteria;
use WebWMS\Inventory\Application\WarehouseTopologyService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/inventory', name: 'v3_inventory_')]
final class V3WarehouseController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly WarehouseTopologyService $topology,
    ) {
    }

    #[Route('/topology', name: 'topology', methods: ['GET'])]
    #[IsGranted('inventory.topology.read')]
    public function topology(): Response
    {
        return $this->render('v3/inventory/topology.html.twig', [
            'page' => 'Lagertopologie',
            'topology' => $this->queries->warehouseTopology($this->user()->tenantId()),
        ]);
    }

    #[Route('/topology/sites', name: 'site_create', methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function createSite(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_site_create');
        $user = $this->user();
        $this->topology->createSite(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'timezone'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Standort wurde angelegt.');

        return $this->redirectToRoute('v3_inventory_topology');
    }

    #[Route('/topology/warehouses', name: 'warehouse_create', methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function createWarehouse(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_warehouse_create');
        $user = $this->user();
        $this->topology->createWarehouse(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'site_id'), $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'warehouse_type'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Das Lager wurde angelegt.');

        return $this->redirectToRoute('v3_inventory_topology');
    }

    #[Route('/topology/areas', name: 'area_create', methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function createArea(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_area_create');
        $user = $this->user();
        $this->topology->createArea(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'warehouse_id'), $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'area_type'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Lagerbereich wurde angelegt.');

        return $this->redirectToRoute('v3_inventory_topology');
    }

    #[Route('/topology/aisles', name: 'aisle_create', methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function createAisle(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_aisle_create');
        $user = $this->user();
        $this->topology->createAisle(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'area_id'), $this->required($request, 'code'), $this->required($request, 'name'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Lagergang wurde angelegt.');

        return $this->redirectToRoute('v3_inventory_topology');
    }

    #[Route('/topology/bins', name: 'bin_create', methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function createBin(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_bin_create');
        $user = $this->user();
        $this->topology->createBin(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'warehouse_id'), $this->required($request, 'area_id'), $this->required($request, 'aisle_id'), $this->required($request, 'code'), $this->required($request, 'level_code'), $this->required($request, 'bin_code'), $this->required($request, 'location_type'), $request->request->getInt('capacity_quantity'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Lagerplatz wurde angelegt.');

        return $this->redirectToRoute('v3_inventory_topology');
    }

    #[Route('/overview', name: 'overview', methods: ['GET'])]
    #[IsGranted('inventory.overview.read')]
    public function overview(): Response
    {
        return $this->render('v3/inventory/overview.html.twig', [
            'page' => 'Lagerübersicht',
            'overview' => $this->queries->warehouseOverview($this->user()->tenantId()),
        ]);
    }

    #[Route('/movements', name: 'movements', methods: ['GET'])]
    #[IsGranted('inventory.stock.movement.read')]
    public function movements(Request $request): Response
    {
        $user = $this->user();
        $productId = $this->query($request, 'product');
        $locationId = $this->query($request, 'location');
        $movementType = $this->query($request, 'movement_type');

        return $this->render('v3/inventory/movements.html.twig', [
            'page' => 'Bewegungshistorie',
            'movements' => $this->queries->stockMovements($user->tenantId(), new StockMovementCriteria($productId, $locationId, null, $movementType), 500, null),
            'products' => $this->queries->products($user->tenantId(), 500, null),
            'locations' => $this->queries->receivingLocations($user->tenantId()),
            'filters' => ['product' => $productId, 'location' => $locationId, 'movement_type' => $movementType],
        ]);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
    }

    private function query(Request $request, string $field): ?string
    {
        $value = trim((string) $request->query->get($field));

        return $value === '' ? null : $value;
    }

    private function csrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }
}
