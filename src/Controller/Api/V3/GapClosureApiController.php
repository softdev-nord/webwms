<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WebWMS\Platform\Application\GapClosureService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/parity', name: 'api_v3_parity_')]
final class GapClosureApiController extends AbstractController
{
    public function __construct(private readonly GapClosureService $service)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('platform.parity.read')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => $this->service->workspace($this->user()->tenantId())]);
    }

    #[Route('/configurations/{resource}', name: 'configuration_create', methods: ['POST'])]
    #[Route('/configurations/{resource}/{id}', name: 'configuration_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('platform.parity.write')]
    public function configuration(string $resource, Request $request, ?string $id = null): JsonResponse
    {
        $payload = $request->toArray();
        $configuration = $payload['configuration'] ?? null;
        if (!is_array($configuration)) {
            throw new \InvalidArgumentException('Field "configuration" must be an object.');
        }
        $user = $this->user();
        $id = $this->service->saveConfiguration($user->tenantId(), $user->actorId(), $resource, $id, $this->string($payload, 'code'), $this->string($payload, 'name'), $configuration, ($payload['active'] ?? true) === true, new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], $request->isMethod('POST') ? JsonResponse::HTTP_CREATED : JsonResponse::HTTP_OK);
    }

    #[Route('/work-items/{workflow}', name: 'work_item_create', methods: ['POST'])]
    #[IsGranted('platform.parity.execute')]
    public function createWorkItem(string $workflow, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $data = $payload['payload'] ?? null;
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Field "payload" must be an object.');
        }
        $user = $this->user();
        $id = $this->service->createWorkItem($user->tenantId(), $user->actorId(), $workflow, $this->string($payload, 'reference'), $data, new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], JsonResponse::HTTP_CREATED);
    }

    #[Route('/work-items/{id}', name: 'work_item_show', methods: ['GET'])]
    #[IsGranted('platform.parity.read')]
    public function showWorkItem(string $id): JsonResponse
    {
        $item = $this->service->workItem($this->user()->tenantId(), $id);

        return new JsonResponse(['data' => $item, 'meta' => ['allowedTransitions' => $this->service->allowedTransitions((string) $item['workflow_type'], (string) $item['status'])]]);
    }

    #[Route('/work-items/{id}/transition', name: 'work_item_transition', methods: ['POST'])]
    #[IsGranted('platform.parity.execute')]
    public function transition(string $id, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $this->service->transition($user->tenantId(), $user->actorId(), $id, $this->string($payload, 'status'), new DateTimeImmutable());

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
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

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }
}
