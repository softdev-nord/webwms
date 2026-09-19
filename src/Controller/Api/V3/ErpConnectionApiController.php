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
use WebWMS\Integration\Application\ChangeErpConnectionStatusCommand;
use WebWMS\Integration\Application\ChangeErpConnectionStatusHandler;
use WebWMS\Integration\Application\RegisterErpConnectionCommand;
use WebWMS\Integration\Application\RegisterErpConnectionHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/erp-connections', name: 'api_v3_erp_connection_')]
final class ErpConnectionApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly RegisterErpConnectionHandler $registerConnection,
        private readonly ChangeErpConnectionStatusHandler $changeStatus
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('integration.erp_connection.read')]
    public function list(): JsonResponse
    {
        $connections = $this->queries->erpConnections($this->apiUser()->tenantId());

        return new JsonResponse(['data' => $connections, 'meta' => ['count' => count($connections)]]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('integration.erp_connection.write')]
    public function create(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $id = Uuid::v7()->toRfc4122();
        $connection = ($this->registerConnection)(new RegisterErpConnectionCommand(
            $id,
            $this->apiUser()->tenantId(),
            $this->requiredString($payload, 'name'),
            $this->requiredString($payload, 'endpointUrl'),
            $this->requiredString($payload, 'credentialEnv'),
            $this->optionalBool($payload, 'active', true),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return new JsonResponse(['data' => [
            'id' => $connection->id,
            'name' => $connection->name,
            'endpointUrl' => $connection->endpointUrl,
            'credentialEnv' => $connection->credentialEnv,
            'active' => $connection->active,
        ]], Response::HTTP_CREATED);
    }

    #[Route('/{connectionId}/status', name: 'status', methods: ['PATCH'])]
    #[IsGranted('integration.erp_connection.write')]
    public function status(string $connectionId, Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        ($this->changeStatus)(new ChangeErpConnectionStatusCommand(
            $connectionId,
            $this->apiUser()->tenantId(),
            $this->requiredBool($payload, 'active'),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        return new JsonResponse(['data' => $this->queries->erpConnection(
            $this->apiUser()->tenantId(),
            $connectionId,
        )]);
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

    /** @param array<string, mixed> $payload */
    private function requiredBool(array $payload, string $field): bool
    {
        $value = $payload[$field] ?? null;
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be boolean.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function optionalBool(array $payload, string $field, bool $default): bool
    {
        return array_key_exists($field, $payload) ? $this->requiredBool($payload, $field) : $default;
    }
}
