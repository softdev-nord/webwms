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
use WebWMS\Inventory\Application\AssignPickListCommand;
use WebWMS\Inventory\Application\AssignPickListHandler;
use WebWMS\Inventory\Application\ConfirmPickTaskCommand;
use WebWMS\Inventory\Application\ConfirmPickTaskHandler;
use WebWMS\Inventory\Application\CreatePickListCommand;
use WebWMS\Inventory\Application\CreatePickListHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_picking_')]
final class PickingApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreatePickListHandler $createPickList,
        private readonly AssignPickListHandler $assignPickList,
        private readonly ConfirmPickTaskHandler $confirmPickTask
    ) {
    }

    #[Route('/orders/{orderId}/pick-lists', name: 'create', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.write')]
    public function create(string $orderId, Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        if ($this->queries->outboundOrder($this->apiUser()->tenantId(), $orderId) === null) {
            throw $this->createNotFoundException('The outbound order does not exist.');
        }
        $allocationIds = $this->queries->pickableAllocationIds($this->apiUser()->tenantId(), $orderId);
        if ($allocationIds === []) {
            throw new \InvalidArgumentException('The outbound order must be fully allocated and have active allocations available for picking.');
        }
        $pickListId = Uuid::v7()->toRfc4122();
        ($this->createPickList)(new CreatePickListCommand(
            $pickListId,
            $this->apiUser()->tenantId(),
            $orderId,
            $this->string($payload, 'code'),
            $allocationIds,
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data($this->requiredPickList($pickListId), Response::HTTP_CREATED);
    }

    #[Route('/pick-lists/{pickListId}', name: 'get', methods: ['GET'])]
    #[IsGranted('fulfillment.pick.read')]
    public function getPickList(string $pickListId): JsonResponse
    {
        return $this->data($this->requiredPickList($pickListId));
    }

    #[Route('/pick-lists/{pickListId}/assignment', name: 'assign', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.assign')]
    public function assign(string $pickListId, Request $request): JsonResponse
    {
        $this->requiredPickList($pickListId);
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        ($this->assignPickList)(new AssignPickListCommand(
            $pickListId,
            $this->apiUser()->tenantId(),
            $this->string($payload, 'assignedTo'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data($this->requiredPickList($pickListId));
    }

    #[Route('/pick-tasks/{taskId}/confirmation', name: 'confirm', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.execute')]
    public function confirm(string $taskId, Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $outcome = $this->string($payload, 'outcome');
        $result = ($this->confirmPickTask)(new ConfirmPickTaskCommand(
            $taskId,
            $this->apiUser()->tenantId(),
            $outcome,
            $outcome === 'picked' ? Uuid::v7()->toRfc4122() : null,
            $this->string($payload, 'note'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $taskId,
            'status' => $result->taskStatus,
            'pickListStatus' => $result->pickListStatus,
            'reservationStatus' => $result->reservationStatus,
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
    private function requiredPickList(string $pickListId): array
    {
        $pickList = $this->queries->pickList($this->apiUser()->tenantId(), $pickListId);
        if ($pickList === null) {
            throw $this->createNotFoundException('The pick list does not exist.');
        }

        return $pickList;
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
    private function data(array $payload, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $payload], $status);
    }
}
