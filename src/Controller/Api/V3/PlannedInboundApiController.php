<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Inventory\Application\ConfirmPutawayCommand;
use WebWMS\Inventory\Application\ConfirmPutawayHandler;
use WebWMS\Inventory\Application\CreatePutawayOrderCommand;
use WebWMS\Inventory\Application\CreatePutawayOrderHandler;
use WebWMS\Inventory\Application\InspectInboundReceiptCommand;
use WebWMS\Inventory\Application\InspectInboundReceiptHandler;
use WebWMS\Inventory\Application\ReceiveInboundDeliveryCommand;
use WebWMS\Inventory\Application\ReceiveInboundDeliveryHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/inbound/planned', name: 'api_v3_planned_inbound_')]
final class PlannedInboundApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly ReceiveInboundDeliveryHandler $receiveInbound,
        private readonly InspectInboundReceiptHandler $inspectInbound,
        private readonly CreatePutawayOrderHandler $createPutaway,
        private readonly ConfirmPutawayHandler $confirmPutaway,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('inbound.planned.read')]
    public function index(): JsonResponse
    {
        $data = $this->queries->plannedInboundWorklist($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/{deliveryId}/lines/{lineId}/receive', name: 'receive', methods: ['POST'])]
    #[IsGranted('inbound.planned.receive')]
    public function receive(string $deliveryId, string $lineId): JsonResponse
    {
        $user = $this->user();
        $result = ($this->receiveInbound)(new ReceiveInboundDeliveryCommand(
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $deliveryId,
            $lineId,
            $user->actorId(),
            new DateTimeImmutable(),
        ));

        return new JsonResponse(['data' => $result]);
    }

    #[Route('/receipts/{receiptId}/inspect', name: 'inspect', methods: ['POST'])]
    #[IsGranted('inbound.planned.inspect')]
    public function inspect(string $receiptId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $answers = $payload['answers'] ?? null;
        if (!is_array($answers) || $answers === []) {
            throw new \InvalidArgumentException('Field "answers" must be a non-empty array.');
        }
        /** @var list<array{question: string, passed: bool, note: string}> $normalized */
        $normalized = [];
        foreach ($answers as $answer) {
            if (!is_array($answer) || !is_bool($answer['passed'] ?? null)) {
                throw new \InvalidArgumentException('Each answer requires a boolean "passed" field.');
            }
            $normalized[] = [
                'question' => $this->string($answer, 'question'),
                'passed' => $answer['passed'],
                'note' => $this->optionalString($answer, 'note') ?? '',
            ];
        }
        $user = $this->user();
        $result = ($this->inspectInbound)(new InspectInboundReceiptCommand(
            $receiptId,
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $this->string($payload, 'locationId'),
            $this->string($payload, 'decision'),
            $normalized,
            $user->actorId(),
            new DateTimeImmutable(),
            $this->optionalString($payload, 'batchNumber'),
            $this->optionalString($payload, 'serialNumber'),
            ($expiresAt = $this->optionalString($payload, 'expiresAt')) === null ? null : new DateTimeImmutable($expiresAt),
        ));

        return new JsonResponse(['data' => $result]);
    }

    #[Route('/receipts/{receiptId}/putaway', name: 'putaway', methods: ['POST'])]
    #[IsGranted('inbound.planned.putaway')]
    public function putaway(string $receiptId): JsonResponse
    {
        $user = $this->user();
        $result = ($this->createPutaway)(new CreatePutawayOrderCommand(
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $receiptId,
            $user->actorId(),
            new DateTimeImmutable(),
        ));

        return new JsonResponse(['data' => $result]);
    }

    #[Route('/putaway/{orderId}/confirm', name: 'confirm_putaway', methods: ['POST'])]
    #[IsGranted('inbound.planned.putaway')]
    public function confirmPutaway(string $orderId): JsonResponse
    {
        $user = $this->user();
        $result = ($this->confirmPutaway)(new ConfirmPutawayCommand(
            $orderId,
            Uuid::v7()->toRfc4122(),
            Uuid::v7()->toRfc4122(),
            Uuid::v7()->toRfc4122(),
            $user->tenantId(),
            $user->actorId(),
            new DateTimeImmutable(),
        ));

        return new JsonResponse(['data' => $result]);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
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
}
