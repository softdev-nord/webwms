<?php

declare(strict_types=1);

namespace WebWMS\Controller\Web;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Inventory\Application\CreateInboundDeliveryCommand;
use WebWMS\Inventory\Application\CreateInboundDeliveryHandler;
use WebWMS\Inventory\Application\CreatePurchaseOrderCommand;
use WebWMS\Inventory\Application\CreatePurchaseOrderHandler;
use WebWMS\Inventory\Application\CreateReturnOrderCommand;
use WebWMS\Inventory\Application\CreateReturnOrderHandler;
use WebWMS\Inventory\Application\InboundProcessService;
use WebWMS\Inventory\Application\InspectReturnCommand;
use WebWMS\Inventory\Application\InspectReturnHandler;
use WebWMS\Inventory\Application\ReceiveReturnCommand;
use WebWMS\Inventory\Application\ReceiveReturnHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/inbound/control', name: 'v3_inbound_control_')]
#[IsGranted('inbound.planned.read')]
final class V3InboundControlController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreatePurchaseOrderHandler $createPurchaseOrder,
        private readonly CreateInboundDeliveryHandler $createDelivery,
        private readonly CreateReturnOrderHandler $createReturn,
        private readonly ReceiveReturnHandler $receiveReturn,
        private readonly InspectReturnHandler $inspectReturn,
        private readonly InboundProcessService $processes,
        private readonly Connection $connection,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->user();

        return $this->render('v3/inbound/control.html.twig', [
            'page' => 'Wareneingangsleitstand',
            'control' => $this->queries->inboundControlCenter($user->tenantId()),
            'worklist' => $this->queries->plannedInboundWorklist($user->tenantId()),
            'products' => $this->queries->products($user->tenantId(), 500, null),
            'locations' => $this->queries->receivingLocations($user->tenantId()),
        ]);
    }

    #[Route('/purchase-orders', name: 'purchase_order', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function purchaseOrder(Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_purchase_order');
        $user = $this->user();
        ($this->createPurchaseOrder)(new CreatePurchaseOrderCommand(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'code'), $this->required($request, 'supplier_reference'), [['id' => Uuid::v7()->toRfc4122(), 'productId' => $this->required($request, 'product_id'), 'quantity' => $request->request->getInt('quantity')]], $user->actorId(), new DateTimeImmutable()));
        $this->addFlash('success', 'Die Bestellung wurde angelegt.');

        return $this->back();
    }

    #[Route('/deliveries', name: 'delivery', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function delivery(Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_delivery');
        $user = $this->user();
        ($this->createDelivery)(new CreateInboundDeliveryCommand(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'purchase_order_id'), $this->required($request, 'code'), $this->required($request, 'delivery_note'), new DateTimeImmutable($this->required($request, 'expected_at')), [['id' => Uuid::v7()->toRfc4122(), 'purchaseOrderItemId' => $this->required($request, 'purchase_order_item_id'), 'quantity' => $request->request->getInt('quantity')]], $user->actorId(), new DateTimeImmutable()));
        $this->addFlash('success', 'Das Lieferavis wurde der Bestellung zugeordnet.');

        return $this->back();
    }

    #[Route('/returns', name: 'return', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function createReturn(Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_return');
        $user = $this->user();
        ($this->createReturn)(new CreateReturnOrderCommand(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'code'), $this->required($request, 'order_reference'), [['id' => Uuid::v7()->toRfc4122(), 'productId' => $this->required($request, 'product_id'), 'quantity' => $request->request->getInt('quantity'), 'reason' => $this->required($request, 'reason')]], $user->actorId(), new DateTimeImmutable()));
        $this->addFlash('success', 'Die Retoure wurde angekündigt.');

        return $this->back();
    }

    #[Route('/returns/{orderId}/{itemId}/receive', name: 'return_receive', methods: ['POST'])]
    #[IsGranted('inbound.planned.receive')]
    public function receiveReturn(string $orderId, string $itemId, Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_return_receive_' . $itemId);
        $user = $this->user();
        ($this->receiveReturn)(new ReceiveReturnCommand(Uuid::v7()->toRfc4122(), $user->tenantId(), $orderId, $itemId, $user->actorId(), new DateTimeImmutable()));
        $this->addFlash('success', 'Die Retoure wurde vereinnahmt und an die Prüfung übergeben.');

        return $this->back();
    }

    #[Route('/returns/receipts/{receiptId}/inspect', name: 'return_inspect', methods: ['POST'])]
    #[IsGranted('inbound.planned.inspect')]
    public function inspectReturn(string $receiptId, Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_return_inspect_' . $receiptId);
        $user = $this->user();
        ($this->inspectReturn)(new InspectReturnCommand($receiptId, Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'location_id'), $this->required($request, 'decision'), $this->required($request, 'note'), $user->actorId(), new DateTimeImmutable(), $this->optional($request, 'batch_number')));
        $this->addFlash('success', 'Die Retourenprüfung wurde abgeschlossen.');

        return $this->back();
    }

    #[Route('/checklists', name: 'checklist', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function checklist(Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_checklist');
        $user = $this->user();
        $this->processes->createChecklist($user->tenantId(), $this->required($request, 'code'), $this->required($request, 'name'), preg_split('/\R/', $this->required($request, 'questions')) ?: [], $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Die QS-Checkliste wurde aktiviert.');

        return $this->back();
    }

    #[Route('/attachments', name: 'attachment', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function attachment(Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_attachment');
        $file = $request->files->get('file');
        if ($file === null || !$file->isValid()) {
            throw new \InvalidArgumentException('Eine gültige Datei ist erforderlich.');
        }
        $user = $this->user();
        $this->processes->attach($user->tenantId(), $this->required($request, 'aggregate_type'), $this->required($request, 'aggregate_id'), $this->required($request, 'category'), $file->getClientOriginalName(), $file->getMimeType() ?? 'application/octet-stream', $file->getContent(), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Nachweis wurde an der Prozessakte abgelegt.');

        return $this->back();
    }

    #[Route('/attachments/{attachmentId}', name: 'attachment_download', methods: ['GET'])]
    public function download(string $attachmentId): Response
    {
        $file = $this->connection->fetchAssociative('SELECT original_name, media_type, content FROM wms_inbound_attachment WHERE id = :id AND tenant_id = :tenantId', ['id' => $attachmentId, 'tenantId' => $this->user()->tenantId()]);
        if ($file === false) {
            throw $this->createNotFoundException('Der Anhang wurde nicht gefunden.');
        }

        return new Response((string) $file['content'], Response::HTTP_OK, ['Content-Type' => (string) $file['media_type'], 'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, (string) $file['original_name'])]);
    }

    #[Route('/labels', name: 'label', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function label(Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_label');
        $user = $this->user();
        $this->processes->requestLabel($user->tenantId(), $this->required($request, 'aggregate_type'), $this->required($request, 'aggregate_id'), $this->required($request, 'label_type'), $request->request->getInt('copies'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Etikettenauftrag wurde in die Warteschlange gestellt.');

        return $this->back();
    }

    #[Route('/cross-dock', name: 'cross_dock', methods: ['POST'])]
    #[IsGranted('inbound.planned.putaway')]
    public function crossDock(Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_cross_dock');
        $user = $this->user();
        $this->processes->assignCrossDock($user->tenantId(), $this->required($request, 'receipt_id'), $this->required($request, 'outbound_item_id'), $request->request->getInt('quantity'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Eingang wurde direkt für den Ausgang bereitgestellt.');

        return $this->back();
    }

    #[Route('/production', name: 'production', methods: ['POST'])]
    #[IsGranted('inbound.receipt.book')]
    public function production(Request $request): Response
    {
        $this->csrf($request, 'v3_inbound_production');
        $user = $this->user();
        $this->processes->receiveProduction($user->tenantId(), $this->required($request, 'production_order'), $this->required($request, 'product_id'), $this->required($request, 'location_id'), $request->request->getInt('quantity'), $this->optional($request, 'batch_number'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Fertigmeldung wurde als Bestand gebucht.');

        return $this->back();
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

    private function optional(Request $request, string $field): ?string
    {
        $value = trim((string) $request->request->get($field));

        return $value === '' ? null : $value;
    }

    private function csrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }

    private function back(): Response
    {
        return $this->redirectToRoute('v3_inbound_control_index');
    }
}
