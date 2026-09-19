<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\PrintJob;

final class PrintJobTest extends TestCase
{
    public function testItTransitionsFromQueuedToPrinted(): void
    {
        $job = $this->job();
        $job->start();
        $job->succeed('printer-job-1', new DateTimeImmutable('2026-09-19 17:30:01'));

        self::assertSame(PrintJob::STATUS_PRINTED, $job->status);
        self::assertSame(1, $job->attempts);
        self::assertSame('printer-job-1', $job->externalReference);
    }

    public function testItCanRetryAFailedJob(): void
    {
        $job = $this->job();
        $job->start();
        $job->fail('Printer offline');
        $job->start();

        self::assertSame(PrintJob::STATUS_PRINTING, $job->status);
        self::assertSame(2, $job->attempts);
        self::assertNull($job->lastError);
    }

    public function testItRejectsDuplicateExecution(): void
    {
        $job = $this->job();
        $job->start();

        $this->expectException(\DomainException::class);
        $job->start();
    }

    private function job(): PrintJob
    {
        return new PrintJob('job', 'tenant', 'printer', 'carrier_label', 'label://1', 'ZPL', 1, 'request', PrintJob::STATUS_QUEUED, 0, null, null, 'user', new DateTimeImmutable('2026-09-19 17:30:00'));
    }
}
