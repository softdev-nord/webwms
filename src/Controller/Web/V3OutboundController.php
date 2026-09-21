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
use WebWMS\Inventory\Application\AllocateStockCommand;
use WebWMS\Inventory\Application\AllocateStockHandler;
use WebWMS\Inventory\Application\CreateOutboundOrderCommand;
use WebWMS\Inventory\Application\CreateOutboundOrderHandler;
use WebWMS\Inventory\Application\CreatePickListCommand;
use WebWMS\Inventory\Application\CreatePickListHandler;
use WebWMS\Inventory\Application\ReleaseOutboundOrderCommand;
use WebWMS\Inventory\Application\ReleaseOutboundOrderHandler;
use WebWMS\Inventory\Application\StockSelectionService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/outbound', name: 'v3_outbound_')]
final class V3OutboundController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreateOutboundOrderHandler $createOrder,
        private readonly ReleaseOutboundOrderHandler $releaseOrder,
        private readonly AllocateStockHandler $allocateStock,
        private readonly CreatePickListHandler $createPickList,
        private readonly StockSelectionService $stockSelection,
    ) {
    }

    #[Route('/orders', name: 'orders', methods: ['GET'])]
    #[IsGranted('outbound.order.read')]
    public function orders(): Response
    {
        return $this->render('v3/outbound/orders.html.twig', [
            'orders' => $this->queries->outboundOrders($this->tenantUser()->tenantId()),
            'page' => 'Ausgangsaufträge',
        ]);
    }

    #[Route('/orders/new', name: 'order_new', methods: ['GET', 'POST'])]
    #[IsGranted('outbound.order.write')]
    public function create(Request $request): Response
    {
        $user = $this->tenantUser();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_outbound_order_create');
            $items = $this->orderItems($request);
            $orderId = Uuid::v7()->toRfc4122();
            ($this->createOrder)(new CreateOutboundOrderCommand(
                $orderId,
                $user->tenantId(),
                $this->required($request, 'order_number'),
                $this->required($request, 'customer_reference'),
                $items,
                $user->actorId(),
                new DateTimeImmutable(),
            ));
            $this->addFlash('success', 'Der Ausgangsauftrag wurde angelegt.');

            return $this->redirectToRoute('v3_outbound_order', ['orderId' => $orderId]);
        }

        return $this->render('v3/outbound/order_new.html.twig', [
            'products' => $this->queries->products($user->tenantId(), 200, null),
            'page' => 'Ausgangsauftrag',
        ]);
    }

    #[Route('/orders/{orderId}', name: 'order', methods: ['GET'])]
    #[IsGranted('outbound.order.read')]
    public function order(string $orderId): Response
    {
        $user = $this->tenantUser();
        $order = $this->requiredOrder($user->tenantId(), $orderId);
        foreach ($order['items'] as &$item) {
            if (is_array($item) && is_string($item['product_id'] ?? null)) {
                $item['stock'] = $this->queries->availableStockForProduct($user->tenantId(), $item['product_id']);
            }
        }
        unset($item);

        return $this->render('v3/outbound/order.html.twig', [
            'order' => $order,
            'selectionRules' => $this->queries->stockSelectionRules($user->tenantId()),
            'page' => 'Ausgangsauftrag',
        ]);
    }

    #[Route('/orders/{orderId}/release', name: 'order_release', methods: ['POST'])]
    #[IsGranted('outbound.order.release')]
    public function release(string $orderId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_outbound_order_release_' . $orderId);
        $user = $this->tenantUser();
        $order = $this->requiredOrder($user->tenantId(), $orderId);
        $reservationIds = [];
        foreach ($order['items'] as $item) {
            if (!is_array($item) || !is_string($item['id'] ?? null)) {
                throw new LogicException('The outbound order item projection is invalid.');
            }
            $reservationIds[$item['id']] = Uuid::v7()->toRfc4122();
        }
        ($this->releaseOrder)(new ReleaseOutboundOrderCommand(
            $orderId,
            $user->tenantId(),
            $reservationIds,
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Der Auftrag wurde freigegeben und reserviert.');

        return $this->redirectToRoute('v3_outbound_order', ['orderId' => $orderId]);
    }

    #[Route('/orders/{orderId}/reservations/{reservationId}/auto-allocate', name: 'automatic_allocation', methods: ['POST'])]
    #[IsGranted('inventory.selection.execute')]
    public function automaticallyAllocate(string $orderId, string $reservationId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_outbound_auto_allocate_' . $reservationId);
        $user = $this->tenantUser();
        $this->requiredOrder($user->tenantId(), $orderId);
        $result = $this->stockSelection->allocate(
            $user->tenantId(),
            $reservationId,
            $this->required($request, 'rule_id'),
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', sprintf('%d Einheiten wurden automatisch per %s allokiert.', $result->allocatedQuantity, mb_strtoupper($result->strategy)));

        return $this->redirectToRoute('v3_outbound_order', ['orderId' => $orderId]);
    }

    #[Route('/orders/{orderId}/reservations/{reservationId}/allocate', name: 'allocation', methods: ['POST'])]
    #[IsGranted('inventory.allocation.write')]
    public function allocate(string $orderId, string $reservationId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_outbound_allocate_' . $reservationId);
        $user = $this->tenantUser();
        $reservation = $this->queries->reservation($user->tenantId(), $reservationId);
        if ($reservation === null || !is_string($reservation['product_id'] ?? null)) {
            throw $this->createNotFoundException('Die Reservierung wurde nicht gefunden.');
        }
        ($this->allocateStock)(new AllocateStockCommand(
            Uuid::v7()->toRfc4122(),
            $reservationId,
            $user->tenantId(),
            $reservation['product_id'],
            $this->required($request, 'location_id'),
            $this->positiveInt($request, 'quantity'),
            $user->actorId(),
            new DateTimeImmutable(),
            $this->required($request, 'stock_status'),
            $this->optional($request, 'batch_number'),
            $this->optional($request, 'serial_number'),
            ($expiresAt = $this->optional($request, 'expires_at')) === null ? null : new DateTimeImmutable($expiresAt),
        ));
        $this->addFlash('success', 'Bestand wurde der Reservierung zugeordnet.');

        return $this->redirectToRoute('v3_outbound_order', ['orderId' => $orderId]);
    }

    #[Route('/orders/{orderId}/pick-list', name: 'pick_list_create', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.write')]
    public function createPickList(string $orderId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_outbound_pick_list_' . $orderId);
        $user = $this->tenantUser();
        $this->requiredOrder($user->tenantId(), $orderId);
        $allocationIds = $this->queries->pickableAllocationIds($user->tenantId(), $orderId);
        if ($allocationIds === []) {
            throw new LogicException('Der Auftrag ist noch nicht vollständig allokiert.');
        }
        $pickListId = Uuid::v7()->toRfc4122();
        ($this->createPickList)(new CreatePickListCommand(
            $pickListId,
            $user->tenantId(),
            $orderId,
            $this->required($request, 'code'),
            $allocationIds,
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Die Pickliste wurde erzeugt.');

        return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
    }

    /** @return array<string, mixed> */
    private function requiredOrder(string $tenantId, string $orderId): array
    {
        $order = $this->queries->outboundOrder($tenantId, $orderId);
        if ($order === null) {
            throw $this->createNotFoundException('Der Ausgangsauftrag wurde nicht gefunden.');
        }

        return $order;
    }

    /** @return list<array{id: string, productId: string, quantity: int}> */
    private function orderItems(Request $request): array
    {
        $productIds = $request->request->all('product_id');
        $quantities = $request->request->all('quantity');
        $items = [];
        foreach ($productIds as $index => $productId) {
            $quantity = $quantities[$index] ?? null;
            if (!is_string($productId) || !is_string($quantity) || (int) $quantity <= 0) {
                throw new \InvalidArgumentException('Jede Auftragsposition benötigt einen Artikel und eine positive Menge.');
            }
            $items[] = ['id' => Uuid::v7()->toRfc4122(), 'productId' => $productId, 'quantity' => (int) $quantity];
        }
        if ($items === []) {
            throw new \InvalidArgumentException('Mindestens eine Auftragsposition ist erforderlich.');
        }

        return $items;
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

    private function positiveInt(Request $request, string $field): int
    {
        $value = $this->required($request, $field);
        if (!ctype_digit($value) || (int) $value <= 0) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" muss eine positive Ganzzahl sein.', $field));
        }

        return (int) $value;
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
