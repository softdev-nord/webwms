<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

interface PrintRepository
{
    public function addPrinter(Printer $printer): void;

    public function printer(string $tenantId, string $id, bool $activeOnly = false): Printer;

    public function changePrinterStatus(string $tenantId, string $id, bool $active, string $actorId, \DateTimeImmutable $at): void;

    public function addJob(PrintJob $job): PrintJob;

    public function job(string $tenantId, string $id): PrintJob;

    public function saveJob(PrintJob $job): void;
}
