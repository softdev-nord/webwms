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
use WebWMS\Inventory\Application\AddPackingPackageCommand;
use WebWMS\Inventory\Application\AddPackingPackageHandler;
use WebWMS\Inventory\Application\CompletePackingOrderCommand;
use WebWMS\Inventory\Application\CompletePackingOrderHandler;
use WebWMS\Inventory\Application\CreatePackingOrderCommand;
use WebWMS\Inventory\Application\CreatePackingOrderHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_packing_')]
final class PackingApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreatePackingOrderHandler $createPackingOrder,
        private readonly AddPackingPackageHandler $addPackingPackage,
        private readonly CompletePackingOrderHandler $completePackingOrder
    ) {
    }

    #[Route('/pick-lists/{pickListId}/packing-orders', name: 'create', methods: ['POST'])]
    #[IsGranted('fulfillment.pack.write')]
    public function create(string $pickListId, Request $request): JsonResponse
    {
        if ($this->queries->pickList($this->apiUser()->tenantId(), $pickListId) === null) {
            throw $this->createNotFoundException('The pick list does not exist.');
        }
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $packingOrderId = Uuid::v7()->toRfc4122();
        ($this->createPackingOrder)(new CreatePackingOrderCommand(
            $packingOrderId,
            $this->apiUser()->tenantId(),
            $pickListId,
            $this->string($payload, 'code'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data($this->requiredPackingOrder($packingOrderId), Response::HTTP_CREATED);
    }

    #[Route('/packing-orders/{packingOrderId}', name: 'get', methods: ['GET'])]
    #[IsGranted('fulfillment.pack.read')]
    public function getPackingOrder(string $packingOrderId): JsonResponse
    {
        return $this->data($this->requiredPackingOrder($packingOrderId));
    }

    #[Route('/packing-orders/{packingOrderId}/packages', name: 'package', methods: ['POST'])]
    #[IsGranted('fulfillment.pack.write')]
    public function addPackage(string $packingOrderId, Request $request): JsonResponse
    {
        $this->requiredPackingOrder($packingOrderId);
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $packageId = Uuid::v7()->toRfc4122();
        ($this->addPackingPackage)(new AddPackingPackageCommand(
            $packageId,
            $packingOrderId,
            $this->apiUser()->tenantId(),
            $this->string($payload, 'packageNumber'),
            $this->positiveInt($payload, 'weightGrams'),
            $this->stringList($payload, 'pickTaskIds'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data($this->requiredPackingOrder($packingOrderId), Response::HTTP_CREATED);
    }

    #[Route('/packing-orders/{packingOrderId}/complete', name: 'complete', methods: ['POST'])]
    #[IsGranted('fulfillment.pack.execute')]
    public function complete(string $packingOrderId): JsonResponse
    {
        $this->requiredPackingOrder($packingOrderId);
        $result = ($this->completePackingOrder)(new CompletePackingOrderCommand(
            $packingOrderId,
            $this->apiUser()->tenantId(),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $packingOrderId,
            'status' => $result->status,
            'packageCount' => $result->packageCount,
            'totalWeightGrams' => $result->totalWeightGrams,
        ]);
    }

    private function apiUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
        }

        return $user;
    }

    /** @return array<string, mixed> */
    private function requiredPackingOrder(string $packingOrderId): array
    {
        $order = $this->queries->packingOrder($this->apiUser()->tenantId(), $packingOrderId);
        if ($order === null) {
            throw $this->createNotFoundException('The packing order does not exist.');
        }

        return $order;
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
    private function positiveInt(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value) || $value <= 0) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a positive integer.', $field));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return list<string>
     */
    private function stringList(array $payload, string $field): array
    {
        $value = $payload[$field] ?? null;
        if (!is_array($value) || $value === []) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a non-empty array.', $field));
        }
        $items = [];
        foreach ($value as $item) {
            if (!is_string($item) || trim($item) === '') {
                throw new \InvalidArgumentException(sprintf('Every value in field "%s" must be a non-empty string.', $field));
            }
            $items[] = trim($item);
        }

        return $items;
    }

    /** @param array<string, mixed> $payload */
    private function data(array $payload, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $payload], $status);
    }
}
