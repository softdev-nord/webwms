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
use WebWMS\Inventory\Application\SpecialStockService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/inventory', name: 'api_v3_inventory_traceability_')]
final class TraceabilityApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly SpecialStockService $specialStock,
    ) {
    }

    #[Route('/traceability', name: 'index', methods: ['GET'])]
    #[IsGranted('inventory.traceability.read')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->traceability($this->user()->tenantId())]);
    }

    #[Route('/traceability/{dimension}/{value}', name: 'events', requirements: ['dimension' => 'batch|serial'], methods: ['GET'])]
    #[IsGranted('inventory.traceability.read')]
    public function events(string $dimension, string $value): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->traceabilityEvents($this->user()->tenantId(), $dimension, $value)]);
    }

    #[Route('/special-stock-types', name: 'special_stock_types', methods: ['GET'])]
    #[IsGranted('inventory.special_stock.read')]
    public function types(): JsonResponse
    {
        return new JsonResponse(['data' => $this->queries->specialStockTypes($this->user()->tenantId())]);
    }

    #[Route('/special-stock-types', name: 'special_stock_type_create', methods: ['POST'])]
    #[IsGranted('inventory.special_stock.write')]
    public function createType(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $id = Uuid::v7()->toRfc4122();
        $user = $this->user();
        $this->specialStock->createType($id, $user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'kind'), $this->boolean($payload, 'allocatable'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], Response::HTTP_CREATED);
    }

    #[Route('/stock-classifications', name: 'stock_classify', methods: ['PUT'])]
    #[IsGranted('inventory.special_stock.write')]
    public function classify(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $user = $this->user();
        $this->specialStock->classify($user->tenantId(), $this->string($payload, 'productId'), $this->string($payload, 'locationId'), $this->string($payload, 'stockKey'), $this->string($payload, 'specialStockTypeId'), $this->optionalString($payload, 'ownerReference'), $this->string($payload, 'reason'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['classified' => true]]);
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
