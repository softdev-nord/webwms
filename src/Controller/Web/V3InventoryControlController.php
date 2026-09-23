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

#[Route('/v3/inventory/control', name: 'v3_inventory_control_')]
#[IsGranted('inventory.control.read')]
final class V3InventoryControlController extends AbstractController
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
    public function index(): Response
    {
        return $this->render('v3/inventory/control.html.twig', ['page' => 'Inventory Control Center', 'inventory' => $this->control->workspace($this->user()->tenantId())]);
    }

    #[Route('/hazard-classes', name: 'hazard_class', methods: ['POST'])]
    #[IsGranted('inventory.hazard.write')]
    public function hazardClass(Request $request): Response
    {
        $this->csrf($request, 'inventory_hazard_class');
        $user = $this->user();
        $this->control->createHazardClass($user->tenantId(), $user->actorId(), $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'un_class'), new DateTimeImmutable());

        return $this->success('Gefahrstoffklasse wurde angelegt.');
    }

    #[Route('/hazardous-materials', name: 'hazardous_material', methods: ['POST'])]
    #[IsGranted('inventory.hazard.write')]
    public function hazardousMaterial(Request $request): Response
    {
        $this->csrf($request, 'inventory_hazardous_material');
        $user = $this->user();
        $this->control->classifyMaterial($user->tenantId(), $user->actorId(), $this->required($request, 'product_id'), $this->required($request, 'hazard_class_id'), $this->required($request, 'un_number'), (string) $request->request->get('packing_group'), $this->required($request, 'description'), new DateTimeImmutable());

        return $this->success('Artikel wurde als Gefahrstoff klassifiziert.');
    }

    #[Route('/storage-restrictions', name: 'storage_restriction', methods: ['POST'])]
    #[IsGranted('inventory.hazard.write')]
    public function storageRestriction(Request $request): Response
    {
        $this->csrf($request, 'inventory_storage_restriction');
        $user = $this->user();
        $max = trim((string) $request->request->get('max_quantity'));
        $this->control->restrictStorage($user->tenantId(), $user->actorId(), $this->required($request, 'hazard_class_id'), $this->required($request, 'location_prefix'), $request->request->getBoolean('allowed'), $max === '' ? null : (int) $max, new DateTimeImmutable());

        return $this->success('Lagerbereichsregel wurde gespeichert.');
    }

    #[Route('/boms', name: 'bom', methods: ['POST'])]
    #[IsGranted('inventory.bom.write')]
    public function bom(Request $request): Response
    {
        $this->csrf($request, 'inventory_bom');
        $items = json_decode($this->required($request, 'items'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($items)) {
            throw new \InvalidArgumentException('Die Komponenten müssen ein JSON-Array sein.');
        }
        $normalized = [];
        foreach ($items as $item) {
            if (!is_array($item) || !is_string($item['productId'] ?? null) || !is_int($item['quantity'] ?? null)) {
                throw new \InvalidArgumentException('Jede Komponente benötigt productId und quantity.');
            }
            $normalized[] = ['productId' => $item['productId'], 'quantity' => $item['quantity']];
        }
        $user = $this->user();
        $this->control->createBom($user->tenantId(), $user->actorId(), $this->required($request, 'product_id'), $this->required($request, 'code'), $this->required($request, 'version'), $normalized, new DateTimeImmutable());

        return $this->success('Stückliste wurde angelegt.');
    }

    #[Route('/requirements', name: 'requirement', methods: ['POST'])]
    #[IsGranted('inventory.bom.execute')]
    public function requirement(Request $request): Response
    {
        $this->csrf($request, 'inventory_requirement');
        $user = $this->user();
        $this->control->planRequirement($user->tenantId(), $user->actorId(), $this->required($request, 'bom_id'), $this->required($request, 'reference'), $request->request->getInt('production_quantity'), new DateTimeImmutable());

        return $this->success('Materialbedarf wurde geplant.');
    }

    #[Route('/load-carriers', name: 'load_carrier', methods: ['POST'])]
    #[IsGranted('inventory.load_carrier.write')]
    public function loadCarrier(Request $request): Response
    {
        $this->csrf($request, 'inventory_load_carrier');
        $user = $this->user();
        $this->control->bookLoadCarrier($user->tenantId(), $user->actorId(), $this->required($request, 'partner_code'), $this->required($request, 'carrier_type'), $request->request->getInt('quantity'), $this->required($request, 'reference'), (string) $request->request->get('note'), new DateTimeImmutable());

        return $this->success('Ladehilfsmittel wurden verbucht.');
    }

    #[Route('/counts', name: 'count', methods: ['POST'])]
    #[IsGranted('inventory.count.write')]
    public function count(Request $request): Response
    {
        $this->csrf($request, 'inventory_count');
        $user = $this->user();
        ($this->createCount)(new CreateInventoryCountCommand(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'warehouse_id'), $this->required($request, 'code'), (string) $request->request->get('location_prefix'), $user->actorId(), new DateTimeImmutable()));

        return $this->success('Stichtagsinventur wurde eröffnet.');
    }

    #[Route('/cycle-plans', name: 'cycle_plan', methods: ['POST'])]
    #[IsGranted('inventory.count.write')]
    public function cyclePlan(Request $request): Response
    {
        $this->csrf($request, 'inventory_cycle_plan');
        $user = $this->user();
        ($this->createCyclePlan)(new CreateCycleCountPlanCommand(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'warehouse_id'), $this->required($request, 'code'), (string) $request->request->get('location_prefix'), $request->request->getInt('interval_days'), new DateTimeImmutable($this->required($request, 'next_due_at')), $user->actorId(), new DateTimeImmutable()));

        return $this->success('Permanenter Inventurplan wurde angelegt.');
    }

    #[Route('/cycle-plans/{planId}/start', name: 'cycle_start', methods: ['POST'])]
    #[IsGranted('inventory.count.execute')]
    public function cycleStart(string $planId, Request $request): Response
    {
        $this->csrf($request, 'inventory_cycle_start_' . $planId);
        $user = $this->user();
        ($this->startCycleCount)(new StartDueCycleCountCommand($planId, Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'code'), $user->actorId(), new DateTimeImmutable()));

        return $this->success('Zyklische Inventur wurde gestartet.');
    }

    #[Route('/counts/{countId}/lines/{lineId}', name: 'record', methods: ['POST'])]
    #[IsGranted('inventory.count.execute')]
    public function record(string $countId, string $lineId, Request $request): Response
    {
        $this->csrf($request, 'inventory_count_line_' . $lineId);
        $user = $this->user();
        ($this->recordCount)(new RecordInventoryCountCommand($countId, $lineId, $user->tenantId(), $request->request->getInt('quantity'), $user->actorId(), new DateTimeImmutable()));

        return $this->success('Zählmenge wurde gespeichert.');
    }

    #[Route('/counts/{countId}/submit', name: 'submit', methods: ['POST'])]
    #[IsGranted('inventory.count.execute')]
    public function submit(string $countId, Request $request): Response
    {
        $this->csrf($request, 'inventory_count_submit_' . $countId);
        $user = $this->user();
        ($this->submitCount)(new SubmitInventoryCountCommand($countId, $user->tenantId(), $user->actorId(), new DateTimeImmutable()));

        return $this->success('Inventur wurde zur Freigabe eingereicht.');
    }

    #[Route('/counts/{countId}/approve', name: 'approve', methods: ['POST'])]
    #[IsGranted('inventory.count.approve')]
    public function approve(string $countId, Request $request): Response
    {
        $this->csrf($request, 'inventory_count_approve_' . $countId);
        $user = $this->user();
        ($this->approveCount)(new ApproveInventoryCountCommand($countId, $user->tenantId(), $this->control->differenceLedgerIds($user->tenantId(), $countId), $user->actorId(), new DateTimeImmutable()));

        return $this->success('Inventurdifferenzen wurden freigegeben und gebucht.');
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

    private function csrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }

    private function success(string $message): Response
    {
        $this->addFlash('success', $message);

        return $this->redirectToRoute('v3_inventory_control_index');
    }
}
