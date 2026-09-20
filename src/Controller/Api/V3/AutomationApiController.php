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
use WebWMS\Integration\Application\StorageAutomationAdapter;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_automation_')]
final class AutomationApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly StorageAutomationAdapter $automation,
    ) {
    }

    #[Route('/automation-devices', name: 'devices', methods: ['GET'])]
    #[IsGranted('integration.automation.read')]
    public function devices(): JsonResponse
    {
        $data = $this->queries->automationDevices($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/automation-devices', name: 'device_create', methods: ['POST'])]
    #[IsGranted('integration.automation.write')]
    public function createDevice(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $device = $this->automation->registerDevice(
            $user->tenantId(),
            $this->string($payload, 'code'),
            $this->string($payload, 'name'),
            $this->string($payload, 'type'),
            $this->string($payload, 'endpointUrl'),
            $this->string($payload, 'credentialEnv'),
            $this->boolean($payload, 'active', true),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->automationDevice($user->tenantId(), $device->id)], Response::HTTP_CREATED);
    }

    #[Route('/automation-devices/{deviceId}/status', name: 'device_status', methods: ['PATCH'])]
    #[IsGranted('integration.automation.write')]
    public function deviceStatus(string $deviceId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $this->automation->changeDeviceStatus($user->tenantId(), $deviceId, $this->boolean($payload, 'active'), $user->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->automationDevice($user->tenantId(), $deviceId)]);
    }

    #[Route('/automation-commands', name: 'commands', methods: ['GET'])]
    #[IsGranted('integration.automation.read')]
    public function commands(): JsonResponse
    {
        $data = $this->queries->deviceCommands($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/automation-commands', name: 'command_create', methods: ['POST'])]
    #[IsGranted('integration.automation.execute')]
    public function createCommand(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $command = $this->automation->queueCommand(
            $user->tenantId(),
            $this->string($payload, 'deviceId'),
            $this->string($payload, 'commandType'),
            $this->string($payload, 'locationId'),
            $this->string($payload, 'referenceType'),
            $this->string($payload, 'referenceId'),
            $this->string($payload, 'requestId'),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->deviceCommand($user->tenantId(), $command->id)], Response::HTTP_CREATED);
    }

    #[Route('/automation-commands/{commandId}/status', name: 'command_status', methods: ['PATCH'])]
    #[IsGranted('integration.automation.execute')]
    public function commandStatus(string $commandId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $command = $this->automation->transition(
            $user->tenantId(),
            $commandId,
            $this->string($payload, 'status'),
            $this->optionalString($payload, 'message'),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->deviceCommand($user->tenantId(), $command->id)]);
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
