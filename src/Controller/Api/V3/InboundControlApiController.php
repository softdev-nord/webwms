<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

#[Route('/api/v3/inbound/control', name: 'api_v3_inbound_control_')]
final class InboundControlApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreatePurchaseOrderHandler $createPurchaseOrder,
        private readonly CreateInboundDeliveryHandler $createDelivery,
        private readonly CreateReturnOrderHandler $createReturn,
        private readonly ReceiveReturnHandler $receiveReturn,
        private readonly InspectReturnHandler $inspectReturn,
        private readonly InboundProcessService $processes,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('inbound.planned.read')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->inboundControlCenter($this->user()->tenantId())]);
    }

    #[Route('/purchase-orders', name: 'purchase_order', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function purchaseOrder(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        ($this->createPurchaseOrder)(new CreatePurchaseOrderCommand($id, $user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'supplierReference'), [['id' => Uuid::v7()->toRfc4122(), 'productId' => $this->string($payload, 'productId'), 'quantity' => $this->integer($payload, 'quantity')]], $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/deliveries', name: 'delivery', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function delivery(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        ($this->createDelivery)(new CreateInboundDeliveryCommand($id, $user->tenantId(), $this->string($payload, 'purchaseOrderId'), $this->string($payload, 'code'), $this->string($payload, 'deliveryNote'), new DateTimeImmutable($this->string($payload, 'expectedAt')), [['id' => Uuid::v7()->toRfc4122(), 'purchaseOrderItemId' => $this->string($payload, 'purchaseOrderItemId'), 'quantity' => $this->integer($payload, 'quantity')]], $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/returns', name: 'return', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function createReturn(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = Uuid::v7()->toRfc4122();
        ($this->createReturn)(new CreateReturnOrderCommand($id, $user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'orderReference'), [['id' => Uuid::v7()->toRfc4122(), 'productId' => $this->string($payload, 'productId'), 'quantity' => $this->integer($payload, 'quantity'), 'reason' => $this->string($payload, 'reason')]], $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/returns/{orderId}/items/{itemId}/receive', name: 'return_receive', methods: ['POST'])]
    #[IsGranted('inbound.planned.receive')]
    public function receiveReturn(string $orderId, string $itemId): JsonResponse
    {
        $user = $this->user();
        $receiptId = Uuid::v7()->toRfc4122();
        $result = ($this->receiveReturn)(new ReceiveReturnCommand($receiptId, $user->tenantId(), $orderId, $itemId, $user->actorId(), new DateTimeImmutable()));

        return new JsonResponse(['data' => $result]);
    }

    #[Route('/returns/receipts/{receiptId}/inspect', name: 'return_inspect', methods: ['POST'])]
    #[IsGranted('inbound.planned.inspect')]
    public function inspectReturn(string $receiptId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $result = ($this->inspectReturn)(new InspectReturnCommand($receiptId, Uuid::v7()->toRfc4122(), $user->tenantId(), $this->string($payload, 'locationId'), $this->string($payload, 'decision'), $this->string($payload, 'note'), $user->actorId(), new DateTimeImmutable(), $this->optionalString($payload, 'batchNumber'), $this->optionalString($payload, 'serialNumber'), ($expiresAt = $this->optionalString($payload, 'expiresAt')) === null ? null : new DateTimeImmutable($expiresAt)));

        return new JsonResponse(['data' => $result]);
    }

    #[Route('/checklists', name: 'checklist', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function checklist(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $questions = $payload['questions'] ?? null;
        if (!is_array($questions)) {
            throw new \InvalidArgumentException('Field "questions" must be an array.');
        }
        $user = $this->user();
        $id = $this->processes->createChecklist($user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'name'), array_map('strval', $questions), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/attachments', name: 'attachment', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function attachment(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $content = base64_decode($this->string($payload, 'content'), true);
        if ($content === false) {
            throw new \InvalidArgumentException('Field "content" must contain valid base64.');
        }
        $user = $this->user();
        $id = $this->processes->attach($user->tenantId(), $this->string($payload, 'aggregateType'), $this->string($payload, 'aggregateId'), $this->string($payload, 'category'), $this->string($payload, 'name'), $this->string($payload, 'mediaType'), $content, $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/labels', name: 'label', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function label(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->processes->requestLabel($user->tenantId(), $this->string($payload, 'aggregateType'), $this->string($payload, 'aggregateId'), $this->string($payload, 'labelType'), $this->integer($payload, 'copies'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/cross-dock', name: 'cross_dock', methods: ['POST'])]
    #[IsGranted('inbound.planned.putaway')]
    public function crossDock(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->processes->assignCrossDock($user->tenantId(), $this->string($payload, 'receiptId'), $this->string($payload, 'outboundItemId'), $this->integer($payload, 'quantity'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/production', name: 'production', methods: ['POST'])]
    #[IsGranted('inbound.receipt.book')]
    public function production(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->processes->receiveProduction($user->tenantId(), $this->string($payload, 'productionOrder'), $this->string($payload, 'productId'), $this->string($payload, 'locationId'), $this->integer($payload, 'quantity'), $this->optionalString($payload, 'batchNumber'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }

    /** @param array<string, mixed> $payload */
    private function string(array $payload, string $field): string
    {
        $value = $payload[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a non-empty string.', $field));
        }

        return trim($value);
    }

    /** @param array<string, mixed> $payload */
    private function optionalString(array $payload, string $field): ?string
    {
        $value = $payload[$field] ?? null;

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    /** @param array<string, mixed> $payload */
    private function integer(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be an integer.', $field));
        }

        return $value;
    }
}
