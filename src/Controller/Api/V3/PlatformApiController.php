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
use WebWMS\Platform\Application\PlatformControlService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/platform', name: 'api_v3_platform_')]
final class PlatformApiController extends AbstractController
{
    public function __construct(
        private readonly PlatformControlService $platform
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('platform.read')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['data' => $this->platform->workspace($this->user()->tenantId())]);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    #[IsGranted('platform.read')]
    public function search(Request $request): JsonResponse
    {
        return new JsonResponse(['data' => $this->platform->search($this->user()->tenantId(), (string) $request->query->get('q'))]);
    }

    #[Route('/resources/{resource}', name: 'create', requirements: ['resource' => 'task|kpi|dashboard|partner_account|automation_rule|storage_fee_rule|service|print_route'], methods: ['POST'])]
    #[IsGranted('platform.write')]
    public function create(string $resource, Request $request): JsonResponse
    {
        $user = $this->user();
        $id = $this->platform->create($user->tenantId(), $user->actorId(), $resource, $request->toArray(), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], JsonResponse::HTTP_CREATED);
    }

    #[Route('/tasks/{taskId}/status', name: 'task_status', methods: ['POST'])]
    #[IsGranted('platform.execute')]
    public function taskStatus(string $taskId, Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $this->platform->transitionTask($user->tenantId(), $user->actorId(), $taskId, $this->string($payload, 'status'), new DateTimeImmutable());

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/events', name: 'event', methods: ['POST'])]
    #[IsGranted('platform.execute')]
    public function event(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $eventPayload = $payload['payload'] ?? null;
        if (!is_array($eventPayload)) {
            throw new \InvalidArgumentException('Field "payload" must be an object.');
        }
        $user = $this->user();

        return new JsonResponse(['data' => ['evaluatedRules' => $this->platform->executeEvent($user->tenantId(), $user->actorId(), $this->string($payload, 'eventName'), $eventPayload, new DateTimeImmutable())]]);
    }

    #[Route('/billing/storage', name: 'bill_storage', methods: ['POST'])]
    #[IsGranted('platform.execute')]
    public function billStorage(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->platform->billStorage($user->tenantId(), $user->actorId(), $this->string($payload, 'ruleId'), $this->number($payload, 'quantity'), $this->integer($payload, 'days'), $this->string($payload, 'reference'), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], JsonResponse::HTTP_CREATED);
    }

    #[Route('/billing/services', name: 'bill_service', methods: ['POST'])]
    #[IsGranted('platform.execute')]
    public function billService(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $id = $this->platform->billService($user->tenantId(), $user->actorId(), $this->string($payload, 'serviceId'), $this->string($payload, 'businessPartnerId'), $this->number($payload, 'quantity'), $this->string($payload, 'reference'), new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], JsonResponse::HTTP_CREATED);
    }

    #[Route('/media', name: 'media', methods: ['POST'])]
    #[IsGranted('platform.write')]
    public function media(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $encoded = $this->string($payload, 'contentBase64');
        $content = base64_decode($encoded, true);
        if (!is_string($content)) {
            throw new \InvalidArgumentException('Field "contentBase64" must contain valid base64.');
        }
        $user = $this->user();
        $id = $this->platform->captureMedia($user->tenantId(), $user->actorId(), $this->string($payload, 'aggregateType'), $this->string($payload, 'aggregateId'), $this->string($payload, 'filename'), $this->string($payload, 'mimeType'), $content, new DateTimeImmutable());

        return new JsonResponse(['data' => ['id' => $id]], JsonResponse::HTTP_CREATED);
    }

    #[Route('/print-routing/select', name: 'print_route', methods: ['GET'])]
    #[IsGranted('platform.read')]
    public function printRoute(Request $request): JsonResponse
    {
        return new JsonResponse(['data' => ['printerId' => $this->platform->routePrinter($this->user()->tenantId(), (string) $request->query->get('documentType'), $request->query->getString('siteId') ?: null, $request->query->getString('workstation') ?: null, $request->query->getString('processKey') ?: null)]]);
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
    private function number(array $payload, string $field): float
    {
        $value = $payload[$field] ?? null;
        if (!is_int($value) && !is_float($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be numeric.', $field));
        }

        return (float) $value;
    }
}
