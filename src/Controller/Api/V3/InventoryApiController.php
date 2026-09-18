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
use WebWMS\Inventory\Application\RegisterProductCommand;
use WebWMS\Inventory\Application\RegisterProductHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_')]
final class InventoryApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly RegisterProductHandler $registerProduct
    ) {
    }

    #[Route('/meta', name: 'meta', methods: ['GET'])]
    public function meta(): JsonResponse
    {
        return $this->data(['name' => 'WebWMS API', 'version' => '3.0']);
    }

    #[Route('/products', name: 'products', methods: ['GET'])]
    #[IsGranted('inventory.product.read')]
    public function products(Request $request): JsonResponse
    {
        $items = $this->queries->products(
            $this->apiUser()->tenantId(),
            $this->limit($request),
            $this->optionalQueryString($request, 'cursor'),
        );

        return $this->collection($items, 'id');
    }

    #[Route('/products', name: 'product_create', methods: ['POST'])]
    #[IsGranted('inventory.product.write')]
    public function createProduct(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $sku = $this->requiredString($payload, 'sku');
        $name = $this->requiredString($payload, 'name');
        $productId = Uuid::v7()->toRfc4122();
        $product = ($this->registerProduct)(new RegisterProductCommand(
            $productId,
            $this->apiUser()->tenantId(),
            $sku,
            $name,
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $product->id()->value(),
            'sku' => $product->sku()->value(),
            'name' => $product->name(),
            'createdAt' => $product->createdAt()->format(DATE_ATOM),
        ], Response::HTTP_CREATED);
    }

    #[Route('/warehouses', name: 'warehouses', methods: ['GET'])]
    #[IsGranted('inventory.location.read')]
    public function warehouses(): JsonResponse
    {
        return $this->collection($this->queries->warehouses($this->apiUser()->tenantId()), null);
    }

    #[Route('/stock', name: 'stock', methods: ['GET'])]
    #[IsGranted('inventory.stock.read')]
    public function stock(Request $request): JsonResponse
    {
        $items = $this->queries->stock(
            $this->apiUser()->tenantId(),
            $this->optionalQueryString($request, 'warehouseId'),
            $this->limit($request),
            $this->optionalQueryString($request, 'cursor'),
        );

        return $this->collection($items, 'cursor');
    }

    private function apiUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
        }

        return $user;
    }

    /** @param array<string, mixed> $payload */
    private function requiredString(array $payload, string $field): string
    {
        $value = $payload[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a non-empty string.', $field));
        }

        return $value;
    }

    private function limit(Request $request): int
    {
        $limit = $request->query->getInt('limit', 50);
        if ($limit < 1 || $limit > 100) {
            throw new \InvalidArgumentException('The limit must be between 1 and 100.');
        }

        return $limit;
    }

    private function optionalQueryString(Request $request, string $name): ?string
    {
        $value = $request->query->getString($name);

        return $value === '' ? null : $value;
    }

    /** @param array<string, mixed> $payload */
    private function data(array $payload, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $payload], $status);
    }

    /** @param list<array<string, mixed>> $items */
    private function collection(array $items, ?string $cursorField): JsonResponse
    {
        $last = $items === [] || $cursorField === null
            ? null
            : $items[array_key_last($items)][$cursorField] ?? null;

        return new JsonResponse(['data' => $items, 'meta' => [
            'count' => count($items),
            'nextCursor' => is_string($last) ? $last : null,
        ]]);
    }
}
