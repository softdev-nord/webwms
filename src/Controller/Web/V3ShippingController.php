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
use WebWMS\Integration\Application\PrintGateway;
use WebWMS\Inventory\Application\CreateShipmentCommand;
use WebWMS\Inventory\Application\CreateShipmentHandler;
use WebWMS\Inventory\Application\DispatchShipmentCommand;
use WebWMS\Inventory\Application\DispatchShipmentHandler;
use WebWMS\Inventory\Application\RegisterShipmentLabelCommand;
use WebWMS\Inventory\Application\RegisterShipmentLabelHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/shipping', name: 'v3_shipping_')]
final class V3ShippingController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreateShipmentHandler $createShipment,
        private readonly RegisterShipmentLabelHandler $registerLabel,
        private readonly DispatchShipmentHandler $dispatchShipment,
        private readonly PrintGateway $printGateway,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('fulfillment.ship.read')]
    public function index(): Response
    {
        return $this->render('v3/shipping/index.html.twig', [
            'page' => 'Versand',
            'shipments' => $this->queries->shipments($this->tenantUser()->tenantId()),
        ]);
    }

    #[Route('/from-packing-order/{packingOrderId}', name: 'create', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.write')]
    public function create(string $packingOrderId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_shipping_create_' . $packingOrderId);
        $user = $this->tenantUser();
        $packingOrder = $this->queries->packingOrder($user->tenantId(), $packingOrderId);
        if ($packingOrder === null || ($packingOrder['status'] ?? null) !== 'completed') {
            throw $this->createNotFoundException('Ein abgeschlossener Packauftrag ist erforderlich.');
        }
        $shipmentId = Uuid::v7()->toRfc4122();
        ($this->createShipment)(new CreateShipmentCommand(
            $shipmentId,
            $user->tenantId(),
            $packingOrderId,
            $this->required($request, 'shipment_number'),
            $this->required($request, 'carrier'),
            $this->required($request, 'service'),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Die Sendung wurde vorbereitet.');

        return $this->redirectToRoute('v3_shipping_show', ['shipmentId' => $shipmentId]);
    }

    #[Route('/{shipmentId}', name: 'show', methods: ['GET'])]
    #[IsGranted('fulfillment.ship.read')]
    public function show(string $shipmentId): Response
    {
        $user = $this->tenantUser();

        return $this->render('v3/shipping/show.html.twig', [
            'page' => 'Sendung',
            'shipment' => $this->requiredShipment($shipmentId),
            'printers' => array_values(array_filter(
                $this->queries->printers($user->tenantId()),
                static fn (array $printer): bool => (bool) ($printer['active'] ?? false),
            )),
            'printJobs' => array_values(array_filter(
                $this->queries->printJobs($user->tenantId()),
                static fn (array $job): bool => ($job['document_reference'] ?? null) === $shipmentId,
            )),
        ]);
    }

    #[Route('/{shipmentId}/label', name: 'label', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.label')]
    public function label(string $shipmentId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_shipping_label_' . $shipmentId);
        $this->requiredShipment($shipmentId);
        $user = $this->tenantUser();
        ($this->registerLabel)(new RegisterShipmentLabelCommand(
            $shipmentId,
            $user->tenantId(),
            $this->required($request, 'tracking_number'),
            $this->required($request, 'label_reference'),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Label und Trackingnummer wurden registriert.');

        return $this->redirectToRoute('v3_shipping_show', ['shipmentId' => $shipmentId]);
    }

    #[Route('/{shipmentId}/print-label', name: 'print_label', methods: ['POST'])]
    #[IsGranted('integration.print_job.write')]
    public function printLabel(string $shipmentId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_shipping_print_' . $shipmentId);
        $shipment = $this->requiredShipment($shipmentId);
        if (!is_string($shipment['label_reference'] ?? null)) {
            throw new LogicException('Vor dem Druck muss ein Label registriert sein.');
        }
        $user = $this->tenantUser();
        $this->printGateway->queue(
            $user->tenantId(),
            $this->required($request, 'printer_id'),
            'carrier_label',
            $shipmentId,
            'zpl',
            1,
            Uuid::v7()->toRfc4122(),
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', 'Das Versandlabel wurde in die Druckwarteschlange gestellt.');

        return $this->redirectToRoute('v3_shipping_show', ['shipmentId' => $shipmentId]);
    }

    #[Route('/{shipmentId}/dispatch', name: 'dispatch', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.dispatch')]
    public function dispatch(string $shipmentId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_shipping_dispatch_' . $shipmentId);
        $this->requiredShipment($shipmentId);
        $user = $this->tenantUser();
        ($this->dispatchShipment)(new DispatchShipmentCommand(
            $shipmentId,
            $user->tenantId(),
            $this->required($request, 'handover_reference'),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Die Sendung wurde an den Frachtführer übergeben.');

        return $this->redirectToRoute('v3_shipping_show', ['shipmentId' => $shipmentId]);
    }

    /** @return array<string, mixed> */
    private function requiredShipment(string $shipmentId): array
    {
        $shipment = $this->queries->shipment($this->tenantUser()->tenantId(), $shipmentId);
        if ($shipment === null) {
            throw $this->createNotFoundException('Die Sendung wurde nicht gefunden.');
        }

        return $shipment;
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
    }

    private function assertCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }

    private function tenantUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }
}
