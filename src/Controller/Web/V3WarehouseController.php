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
use WebWMS\Inventory\Application\StockBlockingService;
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
        private readonly StockBlockingService $stockBlocking,
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

    #[Route('/topology/{resource}/new', name: 'topology_new', requirements: ['resource' => 'site|warehouse|area|aisle|bin'], methods: ['GET'])]
    #[IsGranted('inventory.topology.write')]
    public function newTopologyEntry(string $resource): Response
    {
        return $this->render('v3/inventory/topology_form.html.twig', [
            'page' => 'Topologieeintrag anlegen', 'resource' => $resource, 'entry' => null,
            'topology' => $this->queries->warehouseTopology($this->user()->tenantId()),
        ]);
    }

    #[Route('/topology/{resource}/{id}/edit', name: 'topology_edit', requirements: ['resource' => 'site|warehouse|area|aisle|bin'], methods: ['GET'])]
    #[IsGranted('inventory.topology.write')]
    public function editTopologyEntry(string $resource, string $id): Response
    {
        $user = $this->user();

        return $this->render('v3/inventory/topology_form.html.twig', [
            'page' => 'Topologieeintrag bearbeiten', 'resource' => $resource,
            'entry' => $this->topology->topologyEntry($user->tenantId(), $resource, $id),
            'topology' => $this->queries->warehouseTopology($user->tenantId()),
        ]);
    }

    #[Route('/topology/{resource}/{id}', name: 'topology_update', requirements: ['resource' => 'site|warehouse|area|aisle|bin'], methods: ['POST'])]
    #[IsGranted('inventory.topology.write')]
    public function updateTopologyEntry(string $resource, string $id, Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_topology_update_' . $resource . '_' . $id);
        $user = $this->user();
        $this->topology->updateTopologyEntry($user->tenantId(), $user->actorId(), $resource, $id, $request->request->all(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Topologieeintrag wurde aktualisiert.');

        return $this->redirectToRoute('v3_inventory_topology');
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

    #[Route('/occupancy', name: 'occupancy', methods: ['GET'])]
    #[IsGranted('inventory.overview.read')]
    public function occupancy(Request $request): Response
    {
        $user = $this->user();
        $warehouses = $this->queries->warehouses($user->tenantId());
        $warehouseId = $this->query($request, 'warehouse');
        if ($warehouseId === null && isset($warehouses[0]['id']) && is_string($warehouses[0]['id'])) {
            $warehouseId = $warehouses[0]['id'];
        }

        return $this->render('v3/inventory/occupancy.html.twig', [
            'page' => 'Grafische Lagebelegung',
            'warehouses' => $warehouses,
            'locations' => $this->queries->warehouseOccupancy($user->tenantId(), $warehouseId),
            'selectedWarehouse' => $warehouseId,
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

    #[Route('/special-stock/types/new', name: 'special_stock_type_new', methods: ['GET'])]
    #[IsGranted('inventory.special_stock.write')]
    public function newSpecialStockType(): Response
    {
        return $this->render('v3/inventory/configuration_form.html.twig', ['page' => 'Sonderbestandskennzeichen anlegen', 'kind' => 'special_stock_type', 'entry' => null]);
    }

    #[Route('/special-stock/types/{typeId}/edit', name: 'special_stock_type_edit', methods: ['GET'])]
    #[IsGranted('inventory.special_stock.write')]
    public function editSpecialStockType(string $typeId): Response
    {
        return $this->render('v3/inventory/configuration_form.html.twig', ['page' => 'Sonderbestandskennzeichen bearbeiten', 'kind' => 'special_stock_type', 'entry' => $this->specialStock->type($this->user()->tenantId(), $typeId)]);
    }

    #[Route('/special-stock/types/{typeId}', name: 'special_stock_type_update', methods: ['POST'])]
    #[IsGranted('inventory.special_stock.write')]
    public function updateSpecialStockType(string $typeId, Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_special_stock_type_update_' . $typeId);
        $user = $this->user();
        $this->specialStock->updateType($user->tenantId(), $typeId, $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'classification_kind'), $request->request->getBoolean('allocatable'), $request->request->getBoolean('active'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Das Sonderbestandskennzeichen wurde aktualisiert.');

        return $this->redirectToRoute('v3_inventory_special_stock');
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

    #[Route('/special-stock/classify', name: 'special_stock_classify_form', methods: ['GET'])]
    #[IsGranted('inventory.special_stock.write')]
    public function classifyStockForm(Request $request): Response
    {
        $user = $this->user();
        $productId = $this->query($request, 'product');
        $locationId = $this->query($request, 'location');
        $stockKey = $this->query($request, 'stock_key');
        $stock = array_values(array_filter($this->queries->stock($user->tenantId(), null, 500, null), static fn (array $row): bool => $row['product_id'] === $productId && $row['location_id'] === $locationId && $row['stock_key'] === $stockKey));
        if (!isset($stock[0])) {
            throw $this->createNotFoundException('Der Bestand wurde nicht gefunden.');
        }

        return $this->render('v3/inventory/special_stock_classify.html.twig', ['page' => 'Sonderbestand klassifizieren', 'row' => $stock[0], 'types' => $this->queries->specialStockTypes($user->tenantId())]);
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

    #[Route('/selection-rules/new', name: 'selection_rule_new', methods: ['GET'])]
    #[IsGranted('inventory.selection_rule.write')]
    public function newSelectionRule(): Response
    {
        $user = $this->user();

        return $this->render('v3/inventory/configuration_form.html.twig', ['page' => 'Entnahmestrategie anlegen', 'kind' => 'selection_rule', 'entry' => null, 'warehouses' => $this->queries->warehouses($user->tenantId()), 'products' => $this->queries->products($user->tenantId(), 500, null)]);
    }

    #[Route('/selection-rules/{ruleId}/edit', name: 'selection_rule_edit', methods: ['GET'])]
    #[IsGranted('inventory.selection_rule.write')]
    public function editSelectionRule(string $ruleId): Response
    {
        $user = $this->user();

        return $this->render('v3/inventory/configuration_form.html.twig', ['page' => 'Entnahmestrategie bearbeiten', 'kind' => 'selection_rule', 'entry' => $this->stockSelection->rule($user->tenantId(), $ruleId), 'warehouses' => $this->queries->warehouses($user->tenantId()), 'products' => $this->queries->products($user->tenantId(), 500, null)]);
    }

    #[Route('/selection-rules/{ruleId}', name: 'selection_rule_update', methods: ['POST'])]
    #[IsGranted('inventory.selection_rule.write')]
    public function updateSelectionRule(string $ruleId, Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_selection_rule_update_' . $ruleId);
        $user = $this->user();
        $this->stockSelection->updateRule($user->tenantId(), $ruleId, $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'strategy'), $this->positiveInt($request, 'priority'), $request->request->getBoolean('enabled'), $this->optional($request, 'warehouse_id'), $this->optional($request, 'product_id'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Entnahmestrategie wurde aktualisiert.');

        return $this->redirectToRoute('v3_inventory_selection_rules');
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

    #[Route('/stock-blocks', name: 'stock_blocks', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.read')]
    public function stockBlocks(): Response
    {
        $user = $this->user();

        return $this->render('v3/inventory/stock_blocks.html.twig', [
            'page' => 'Bestandssperren',
            'reasons' => $this->queries->stockBlockReasons($user->tenantId()),
            'blocks' => $this->queries->stockBlocks($user->tenantId()),
            'stock' => $this->queries->stock($user->tenantId(), null, 500, null),
        ]);
    }

    #[Route('/stock-blocks/{blockId}', name: 'stock_block_show', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.read')]
    public function stockBlock(string $blockId): Response
    {
        $user = $this->user();
        $blocks = array_values(array_filter($this->queries->stockBlocks($user->tenantId()), static fn (array $row): bool => $row['id'] === $blockId));
        if (!isset($blocks[0])) {
            throw $this->createNotFoundException('Die Bestandssperre wurde nicht gefunden.');
        }

        return $this->render('v3/inventory/stock_block_show.html.twig', ['page' => 'Bestandssperre bearbeiten', 'block' => $blocks[0], 'events' => $this->queries->stockBlockEvents($user->tenantId(), $blockId)]);
    }

    #[Route('/stock-blocks/reasons/new', name: 'stock_block_reason_new', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.write')]
    public function newStockBlockReason(): Response
    {
        return $this->render('v3/inventory/configuration_form.html.twig', ['page' => 'Sperrgrund anlegen', 'kind' => 'stock_block_reason', 'entry' => null]);
    }

    #[Route('/stock-blocks/reasons', name: 'stock_block_reasons', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.read')]
    public function stockBlockReasons(): Response
    {
        return $this->render('v3/inventory/stock_block_reasons.html.twig', ['page' => 'Sperrgründe', 'reasons' => $this->queries->stockBlockReasons($this->user()->tenantId())]);
    }

    #[Route('/stock-blocks/new', name: 'stock_block_new', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.write')]
    public function newStockBlock(Request $request): Response
    {
        $user = $this->user();
        $productId = $this->query($request, 'product');
        $locationId = $this->query($request, 'location');
        $stockKey = $this->query($request, 'stock_key');
        $stock = array_values(array_filter($this->queries->stock($user->tenantId(), null, 500, null), static fn (array $row): bool => $row['product_id'] === $productId && $row['location_id'] === $locationId && $row['stock_key'] === $stockKey));
        if (!isset($stock[0])) {
            throw $this->createNotFoundException('Der Bestand wurde nicht gefunden.');
        }

        return $this->render('v3/inventory/stock_block_new.html.twig', ['page' => 'Bestand sperren', 'row' => $stock[0], 'reasons' => $this->queries->stockBlockReasons($user->tenantId())]);
    }

    #[Route('/stock-blocks/reasons/{reasonId}/edit', name: 'stock_block_reason_edit', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.write')]
    public function editStockBlockReason(string $reasonId): Response
    {
        return $this->render('v3/inventory/configuration_form.html.twig', ['page' => 'Sperrgrund bearbeiten', 'kind' => 'stock_block_reason', 'entry' => $this->stockBlocking->reason($this->user()->tenantId(), $reasonId)]);
    }

    #[Route('/stock-blocks/reasons/{reasonId}', name: 'stock_block_reason_update', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.write')]
    public function updateStockBlockReason(string $reasonId, Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_stock_block_reason_update_' . $reasonId);
        $user = $this->user();
        $this->stockBlocking->updateReason($user->tenantId(), $reasonId, $this->required($request, 'code'), $this->required($request, 'name'), $this->optional($request, 'description'), $request->request->getBoolean('active'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Sperrgrund wurde aktualisiert.');

        return $this->redirectToRoute('v3_inventory_stock_blocks');
    }

    #[Route('/stock-blocks/reasons', name: 'stock_block_reason_create', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.write')]
    public function createStockBlockReason(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_stock_block_reason_create');
        $user = $this->user();
        $this->stockBlocking->createReason(
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $this->required($request, 'code'),
            $this->required($request, 'name'),
            $this->optional($request, 'description'),
            $request->request->getBoolean('active'),
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', 'Der Sperrgrund wurde angelegt.');

        return $this->redirectToRoute('v3_inventory_stock_blocks');
    }

    #[Route('/stock-blocks', name: 'stock_block_create', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.write')]
    public function createStockBlock(Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_stock_block_create');
        $user = $this->user();
        $this->stockBlocking->block(
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $this->required($request, 'reason_id'),
            $this->required($request, 'product_id'),
            $this->required($request, 'location_id'),
            $this->required($request, 'stock_status'),
            $this->optional($request, 'batch_number'),
            $this->optional($request, 'serial_number'),
            ($expiresAt = $this->optional($request, 'expires_at')) === null ? null : new DateTimeImmutable($expiresAt),
            $this->positiveInt($request, 'quantity'),
            $this->required($request, 'note'),
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', 'Der Bestand wurde gesperrt und dem Prüfworkflow übergeben.');

        return $this->redirectToRoute('v3_inventory_stock_blocks');
    }

    #[Route('/stock-blocks/{blockId}/review', name: 'stock_block_review', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.review')]
    public function reviewStockBlock(string $blockId, Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_stock_block_review_' . $blockId);
        $user = $this->user();
        $this->stockBlocking->review($user->tenantId(), $blockId, $this->required($request, 'note'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Bestandssperre wurde geprüft.');

        return $this->redirectToRoute('v3_inventory_stock_blocks', ['block' => $blockId]);
    }

    #[Route('/stock-blocks/{blockId}/release', name: 'stock_block_release', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.release')]
    public function releaseStockBlock(string $blockId, Request $request): Response
    {
        $this->csrf($request, 'v3_inventory_stock_block_release_' . $blockId);
        $user = $this->user();
        $this->stockBlocking->release($user->tenantId(), $blockId, $this->required($request, 'note'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der geprüfte Bestand wurde freigegeben.');

        return $this->redirectToRoute('v3_inventory_stock_blocks', ['block' => $blockId]);
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
