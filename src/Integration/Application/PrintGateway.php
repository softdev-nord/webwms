<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\Printer;
use WebWMS\Integration\Domain\PrintJob;
use WebWMS\Integration\Domain\PrintRepository;
use WebWMS\Integration\Domain\PrintTransport;

final readonly class PrintGateway
{
    public function __construct(
        private PrintRepository $repository,
        private PrintTransport $transport
    ) {
    }

    public function registerPrinter(
        string $tenantId,
        string $name,
        string $endpointUrl,
        string $credentialEnv,
        bool $active,
        string $actorId,
        DateTimeImmutable $at
    ): Printer {
        $printer = new Printer(Uuid::v7()->toRfc4122(), $tenantId, trim($name), rtrim($endpointUrl, '/'), $credentialEnv, $active, $actorId, $at);
        $this->repository->addPrinter($printer);

        return $printer;
    }

    public function changePrinterStatus(string $tenantId, string $printerId, bool $active, string $actorId, DateTimeImmutable $at): void
    {
        $this->repository->changePrinterStatus($tenantId, $printerId, $active, $actorId, $at);
    }

    public function queue(
        string $tenantId,
        string $printerId,
        string $documentType,
        string $documentReference,
        string $format,
        int $copies,
        string $requestId,
        string $actorId,
        DateTimeImmutable $at
    ): PrintJob {
        $this->repository->printer($tenantId, $printerId, true);
        $job = new PrintJob(
            Uuid::v7()->toRfc4122(), $tenantId, $printerId, $documentType, $documentReference,
            mb_strtoupper($format), $copies, $requestId, PrintJob::STATUS_QUEUED, 0, null, null, $actorId, $at,
        );

        return $this->repository->addJob($job);
    }

    public function execute(string $tenantId, string $jobId, DateTimeImmutable $at): PrintJob
    {
        $job = $this->repository->job($tenantId, $jobId);
        $printer = $this->repository->printer($tenantId, $job->printerId, true);
        $job->start();
        $this->repository->saveJob($job);

        try {
            $job->succeed($this->transport->print($printer, $job), $at);
        } catch (\Throwable $exception) {
            $message = trim($exception->getMessage());
            $job->fail($message === '' ? $exception::class : $message);
            $this->repository->saveJob($job);
            throw $exception;
        }
        $this->repository->saveJob($job);

        return $job;
    }
}
