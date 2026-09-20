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
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Inventory\Application\UnplannedReceiptService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/unplanned-receipts', name: 'api_v3_inbound_')]
final class InboundApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly UnplannedReceiptService $receipts,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('inbound.receipt.read')]
    public function index(): JsonResponse
    {
        $data = $this->queries->unplannedReceipts($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function create(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $items = $payload['items'] ?? null;
        if (!is_array($items)) {
            throw new \InvalidArgumentException('Field "items" must be an array.');
        }
        /** @var list<array{productId: string, locationId: string, quantity: int, status: string, batchNumber: string|null, serialNumber: string|null}> $normalized */
        $normalized = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                throw new \InvalidArgumentException('Each receipt item must be an object.');
            }
            $normalized[] = [
                'productId' => $this->string($item, 'productId'),
                'locationId' => $this->string($item, 'locationId'),
                'quantity' => $this->integer($item, 'quantity'),
                'status' => $this->optionalString($item, 'status') ?? 'available',
                'batchNumber' => $this->optionalString($item, 'batchNumber'),
                'serialNumber' => $this->optionalString($item, 'serialNumber'),
            ];
        }
        $user = $this->user();
        $receipt = $this->receipts->accept($user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'supplierCode'), $this->string($payload, 'supplierName'), $this->optionalString($payload, 'deliveryNote'), $normalized, $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->unplannedReceipt($user->tenantId(), $receipt->id()->value())], Response::HTTP_CREATED);
    }

    #[Route('/{receiptId}/book', name: 'book', methods: ['POST'])]
    #[IsGranted('inbound.receipt.book')]
    public function book(string $receiptId): JsonResponse
    {
        $user = $this->user();
        $this->receipts->book($user->tenantId(), $receiptId, $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->unplannedReceipt($user->tenantId(), $receiptId)]);
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
    private function integer(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be an integer.', $field));
        }

        return $value;
    }
}
