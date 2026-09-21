<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Inventory\Application\StockBlockingService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/inventory', name: 'api_v3_inventory_stock_block_')]
final class StockBlockingApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly StockBlockingService $stockBlocking,
    ) {
    }

    #[Route('/stock-block-reasons', name: 'reasons', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.read')]
    public function reasons(): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->stockBlockReasons($this->user()->tenantId())]);
    }

    #[Route('/stock-block-reasons', name: 'reason_create', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.write')]
    public function createReason(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $id = Uuid::v7()->toRfc4122();
        $user = $this->user();
        $this->stockBlocking->createReason($id, $user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->optionalString($payload, 'description'), $this->boolean($payload, 'active'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/stock-blocks', name: 'index', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.read')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->stockBlocks($this->user()->tenantId())]);
    }

    #[Route('/stock-blocks/{blockId}/events', name: 'events', methods: ['GET'])]
    #[IsGranted('inventory.stock_block.read')]
    public function events(string $blockId): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->stockBlockEvents($this->user()->tenantId(), $blockId)]);
    }

    #[Route('/stock-blocks', name: 'create', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.write')]
    public function create(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $id = Uuid::v7()->toRfc4122();
        $user = $this->user();
        $this->stockBlocking->block(
            $id,
            $user->tenantId(),
            $this->string($payload, 'reasonId'),
            $this->string($payload, 'productId'),
            $this->string($payload, 'locationId'),
            $this->string($payload, 'status'),
            $this->optionalString($payload, 'batchNumber'),
            $this->optionalString($payload, 'serialNumber'),
            ($expiresAt = $this->optionalString($payload, 'expiresAt')) === null ? null : new DateTimeImmutable($expiresAt),
            $this->positiveInt($payload, 'quantity'),
            $this->string($payload, 'note'),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => ['id' => $id, 'status' => 'open']], Response::HTTP_CREATED);
    }

    #[Route('/stock-blocks/{blockId}/review', name: 'review', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.review')]
    public function review(string $blockId, Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $this->stockBlocking->review($user->tenantId(), $blockId, $this->string($payload, 'note'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $blockId, 'status' => 'reviewed']]);
    }

    #[Route('/stock-blocks/{blockId}/release', name: 'release', methods: ['POST'])]
    #[IsGranted('inventory.stock_block.release')]
    public function release(string $blockId, Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $this->stockBlocking->release($user->tenantId(), $blockId, $this->string($payload, 'note'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $blockId, 'status' => 'released']]);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
        }

        return $user;
    }

    /** @return array<string, mixed> */
    private function payload(Request $request): array
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        return $payload;
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
    private function boolean(array $payload, string $field): bool
    {
        $value = $payload[$field] ?? null;
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a boolean.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function positiveInt(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value) || $value < 1) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a positive integer.', $field));
        }

        return $value;
    }
}
