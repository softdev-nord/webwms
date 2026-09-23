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
use WebWMS\Administration\Application\AdministrationWorkspaceService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/administration', name: 'api_v3_administration_')]
final class AdministrationApiController extends AbstractController
{
    public function __construct(
        private readonly AdministrationWorkspaceService $workspace
    ) {
    }

    #[Route('/workspace', name: 'workspace', methods: ['GET'])]
    #[IsGranted('administration.configuration.read')]
    public function workspace(): JsonResponse
    {
        return new JsonResponse(['data' => $this->workspace->workspace($this->user()->tenantId())]);
    }

    #[Route('/workspace/{resource}', name: 'create', requirements: ['resource' => 'partner|context|identity_provider|number_range|device_profile'], methods: ['POST'])]
    #[IsGranted('administration.configuration.write')]
    public function create(string $resource, Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->workspace->create($user->tenantId(), $user->actorId(), $resource, $payload, new DateTimeImmutable());

        return new JsonResponse(['id' => $id], JsonResponse::HTTP_CREATED);
    }

    #[Route('/processes/{processKey}', name: 'process_configure', methods: ['PUT'])]
    #[IsGranted('administration.configuration.write')]
    public function configureProcess(string $processKey, Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $user = $this->user();
        $this->workspace->configureProcess(
            $user->tenantId(),
            $user->actorId(),
            $processKey,
            $this->string($payload, 'name'),
            ($payload['enabled'] ?? false) === true,
            isset($payload['configuration']) ? json_encode($payload['configuration'], JSON_THROW_ON_ERROR) : '{}',
            new DateTimeImmutable(),
        );

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/deployment', name: 'deployment_configure', methods: ['PUT'])]
    #[IsGranted('administration.configuration.write')]
    public function configureDeployment(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $values = [];
        foreach (['deployment_mode', 'public_url', 'storage_driver', 'queue_transport', 'release_channel'] as $field) {
            $values[$field] = $this->string($payload, $field);
        }
        $user = $this->user();
        $this->workspace->configureDeployment($user->tenantId(), $user->actorId(), $values, new DateTimeImmutable());

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/number-ranges/{code}/next', name: 'number_range_next', methods: ['POST'])]
    #[IsGranted('administration.number_range.use')]
    public function nextNumber(string $code): JsonResponse
    {
        $user = $this->user();

        return new JsonResponse(['number' => $this->workspace->nextNumber($user->tenantId(), $user->actorId(), $code, new DateTimeImmutable())]);
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
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return trim($value);
    }
}
