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
use WebWMS\Inventory\Application\SpecialStockService;
use WebWMS\Inventory\Application\StockSelectionService;
use WebWMS\Inventory\Application\WarehouseTopologyService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/inventory', name: 'v3_inventory_')]
final class V3WarehouseController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly WarehouseTopologyService $topology,
        private readonly SpecialStockService $specialStock,
        private readonly StockSelectionService $stockSelection,
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

    #[Route('/traceability', name: 'traceability', methods: ['GET'])]
    #[IsGranted('inventory.traceability.read')]
    public function traceability(Request $request): Response
    {
        $user = $this->user();
        $dimension = $this->query($request, 'dimension');
        $value = $this->query($request, 'value');

        return $this->render('v3/inventory/traceability.html.twig', [
            'page' => 'Rückverfolgung',
            'traceability' => $this->queries->traceability($user->tenantId()),
            'events' => $dimension !== null && $value !== null ? $this->queries->traceabilityEvents($user->tenantId(), $dimension, $value) : [],
            'selectedDimension' => $dimension,
            'selectedValue' => $value,
        ]);
    }

    #[Route('/special-stock', name: 'special_stock', methods: ['GET'])]
    #[IsGranted('inventory.special_stock.read')]
    public function specialStock(): Response
    {
        $user = $this->user();

        return $this->render('v3/inventory/special_stock.html.twig', [
            'page' => 'Sonderbestände',
            'types' => $this->queries->specialStockTypes($user->tenantId()),
            'stock' => $this->queries->stock($user->tenantId(), null, 500, null),
        ]);
    }

    #[Route('/special-stock/types', name: 'special_stock_type_create', methods: ['POST'])]
    #[IsGranted('inventory.special_stock.write')]
    public function createSpecialStockType(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_special_stock_type_create');
        $user = $this->user();
        $this->specialStock->createType(
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $this->required($request, 'code'),
            $this->required($request, 'name'),
            $this->required($request, 'classification_kind'),
            $request->request->getBoolean('allocatable'),
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', 'Das Sonderbestandskennzeichen wurde angelegt.');

        return $this->redirectToRoute('v3_inventory_special_stock');
    }

    #[Route('/special-stock/classify', name: 'special_stock_classify', methods: ['POST'])]
    #[IsGranted('inventory.special_stock.write')]
    public function classifyStock(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_special_stock_classify');
        $user = $this->user();
        $this->specialStock->classify(
            $user->tenantId(),
            $this->required($request, 'product_id'),
            $this->required($request, 'location_id'),
            $this->required($request, 'stock_key'),
            $this->required($request, 'special_stock_type_id'),
            $request->request->getString('owner_reference'),
            $this->required($request, 'reason'),
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', 'Der Bestand wurde neu klassifiziert.');

        return $this->redirectToRoute('v3_inventory_special_stock');
    }

    #[Route('/selection-rules', name: 'selection_rules', methods: ['GET'])]
    #[IsGranted('inventory.selection_rule.read')]
    public function selectionRules(): Response
    {
        $user = $this->user();

        return $this->render('v3/inventory/selection_rules.html.twig', [
            'page' => 'Entnahmestrategien',
            'rules' => $this->queries->stockSelectionRules($user->tenantId()),
            'events' => $this->queries->stockSelectionEvents($user->tenantId()),
            'warehouses' => $this->queries->warehouses($user->tenantId()),
            'products' => $this->queries->products($user->tenantId(), 500, null),
        ]);
    }

    #[Route('/selection-rules', name: 'selection_rule_create', methods: ['POST'])]
    #[IsGranted('inventory.selection_rule.write')]
    public function createSelectionRule(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_selection_rule_create');
        $user = $this->user();
        $this->stockSelection->createRule(
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $this->required($request, 'code'),
            $this->required($request, 'name'),
            $this->required($request, 'strategy'),
            $this->positiveInt($request, 'priority'),
            $request->request->getBoolean('enabled'),
            $this->optional($request, 'warehouse_id'),
            $this->optional($request, 'product_id'),
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', 'Die Entnahmestrategie wurde angelegt.');

        return $this->redirectToRoute('v3_inventory_selection_rules');
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

    private function optional(Request $request, string $field): ?string
    {
        $value = trim((string) $request->request->get($field));

        return $value === '' ? null : $value;
    }

    private function positiveInt(Request $request, string $field): int
    {
        $value = $this->required($request, $field);
        if (!ctype_digit($value) || (int) $value < 1) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" muss eine positive Ganzzahl sein.', $field));
        }

        return (int) $value;
    }

    private function csrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }
}
