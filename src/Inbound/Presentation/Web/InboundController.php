<?php

declare(strict_types=1);

namespace WebWMS\Inbound\Presentation\Web;

use DateTimeImmutable;
use InvalidArgumentException;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inbound\Application\Query\InboundQueryService;
use WebWMS\Inventory\Application\ConfirmPutawayCommand;
use WebWMS\Inventory\Application\ConfirmPutawayHandler;
use WebWMS\Inventory\Application\CreatePutawayOrderCommand;
use WebWMS\Inventory\Application\CreatePutawayOrderHandler;
use WebWMS\Inventory\Application\InspectInboundReceiptCommand;
use WebWMS\Inventory\Application\InspectInboundReceiptHandler;
use WebWMS\Inventory\Application\ReceiveInboundDeliveryCommand;
use WebWMS\Inventory\Application\ReceiveInboundDeliveryHandler;
use WebWMS\Inventory\Application\ResolveInboundDiscrepancyCommand;
use WebWMS\Inventory\Application\ResolveInboundDiscrepancyHandler;
use WebWMS\Inventory\Application\UnplannedReceiptService;
use WebWMS\Security\V3\TenantPermissionUser;
use WebWMS\Warehouse\Application\Query\WarehouseQueryService;

#[Route('/v3/inbound', name: 'v3_inbound_')]
class InboundController extends AbstractController
{
    public function __construct(
        private readonly InboundQueryService $inboundQueries,
        private readonly WarehouseQueryService $warehouseQueries,
        private readonly UnplannedReceiptService $receipts,
        private readonly ReceiveInboundDeliveryHandler $receiveInbound,
        private readonly InspectInboundReceiptHandler $inspectInbound,
        private readonly CreatePutawayOrderHandler $createPutaway,
        private readonly ConfirmPutawayHandler $confirmPutaway,
        private readonly ResolveInboundDiscrepancyHandler $resolveDiscrepancy,
    ) {
    }

    #[Route('/planned', name: 'planned', methods: ['GET'])]
    #[IsGranted('inbound.planned.read')]
    public function planned(): Response
    {
        $tenantId = $this->user()->tenantId();

        return $this->render('inbound/planned.html.twig', [
            'page' => 'inbound.planned.planned_goods_receipt',
            'worklist' => $this->inboundQueries->plannedInboundWorklist($tenantId),
            'locations' => $this->inboundQueries->receivingLocations($tenantId),
        ]);
    }

    #[Route('/planned/{deliveryId}/lines/{lineId}/receive', name: 'planned_receive', methods: ['POST'])]
    #[IsGranted('inbound.planned.receive')]
    public function receive(string $deliveryId, string $lineId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_inbound_receive_' . $lineId);
        $user = $this->user();
        ($this->receiveInbound)(new ReceiveInboundDeliveryCommand(
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $deliveryId,
            $lineId,
            $user->actorId(),
            new DateTimeImmutable(),
            $request->request->getInt('actual_quantity'),
            $this->optional($request, 'discrepancy_reason'),
        ));
        $this->addFlash('success', 'inbound.flash.notified_item_was_accepted_and_handed_over_to_quality_assurance');

        return $this->redirectToRoute('v3_inbound_planned');
    }

    #[Route('/planned/receipts/{receiptId}/resolve', name: 'planned_resolve', methods: ['POST'])]
    #[IsGranted('inbound.planned.resolve')]
    public function resolve(string $receiptId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_inbound_resolve_' . $receiptId);
        $user = $this->user();
        ($this->resolveDiscrepancy)(new ResolveInboundDiscrepancyCommand(
            $receiptId,
            $user->tenantId(),
            $this->required($request, 'action'),
            $this->required($request, 'resolution_note'),
            Uuid::v7()->toRfc4122(),
            Uuid::v7()->toRfc4122(),
            Uuid::v7()->toRfc4122(),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'inbound.flash.decision_was_made_regarding_discrepancy');

        return $this->redirectToRoute('v3_inbound_planned');
    }

    #[Route('/planned/receipts/{receiptId}/inspect', name: 'planned_inspect', methods: ['POST'])]
    #[IsGranted('inbound.planned.inspect')]
    public function inspect(string $receiptId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_inbound_inspect_' . $receiptId);
        $user = $this->user();
        $packagingPassed = $request->request->getBoolean('packaging_passed');
        $quantityPassed = $request->request->getBoolean('quantity_passed');
        ($this->inspectInbound)(new InspectInboundReceiptCommand(
            $receiptId,
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $this->required($request, 'location_id'),
            $this->required($request, 'decision'),
            [
                ['question' => 'Verpackung unbeschädigt?', 'passed' => $packagingPassed, 'note' => $this->optional($request, 'packaging_note') ?? ''],
                ['question' => 'Menge vollständig?', 'passed' => $quantityPassed, 'note' => $this->optional($request, 'quantity_note') ?? ''],
            ],
            $user->actorId(),
            new DateTimeImmutable(),
            $this->optional($request, 'batch_number'),
            $this->optional($request, 'serial_number'),
            ($expiresAt = $this->optional($request, 'expires_at')) === null ? null : new DateTimeImmutable($expiresAt),
        ));
        $this->addFlash('success', 'inbound.flash.quality_inspection_was_completed_and_stock_posted');

        return $this->redirectToRoute('v3_inbound_planned');
    }

    #[Route('/planned/receipts/{receiptId}/putaway', name: 'planned_putaway', methods: ['POST'])]
    #[IsGranted('inbound.planned.putaway')]
    public function createPutaway(string $receiptId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_inbound_putaway_' . $receiptId);
        $user = $this->user();
        ($this->createPutaway)(new CreatePutawayOrderCommand(
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $receiptId,
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'inbound.flash.put_away_order_was_created');

        return $this->redirectToRoute('v3_inbound_planned');
    }

    #[Route('/planned/putaway/{orderId}/confirm', name: 'planned_putaway_confirm', methods: ['POST'])]
    #[IsGranted('inbound.planned.putaway')]
    public function confirmPutaway(string $orderId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_inbound_putaway_confirm_' . $orderId);
        $user = $this->user();
        ($this->confirmPutaway)(new ConfirmPutawayCommand(
            $orderId,
            Uuid::v7()->toRfc4122(),
            Uuid::v7()->toRfc4122(),
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'inbound.flash.put_away_was_confirmed_and_stock_transferred_atomically');

        return $this->redirectToRoute('v3_inbound_planned');
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('inbound.receipt.read')]
    public function index(): Response
    {
        return $this->render('inbound/index.html.twig', [
            'page' => 'inbound.control.goods_receipt',
            'receipts' => $this->inboundQueries->unplannedReceipts($this->user()->tenantId()),
        ]);
    }

    #[Route('/unplanned/new', name: 'unplanned_new', methods: ['GET', 'POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function create(Request $request): Response
    {
        $user = $this->user();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_inbound_unplanned_create');
            $receipt = $this->receipts->accept(
                $user->tenantId(),
                $this->required($request, 'code'),
                $this->required($request, 'supplier_code'),
                $this->required($request, 'supplier_name'),
                $this->optional($request, 'delivery_note'),
                [[
                    'productId' => $this->required($request, 'product_id'),
                    'locationId' => $this->required($request, 'location_id'),
                    'quantity' => $request->request->getInt('quantity'),
                    'status' => $this->required($request, 'stock_status'),
                    'batchNumber' => $this->optional($request, 'batch_number'),
                    'serialNumber' => $this->optional($request, 'serial_number'),
                ]],
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'inbound.flash.unplanned_goods_receipt_was_accepted');

            return $this->redirectToRoute('v3_inbound_show', ['receiptId' => $receipt->id()->value()]);
        }

        return $this->render('inbound/new.html.twig', [
            'page' => 'inbound.page.unplanned_goods_receipt',
            'products' => $this->warehouseQueries->products($user->tenantId(), 500, null),
            'locations' => $this->inboundQueries->receivingLocations($user->tenantId()),
        ]);
    }

    #[Route('/unplanned/{receiptId}', name: 'show', methods: ['GET'])]
    #[IsGranted('inbound.receipt.read')]
    public function show(string $receiptId): Response
    {
        $receipt = $this->inboundQueries->unplannedReceipt($this->user()->tenantId(), $receiptId);
        if ($receipt === null) {
            throw $this->createNotFoundException('Der Wareneingang wurde nicht gefunden.');
        }

        return $this->render('inbound/show.html.twig', ['page' => 'inbound.control.goods_receipt', 'receipt' => $receipt]);
    }

    #[Route('/unplanned/{receiptId}/book', name: 'book', methods: ['POST'])]
    #[IsGranted('inbound.receipt.book')]
    public function book(string $receiptId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_inbound_book_' . $receiptId);
        $user = $this->user();
        $this->receipts->book($user->tenantId(), $receiptId, $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'inbound.flash.goods_receipt_was_posted_to_stock_atomically');

        return $this->redirectToRoute('v3_inbound_show', ['receiptId' => $receiptId]);
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
            throw new InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
    }

    private function optional(Request $request, string $field): ?string
    {
        $value = trim((string) $request->request->get($field));

        return $value === '' ? null : $value;
    }

    private function assertCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }
}
