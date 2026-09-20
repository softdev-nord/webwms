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
use WebWMS\Integration\Application\DeviceIntegrationService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_device_')]
final class DeviceApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly DeviceIntegrationService $devices,
    ) {
    }

    #[Route('/devices', name: 'list', methods: ['GET'])]
    #[IsGranted('integration.device.read')]
    public function list(): JsonResponse
    {
        $data = $this->queries->devices($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/devices', name: 'create', methods: ['POST'])]
    #[IsGranted('integration.device.write')]
    public function create(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $device = $this->devices->registerDevice(
            $user->tenantId(),
            $this->string($payload, 'code'),
            $this->string($payload, 'name'),
            $this->string($payload, 'type'),
            $this->boolean($payload, 'active', true),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->device($user->tenantId(), $device->id)], Response::HTTP_CREATED);
    }

    #[Route('/devices/{deviceId}/status', name: 'status', methods: ['PATCH'])]
    #[IsGranted('integration.device.write')]
    public function status(string $deviceId, Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $this->devices->changeStatus(
            $user->tenantId(),
            $deviceId,
            $this->boolean($payload, 'active'),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->device($user->tenantId(), $deviceId)]);
    }

    #[Route('/scan-events', name: 'scan_list', methods: ['GET'])]
    #[IsGranted('integration.device.read')]
    public function scans(): JsonResponse
    {
        $data = $this->queries->scanEvents($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/scan-events', name: 'scan', methods: ['POST'])]
    #[IsGranted('integration.device.scan')]
    public function scan(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $user = $this->user();
        $event = $this->devices->recordScan(
            $user->tenantId(),
            $this->string($payload, 'deviceId'),
            $this->string($payload, 'scanType'),
            $this->string($payload, 'value'),
            $this->string($payload, 'processType'),
            $this->string($payload, 'contextReference'),
            $this->string($payload, 'requestId'),
            $this->boolean($payload, 'accepted', true),
            $this->optionalString($payload, 'message'),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->scanEvent($user->tenantId(), $event->id)], Response::HTTP_CREATED);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
        }

        return $user;
    }

    /** @return array<string, mixed> */
    private function payload(Request $request): array
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        return $payload;
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
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a string or null.', $field));
        }

        return trim($value) === '' ? null : trim($value);
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
