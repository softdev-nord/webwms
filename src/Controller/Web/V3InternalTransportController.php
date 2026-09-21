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
use WebWMS\Fulfillment\Application\InternalTransportService;
use WebWMS\Inventory\Application\ConfirmReplenishmentCommand;
use WebWMS\Inventory\Application\ConfirmReplenishmentHandler;
use WebWMS\Inventory\Application\CreateReplenishmentOrderCommand;
use WebWMS\Inventory\Application\CreateReplenishmentOrderHandler;
use WebWMS\Inventory\Application\CreateReplenishmentPolicyCommand;
use WebWMS\Inventory\Application\CreateReplenishmentPolicyHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/internal-transport', name: 'v3_internal_transport_')]
final class V3InternalTransportController extends AbstractController
{
    public function __construct(
        private readonly InternalTransportService $transport,
        private readonly CreateReplenishmentPolicyHandler $createReplenishmentPolicy,
        private readonly CreateReplenishmentOrderHandler $createReplenishmentOrder,
        private readonly ConfirmReplenishmentHandler $confirmReplenishment,
    ) {
    }

    #[Route('/replenishment-policies', name: 'replenishment_policy_create', methods: ['POST'])]
    #[IsGranted('fulfillment.replenishment.write')]
    public function createReplenishmentPolicy(Request $request): Response
    {
        $this->csrf($request, 'v3_replenishment_policy_create');
        $user = $this->user();
        ($this->createReplenishmentPolicy)(new CreateReplenishmentPolicyCommand(Uuid::v7()->toRfc4122(), $user->tenantId(), $this->required($request, 'warehouse_id'), $this->required($request, 'product_id'), $this->required($request, 'target_location_id'), $this->required($request, 'code'), $this->required($request, 'source_location_prefix'), $request->request->getInt('minimum_quantity'), $request->request->getInt('target_quantity'), $request->request->getInt('priority'), $user->actorId(), new DateTimeImmutable()));
        $this->addFlash('success', 'Die Nachschubregel wurde angelegt.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('/replenishment-policies/{policyId}/orders', name: 'replenishment_order_create', methods: ['POST'])]
    #[IsGranted('fulfillment.replenishment.write')]
    public function createReplenishmentOrder(string $policyId, Request $request): Response
    {
        $this->csrf($request, 'v3_replenishment_order_create_' . $policyId);
        $user = $this->user();
        ($this->createReplenishmentOrder)(new CreateReplenishmentOrderCommand(Uuid::v7()->toRfc4122(), $user->tenantId(), $policyId, $user->actorId(), new DateTimeImmutable()));
        $this->addFlash('success', 'Der Nachschubauftrag wurde erzeugt.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('/replenishment-orders/{orderId}/completion', name: 'replenishment_complete', methods: ['POST'])]
    #[IsGranted('fulfillment.replenishment.execute')]
    public function completeReplenishment(string $orderId, Request $request): Response
    {
        $this->csrf($request, 'v3_replenishment_complete_' . $orderId);
        $user = $this->user();
        ($this->confirmReplenishment)(new ConfirmReplenishmentCommand($orderId, Uuid::v7()->toRfc4122(), Uuid::v7()->toRfc4122(), Uuid::v7()->toRfc4122(), $user->tenantId(), $user->actorId(), new DateTimeImmutable()));
        $this->addFlash('success', 'Der Nachschub wurde quittiert.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('fulfillment.transport.read')]
    public function index(): Response
    {
        return $this->render('v3/transport/index.html.twig', ['page' => 'Interner Transport', 'workspace' => $this->transport->workspace($this->user()->tenantId())]);
    }

    #[Route('/orders', name: 'order_create', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.write')]
    public function createOrder(Request $request): Response
    {
        $this->csrf($request, 'v3_transport_order_create');
        $user = $this->user();
        $this->transport->createOrder($user->tenantId(), $user->actorId(), $request->request->all(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Fahrbefehl wurde angelegt.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('/resources/{resource}', name: 'resource_create', requirements: ['resource' => 'forklift|rule|station|milk_run'], methods: ['POST'])]
    #[IsGranted('fulfillment.transport.configure')]
    public function createResource(string $resource, Request $request): Response
    {
        $this->csrf($request, 'v3_transport_resource_create_' . $resource);
        $user = $this->user();
        $this->transport->createResource($user->tenantId(), $user->actorId(), $resource, $request->request->all(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Transportkonfiguration wurde angelegt.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('/orders/{orderId}/assignment', name: 'assign', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.assign')]
    public function assign(string $orderId, Request $request): Response
    {
        $this->csrf($request, 'v3_transport_assign_' . $orderId);
        $user = $this->user();
        $this->transport->assign($user->tenantId(), $user->actorId(), $orderId, $this->required($request, 'forklift_id'), new DateTimeImmutable());
        $this->addFlash('success', 'Der Fahrbefehl wurde zugewiesen.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('/orders/{orderId}/start', name: 'start', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.execute')]
    public function start(string $orderId, Request $request): Response
    {
        $this->csrf($request, 'v3_transport_start_' . $orderId);
        $user = $this->user();
        $this->transport->start($user->tenantId(), $user->actorId(), $orderId, new DateTimeImmutable());
        $this->addFlash('success', 'Der Fahrbefehl wurde gestartet.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('/orders/{orderId}/completion', name: 'complete', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.execute')]
    public function complete(string $orderId, Request $request): Response
    {
        $this->csrf($request, 'v3_transport_complete_' . $orderId);
        $user = $this->user();
        $this->transport->complete($user->tenantId(), $user->actorId(), $orderId, new DateTimeImmutable());
        $this->addFlash('success', 'Der Fahrbefehl wurde quittiert und der Bestand gebucht.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('/milk-runs/{milkRunId}/stops', name: 'milk_run_stop', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.configure')]
    public function addStop(string $milkRunId, Request $request): Response
    {
        $this->csrf($request, 'v3_transport_milk_run_stop_' . $milkRunId);
        $user = $this->user();
        $this->transport->addMilkRunStop($user->tenantId(), $user->actorId(), $milkRunId, $this->required($request, 'station_id'), $request->request->getInt('sequence_number'), $request->request->getInt('dwell_minutes'), new DateTimeImmutable());
        $this->addFlash('success', 'Die Station wurde zur Routenzugtour hinzugefügt.');

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    #[Route('/milk-runs/{milkRunId}/dispatch', name: 'milk_run_dispatch', methods: ['POST'])]
    #[IsGranted('fulfillment.transport.write')]
    public function dispatch(string $milkRunId, Request $request): Response
    {
        $this->csrf($request, 'v3_transport_milk_run_dispatch_' . $milkRunId);
        $user = $this->user();
        $orders = $this->transport->dispatchMilkRun($user->tenantId(), $user->actorId(), $milkRunId, new DateTimeImmutable());
        $this->addFlash('success', sprintf('%d Routenzug-Fahrbefehle wurden erzeugt.', count($orders)));

        return $this->redirectToRoute('v3_internal_transport_index');
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }

    private function csrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
    }
}
