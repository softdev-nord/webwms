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
use WebWMS\Integration\Application\PrintGateway;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3', name: 'api_v3_print_')]
final class PrintApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly PrintGateway $gateway
    ) {
    }

    #[Route('/printers', name: 'printers', methods: ['GET'])]
    #[IsGranted('integration.printer.read')]
    public function printers(): JsonResponse
    {
        $data = $this->queries->printers($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/printers', name: 'create_printer', methods: ['POST'])]
    #[IsGranted('integration.printer.write')]
    public function createPrinter(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $printer = $this->gateway->registerPrinter(
            $this->user()->tenantId(), $this->string($payload, 'name'), $this->string($payload, 'endpointUrl'),
            $this->string($payload, 'credentialEnv'), $this->boolean($payload, 'active', true),
            $this->user()->actorId(), new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => ['id' => $printer->id, 'name' => $printer->name, 'active' => $printer->active]], Response::HTTP_CREATED);
    }

    #[Route('/printers/{printerId}/status', name: 'printer_status', methods: ['PATCH'])]
    #[IsGranted('integration.printer.write')]
    public function printerStatus(string $printerId, Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $this->gateway->changePrinterStatus($this->user()->tenantId(), $printerId, $this->boolean($payload, 'active'), $this->user()->actorId(), new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->printer($this->user()->tenantId(), $printerId)]);
    }

    #[Route('/print-jobs', name: 'jobs', methods: ['GET'])]
    #[IsGranted('integration.print_job.read')]
    public function jobs(): JsonResponse
    {
        $data = $this->queries->printJobs($this->user()->tenantId());

        return new JsonResponse(['data' => $data, 'meta' => ['count' => count($data)]]);
    }

    #[Route('/print-jobs', name: 'queue', methods: ['POST'])]
    #[IsGranted('integration.print_job.write')]
    public function queue(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        $job = $this->gateway->queue(
            $this->user()->tenantId(), $this->string($payload, 'printerId'), $this->string($payload, 'documentType'),
            $this->string($payload, 'documentReference'), $this->string($payload, 'format'), $this->integer($payload, 'copies', 1),
            $this->string($payload, 'requestId'), $this->user()->actorId(), new DateTimeImmutable(),
        );

        return new JsonResponse(['data' => $this->queries->printJob($this->user()->tenantId(), $job->id)], Response::HTTP_CREATED);
    }

    #[Route('/print-jobs/{jobId}/execute', name: 'execute', methods: ['POST'])]
    #[IsGranted('integration.print_job.execute')]
    public function execute(string $jobId): JsonResponse
    {
        $job = $this->gateway->execute($this->user()->tenantId(), $jobId, new DateTimeImmutable());

        return new JsonResponse(['data' => $this->queries->printJob($this->user()->tenantId(), $job->id)]);
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
    private function boolean(array $payload, string $field, ?bool $default = null): bool
    {
        $value = $payload[$field] ?? $default;
        if (!is_bool($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be boolean.', $field));
        }

        return $value;
    }

    /** @param array<string, mixed> $payload */
    private function integer(array $payload, string $field, ?int $default = null): int
    {
        $value = $payload[$field] ?? $default;
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Field "%s" must be an integer.', $field));
        }

        return $value;
    }
}
