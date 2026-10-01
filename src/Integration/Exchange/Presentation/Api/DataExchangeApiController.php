<?php

declare(strict_types=1);

namespace WebWMS\Integration\Exchange\Presentation\Api;

use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WebWMS\Integration\Application\DataExchangeService;
use WebWMS\Integration\Application\Query\IntegrationQueryService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/integration', name: 'api_v3_integration_exchange_')]
class DataExchangeApiController extends AbstractController
{
    public function __construct(
        private readonly DataExchangeService $exchange,
        private readonly IntegrationQueryService $queries,
        private readonly string $projectDir,
    ) {
    }

    #[Route('/openapi.yaml', name: 'openapi', methods: ['GET'])]
    public function openApi(): Response
    {
        $content = file_get_contents($this->projectDir . '/docs/technical/openapi-v3.yaml');
        if (!is_string($content)) {
            throw $this->createNotFoundException('The OpenAPI contract is unavailable.');
        }

        return new Response($content, Response::HTTP_OK, ['Content-Type' => 'application/yaml']);
    }

    #[Route('/jobs', name: 'jobs', methods: ['GET'])]
    #[IsGranted('integration.exchange.read')]
    public function jobs(): JsonResponse
    {
        return $this->collection($this->queries->exchangeJobs($this->user()->tenantId()));
    }

    #[Route('/mappings', name: 'mappings', methods: ['GET'])]
    #[IsGranted('integration.exchange.read')]
    public function mappings(): JsonResponse
    {
        return $this->collection($this->queries->integrationMappings($this->user()->tenantId()));
    }

    #[Route('/mappings', name: 'mapping_create', methods: ['POST'])]
    #[IsGranted('integration.exchange.write')]
    public function createMapping(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $mapping = $this->exchange->addMapping(
            $user->tenantId(),
            $this->string($payload, 'systemType'),
            $this->string($payload, 'messageType'),
            $this->string($payload, 'sourceField'),
            $this->string($payload, 'targetField'),
            $this->string($payload, 'transformation'),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => ['id' => $mapping->id]], Response::HTTP_CREATED);
    }

    #[Route('/import', name: 'import', methods: ['POST'])]
    #[IsGranted('integration.exchange.write')]
    public function import(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $format = $this->string($payload, 'format');
        $content = $this->string($payload, 'content');
        if ($format === 'xlsx') {
            $decoded = base64_decode($content, true);
            if (!is_string($decoded)) {
                throw new InvalidArgumentException('XLSX content must be valid base64.');
            }
            $content = $decoded;
        }
        $job = $format === 'idoc'
            ? $this->exchange->receiveIdoc($user->tenantId(), $content, $user->actorId(), new DateTimeImmutable())
            : $this->exchange->import(
                $user->tenantId(),
                $format,
                $this->string($payload, 'resourceType'),
                $content,
                $this->nullableString($payload, 'sourceReference'),
                $user->actorId(),
                new DateTimeImmutable(),
                $this->nullableString($payload, 'systemType'),
            );

        return new JsonResponse(['data' => ['id' => $job->id, 'rows' => count($job->rows)]], Response::HTTP_CREATED);
    }

    #[Route('/export', name: 'export', methods: ['POST'])]
    #[IsGranted('integration.exchange.write')]
    public function export(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $rows = $payload['rows'] ?? null;
        if (!is_array($rows) || !array_is_list($rows)) {
            throw new InvalidArgumentException('Field "rows" must be a list.');
        }
        $user = $this->user();
        $format = $this->string($payload, 'format');
        $result = $this->exchange->export(
            $user->tenantId(),
            $format,
            $this->string($payload, 'resourceType'),
            $rows,
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => [
            'id' => $result['job']->id,
            'format' => $format,
            'encoding' => $format === 'xlsx' ? 'base64' : 'utf-8',
            'content' => $format === 'xlsx' ? base64_encode($result['content']) : $result['content'],
        ]], Response::HTTP_CREATED);
    }

    #[Route('/commerce-connections', name: 'commerce_connections', methods: ['GET'])]
    #[IsGranted('integration.exchange.read')]
    public function commerceConnections(): JsonResponse
    {
        return $this->collection($this->queries->commerceConnections($this->user()->tenantId()));
    }

    #[Route('/commerce-connections', name: 'commerce_connection_create', methods: ['POST'])]
    #[IsGranted('integration.exchange.write')]
    public function createCommerceConnection(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $user = $this->user();
        $connection = $this->exchange->addCommerceConnection(
            $user->tenantId(),
            $this->string($payload, 'name'),
            $this->string($payload, 'channelType'),
            $this->string($payload, 'endpointUrl'),
            $this->string($payload, 'credentialEnv'),
            $this->optionalBool($payload, 'active', true),
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => ['id' => $connection->id]], Response::HTTP_CREATED);
    }

    #[Route('/commerce-orders', name: 'commerce_order_import', methods: ['POST'])]
    #[IsGranted('integration.exchange.write')]
    public function importCommerceOrder(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $order = $payload['order'] ?? null;
        if (!is_array($order)) {
            throw new InvalidArgumentException('Field "order" must be an object.');
        }
        $user = $this->user();
        $id = $this->exchange->importChannelOrder(
            $user->tenantId(),
            $this->string($payload, 'connectionId'),
            $this->string($payload, 'externalOrderId'),
            $order,
            $user->actorId(),
            new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => ['id' => $id, 'status' => 'imported']], Response::HTTP_CREATED);
    }

    /** @param list<array<string, mixed>> $items */
    private function collection(array $items): JsonResponse
    {
        return new JsonResponse(['data' => $items, 'meta' => ['count' => count($items)]]);
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
            throw new InvalidArgumentException(sprintf('Field "%s" must be a non-empty string.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function nullableString(array $payload, string $field): ?string
    {
        $value = $payload[$field] ?? null;

        return is_string($value) && trim($value) !== '' ? $value : null;
    }

    /** @param array<string, mixed> $payload */
    private function optionalBool(array $payload, string $field, bool $default): bool
    {
        $value = $payload[$field] ?? $default;
        if (!is_bool($value)) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be boolean.', $field));
        }

        return $value;
    }
}
