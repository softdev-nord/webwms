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
use WebWMS\Integration\Application\IntegrationTransportService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/transport-endpoints', name: 'api_v3_transport_')]
final class IntegrationTransportApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly IntegrationTransportService $transport,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.transport.read')]
    public function index(): JsonResponse
    {
        $data = $this->queries->transportEndpoints($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('integration.transport.write')]
    public function create(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $endpoint = $this->transport->register(
            $user->tenantId(),
            $this->string($payload, 'code'),
            $this->string($payload, 'name'),
            $this->string($payload, 'adapterType'),
            $this->string($payload, 'address'),
            $this->string($payload, 'credentialEnv'),
            $this->string($payload, 'protocol'),
            $this->string($payload, 'framing'),
            $this->integer($payload, 'connectTimeoutMs'),
            $this->integer($payload, 'readTimeoutMs'),
            $this->boolean($payload, 'active', true),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->transportEndpoint($user->tenantId(), $endpoint->id)], Response::HTTP_CREATED);
    }

    #[Route('/{endpointId}/status', name: 'status', methods: ['PATCH'])]
    #[IsGranted('integration.transport.write')]
    public function status(string $endpointId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $this->transport->changeStatus($user->tenantId(), $endpointId, $this->boolean($payload, 'active'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->transportEndpoint($user->tenantId(), $endpointId)]);
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
    private function integer(array $payload, string $field): int
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be an integer.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function boolean(array $payload, string $field, ?bool $default = null): bool
    {
        $value = $payload[$field] ?? $default;
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be boolean.', $field));
        }

        return $value;
    }
}
