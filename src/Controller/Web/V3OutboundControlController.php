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
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Inventory\Application\OutboundProcessService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/outbound/control', name: 'v3_outbound_control_')]
#[IsGranted('outbound.order.read')]
final class V3OutboundControlController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly OutboundProcessService $processes
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->user();

        return $this->render('v3/outbound/control.html.twig', [
            'page' => 'Warenausgangsleitstand',
            'control' => $this->queries->outboundControlCenter($user->tenantId()),
            'orders' => $this->queries->outboundOrders($user->tenantId()),
            'shipments' => $this->queries->shipments($user->tenantId()),
            'manifests' => $this->queries->loadingManifests($user->tenantId()),
        ]);
    }

    #[Route('/orders/{orderId}/cancel', name: 'cancel_order', methods: ['POST'])]
    #[IsGranted('outbound.order.write')]
    public function cancel(string $orderId, Request $request): Response
    {
        $this->csrf($request, 'v3_outbound_cancel_' . $orderId);
        $user = $this->user();
        $this->processes->cancelOrder($user->tenantId(), $orderId, $this->required($request, 'reason'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Ausgangsauftrag wurde storniert.');

        return $this->back();
    }

    #[Route('/quality', name: 'quality', methods: ['POST'])]
    #[IsGranted('fulfillment.pack.execute')]
    public function quality(Request $request): Response
    {
        $this->csrf($request, 'v3_outbound_quality');
        $user = $this->user();
        $this->processes->inspectPickList($user->tenantId(), $this->required($request, 'pick_list_id'), $request->request->getBoolean('completeness_passed'), $request->request->getBoolean('condition_passed'), $request->request->getBoolean('customer_check_passed'), $this->optional($request, 'note') ?? '', $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Ausgangs-QS wurde dokumentiert.');

        return $this->back();
    }

    #[Route('/shipping-rules', name: 'shipping_rule', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.write')]
    public function shippingRule(Request $request): Response
    {
        $this->csrf($request, 'v3_outbound_shipping_rule');
        $user = $this->user();
        $this->processes->createShippingRule($user->tenantId(), $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'carrier'), $this->required($request, 'service'), $request->request->getInt('min_weight_grams'), $request->request->getInt('max_weight_grams'), $request->request->getInt('priority'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Versandregel wurde aktiviert.');

        return $this->back();
    }

    #[Route('/tracking', name: 'tracking', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.write')]
    public function tracking(Request $request): Response
    {
        $this->csrf($request, 'v3_outbound_tracking');
        $user = $this->user();
        $this->processes->recordTracking($user->tenantId(), $this->required($request, 'shipment_id'), $this->required($request, 'status'), $this->optional($request, 'location'), $this->required($request, 'description'), 'manual', new DateTimeImmutable($this->required($request, 'occurred_at')), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Das Trackingereignis wurde gespeichert und steht zur Rückmeldung bereit.');

        return $this->back();
    }

    #[Route('/documents', name: 'document', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.write')]
    public function document(Request $request): Response
    {
        $this->csrf($request, 'v3_outbound_document');
        $user = $this->user();
        $title = htmlspecialchars($this->required($request, 'title'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $body = nl2br(htmlspecialchars($this->required($request, 'body'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        $content = sprintf('<!doctype html><html lang="de"><head><meta charset="utf-8"><title>%s</title></head><body><h1>%s</h1><p>%s</p></body></html>', $title, $title, $body);
        $this->processes->generateDocument($user->tenantId(), $this->required($request, 'aggregate_type'), $this->required($request, 'aggregate_id'), $this->required($request, 'document_type'), $this->required($request, 'document_number'), $content, $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Das Versanddokument wurde unveränderlich erzeugt.');

        return $this->back();
    }

    #[Route('/documents/{documentId}', name: 'document_show', methods: ['GET'])]
    public function showDocument(string $documentId): Response
    {
        $document = $this->queries->shippingDocument($this->user()->tenantId(), $documentId);
        if ($document === null) {
            throw $this->createNotFoundException('Das Versanddokument wurde nicht gefunden.');
        }

        return new Response((string) $document['content'], Response::HTTP_OK, ['Content-Type' => (string) $document['content_type']]);
    }

    #[Route('/tours', name: 'tour', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.write')]
    public function tour(Request $request): Response
    {
        $this->csrf($request, 'v3_outbound_tour');
        $user = $this->user();
        $this->processes->createTour($user->tenantId(), $this->required($request, 'code'), $this->required($request, 'carrier'), $this->required($request, 'vehicle_reference'), $request->request->getInt('max_weight_grams'), new DateTimeImmutable($this->required($request, 'departure_at')), [['destinationName' => $this->required($request, 'destination_name'), 'destinationAddress' => $this->required($request, 'destination_address'), 'shipmentId' => $this->optional($request, 'shipment_id')]], $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Tour mit Stoppreihenfolge wurde geplant.');

        return $this->back();
    }

    #[Route('/weight-constraints', name: 'weight_constraint', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.write')]
    public function weightConstraint(Request $request): Response
    {
        $this->csrf($request, 'v3_outbound_weight');
        $user = $this->user();
        $this->processes->createWeightConstraint($user->tenantId(), $this->required($request, 'scope'), $this->optional($request, 'reference_code'), $request->request->getInt('max_weight_grams'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Gewichtsrestriktion wurde aktiviert.');

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
        return $this->redirectToRoute('v3_outbound_control_index');
    }
}
