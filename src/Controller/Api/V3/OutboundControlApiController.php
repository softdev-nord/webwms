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
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Inventory\Application\OutboundProcessService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/outbound/control', name: 'api_v3_outbound_control_')]
final class OutboundControlApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly OutboundProcessService $processes
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('outbound.order.read')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->outboundControlCenter($this->user()->tenantId())]);
    }

    #[Route('/orders/{orderId}/cancel', name: 'cancel', methods: ['POST'])]
    #[IsGranted('outbound.order.write')]
    public function cancel(string $orderId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $this->processes->cancelOrder($user->tenantId(), $orderId, $this->string($payload, 'reason'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/quality-checks', name: 'quality', methods: ['POST'])]
    #[IsGranted('fulfillment.pack.execute')]
    public function quality(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->processes->inspectPickList($user->tenantId(), $this->string($payload, 'pickListId'), $this->boolean($payload, 'completenessPassed'), $this->boolean($payload, 'conditionPassed'), $this->boolean($payload, 'customerCheckPassed'), $this->optionalString($payload, 'note') ?? '', $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/shipping-rules', name: 'shipping_rule', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.write')]
    public function shippingRule(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->processes->createShippingRule($user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'carrier'), $this->string($payload, 'service'), $this->integer($payload, 'minWeightGrams'), $this->integer($payload, 'maxWeightGrams'), $this->integer($payload, 'priority'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/shipping-rules/select/{weight}', name: 'shipping_rule_select', methods: ['GET'])]
    #[IsGranted('fulfillment.ship.read')]
    public function selectShippingRule(int $weight): JsonResponse
    {
        return new JsonResponse(['data' => $this->processes->selectShippingRule($this->user()->tenantId(), $weight)]);
    }

    #[Route('/tracking-events', name: 'tracking', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.write')]
    public function tracking(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->processes->recordTracking($user->tenantId(), $this->string($payload, 'shipmentId'), $this->string($payload, 'status'), $this->optionalString($payload, 'location'), $this->string($payload, 'description'), $this->optionalString($payload, 'source') ?? 'api', new DateTimeImmutable($this->string($payload, 'occurredAt')), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/documents', name: 'document', methods: ['POST'])]
    #[IsGranted('fulfillment.ship.write')]
    public function document(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->processes->generateDocument($user->tenantId(), $this->string($payload, 'aggregateType'), $this->string($payload, 'aggregateId'), $this->string($payload, 'documentType'), $this->string($payload, 'documentNumber'), $this->string($payload, 'content'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/tours', name: 'tour', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.write')]
    public function tour(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $stops = $payload['stops'] ?? null;
        if (!is_array($stops)) {
            throw new \InvalidArgumentException('Field "stops" must be an array.');
        }
        /** @var list<array{destinationName: string, destinationAddress: string, shipmentId: string|null}> $normalized */
        $normalized = [];
        foreach ($stops as $stop) {
            if (!is_array($stop)) {
                throw new \InvalidArgumentException('Each tour stop must be an object.');
            }
            $normalized[] = ['destinationName' => $this->string($stop, 'destinationName'), 'destinationAddress' => $this->string($stop, 'destinationAddress'), 'shipmentId' => $this->optionalString($stop, 'shipmentId')];
        }
        $user = $this->user();
        $id = $this->processes->createTour($user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'carrier'), $this->string($payload, 'vehicleReference'), $this->integer($payload, 'maxWeightGrams'), new DateTimeImmutable($this->string($payload, 'departureAt')), $normalized, $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/weight-constraints', name: 'weight', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.write')]
    public function weight(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->processes->createWeightConstraint($user->tenantId(), $this->string($payload, 'scope'), $this->optionalString($payload, 'referenceCode'), $this->integer($payload, 'maxWeightGrams'), $user->actorId(), new DateTimeImmutable());

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

    /** @param array<string, mixed> $payload */
    private function boolean(array $payload, string $field): bool
    {
        $value = $payload[$field] ?? null;
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a boolean.', $field));
        }

        return $value;
    }
}
