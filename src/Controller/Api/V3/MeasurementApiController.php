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
use WebWMS\Integration\Application\MeasurementService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_measurement_')]
final class MeasurementApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly MeasurementService $measurements,
    ) {
    }

    #[Route('/measurement-devices', name: 'devices', methods: ['GET'])]
    #[IsGranted('integration.measurement.read')]
    public function devices(): JsonResponse
    {
        $data = $this->queries->measurementDevices($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/measurement-devices', name: 'device_create', methods: ['POST'])]
    #[IsGranted('integration.measurement.write')]
    public function createDevice(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $device = $this->measurements->registerDevice(
            $user->tenantId(),
            $this->string($payload, 'code'),
            $this->string($payload, 'name'),
            $this->string($payload, 'type'),
            $this->boolean($payload, 'active', true),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->measurementDevice($user->tenantId(), $device->id)], Response::HTTP_CREATED);
    }

    #[Route('/measurement-devices/{deviceId}/status', name: 'device_status', methods: ['PATCH'])]
    #[IsGranted('integration.measurement.write')]
    public function status(string $deviceId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $this->measurements->changeStatus(
            $user->tenantId(),
            $deviceId,
            $this->boolean($payload, 'active'),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->measurementDevice($user->tenantId(), $deviceId)]);
    }

    #[Route('/measurements', name: 'list', methods: ['GET'])]
    #[IsGranted('integration.measurement.read')]
    public function list(): JsonResponse
    {
        $data = $this->queries->measurements($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/measurements', name: 'record', methods: ['POST'])]
    #[IsGranted('integration.measurement.capture')]
    public function record(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $measurement = $this->measurements->record(
            $user->tenantId(),
            $this->string($payload, 'deviceId'),
            $this->string($payload, 'targetType'),
            $this->string($payload, 'targetId'),
            $this->positiveInt($payload, 'weightGrams'),
            $this->positiveInt($payload, 'lengthMillimeters'),
            $this->positiveInt($payload, 'widthMillimeters'),
            $this->positiveInt($payload, 'heightMillimeters'),
            $this->string($payload, 'requestId'),
            $this->boolean($payload, 'accepted', true),
            $this->optionalString($payload, 'message'),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->measurement($user->tenantId(), $measurement->id)], Response::HTTP_CREATED);
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
    private function positiveInt(array $payload, string $field): ?int
    {
        $value = $payload[$field] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_int($value) || $value <= 0) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be a positive integer or null.', $field));
        }

        return $value;
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
