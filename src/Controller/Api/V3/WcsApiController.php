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
use WebWMS\Integration\Application\WcsIntegrationService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_wcs_')]
final class WcsApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly WcsIntegrationService $wcs,
    ) {
    }

    #[Route('/wcs-connections', name: 'connections', methods: ['GET'])]
    #[IsGranted('integration.wcs.read')]
    public function connections(): JsonResponse
    {
        $data = $this->queries->wcsConnections($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/wcs-connections', name: 'connection_create', methods: ['POST'])]
    #[IsGranted('integration.wcs.write')]
    public function createConnection(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $connection = $this->wcs->registerConnection($user->tenantId(), $this->string($payload, 'code'), $this->string($payload, 'name'), $this->string($payload, 'systemType'), $this->string($payload, 'endpointUrl'), $this->string($payload, 'credentialEnv'), $this->boolean($payload, 'active', true), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->wcsConnection($user->tenantId(), $connection->id)], Response::HTTP_CREATED);
    }

    #[Route('/wcs-connections/{connectionId}/status', name: 'connection_status', methods: ['PATCH'])]
    #[IsGranted('integration.wcs.write')]
    public function connectionStatus(string $connectionId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $this->wcs->changeConnectionStatus($user->tenantId(), $connectionId, $this->boolean($payload, 'active'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->wcsConnection($user->tenantId(), $connectionId)]);
    }

    #[Route('/machine-commands', name: 'commands', methods: ['GET'])]
    #[IsGranted('integration.wcs.read')]
    public function commands(): JsonResponse
    {
        $data = $this->queries->machineCommands($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/machine-commands', name: 'command_create', methods: ['POST'])]
    #[IsGranted('integration.wcs.execute')]
    public function createCommand(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $command = $this->wcs->queueCommand($user->tenantId(), $this->string($payload, 'connectionId'), $this->string($payload, 'commandType'), $this->string($payload, 'source'), $this->string($payload, 'destination'), $this->string($payload, 'loadUnit'), $this->string($payload, 'requestId'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->machineCommand($user->tenantId(), $command->id)], Response::HTTP_CREATED);
    }

    #[Route('/machine-commands/{commandId}/status', name: 'command_status', methods: ['PATCH'])]
    #[IsGranted('integration.wcs.execute')]
    public function commandStatus(string $commandId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $command = $this->wcs->transitionCommand($user->tenantId(), $commandId, $this->string($payload, 'status'), $this->optionalString($payload, 'message'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->machineCommand($user->tenantId(), $command->id)]);
    }

    #[Route('/machine-statuses', name: 'statuses', methods: ['GET'])]
    #[IsGranted('integration.wcs.read')]
    public function statuses(): JsonResponse
    {
        $data = $this->queries->machineStatuses($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/machine-statuses', name: 'status_create', methods: ['POST'])]
    #[IsGranted('integration.wcs.execute')]
    public function createStatus(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $status = $this->wcs->recordStatus($user->tenantId(), $this->string($payload, 'connectionId'), $this->optionalString($payload, 'commandId'), $this->string($payload, 'machineCode'), $this->string($payload, 'status'), $this->optionalString($payload, 'message'), $this->string($payload, 'externalEventId'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $status->id]], Response::HTTP_CREATED);
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
    private function boolean(array $payload, string $field, ?bool $default = null): bool
    {
        $value = $payload[$field] ?? $default;
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be boolean.', $field));
        }

        return $value;
    }
}
