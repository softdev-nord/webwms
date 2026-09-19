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
use WebWMS\Inventory\Application\CompleteLoadingManifestCommand;
use WebWMS\Inventory\Application\CompleteLoadingManifestHandler;
use WebWMS\Inventory\Application\ConfirmShipmentLoadingCommand;
use WebWMS\Inventory\Application\ConfirmShipmentLoadingHandler;
use WebWMS\Inventory\Application\CreateLoadingManifestCommand;
use WebWMS\Inventory\Application\CreateLoadingManifestHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/loading-manifests', name: 'api_v3_loading_')]
final class LoadingApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreateLoadingManifestHandler $createManifest,
        private readonly ConfirmShipmentLoadingHandler $confirmLoading,
        private readonly CompleteLoadingManifestHandler $completeManifest
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.write')]
    public function create(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $manifestId = Uuid::v7()->toRfc4122();
        ($this->createManifest)(new CreateLoadingManifestCommand(
            $manifestId,
            $this->apiUser()->tenantId(),
            $this->string($payload, 'code'),
            $this->string($payload, 'tourReference'),
            $this->string($payload, 'vehicleReference'),
            $this->stringList($payload, 'shipmentIds'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data($this->requiredManifest($manifestId), Response::HTTP_CREATED);
    }

    #[Route('/{manifestId}', name: 'get', methods: ['GET'])]
    #[IsGranted('fulfillment.loading.read')]
    public function getManifest(string $manifestId): JsonResponse
    {
        return $this->data($this->requiredManifest($manifestId));
    }

    #[Route('/{manifestId}/shipments/{shipmentId}/loading', name: 'confirm', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.execute')]
    public function confirm(string $manifestId, string $shipmentId): JsonResponse
    {
        $this->requiredManifest($manifestId);
        $result = ($this->confirmLoading)(new ConfirmShipmentLoadingCommand(
            $manifestId,
            $this->apiUser()->tenantId(),
            $shipmentId,
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $manifestId,
            'status' => $result->status,
            'loadedShipments' => $result->loadedShipments,
            'totalShipments' => $result->totalShipments,
        ]);
    }

    #[Route('/{manifestId}/complete', name: 'complete', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.execute')]
    public function complete(string $manifestId): JsonResponse
    {
        $this->requiredManifest($manifestId);
        $result = ($this->completeManifest)(new CompleteLoadingManifestCommand(
            $manifestId,
            $this->apiUser()->tenantId(),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return $this->data([
            'id' => $manifestId,
            'status' => $result->status,
            'loadedShipments' => $result->loadedShipments,
            'totalShipments' => $result->totalShipments,
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
    private function requiredManifest(string $manifestId): array
    {
        $manifest = $this->queries->loadingManifest($this->apiUser()->tenantId(), $manifestId);
        if ($manifest === null) {
            throw $this->createNotFoundException('The loading manifest does not exist.');
        }

        return $manifest;
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
