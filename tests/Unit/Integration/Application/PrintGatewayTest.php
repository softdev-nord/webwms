<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use WebWMS\Integration\Application\PrintGateway;
use WebWMS\Integration\Domain\Printer;
use WebWMS\Integration\Domain\PrintJob;
use WebWMS\Integration\Domain\PrintRepository;
use WebWMS\Integration\Domain\PrintTransport;

class PrintGatewayTest extends TestCase
{
    public function testItPersistsPrintingAndSuccessfulCompletion(): void
    {
        [$repository, $transport, $job] = $this->fixture();
        $repository->expects($this->exactly(2))->method('saveJob');
        $transport->expects($this->once())->method('print')->willReturn('PRINT-1');

        $result = new PrintGateway($repository, $transport)->execute('tenant-id', 'job-id', new DateTimeImmutable());

        self::assertSame(PrintJob::STATUS_PRINTED, $result->status);
        self::assertSame('PRINT-1', $result->externalReference);
        self::assertSame(1, $result->attempts);
    }

    public function testItPersistsTransportFailuresForRetry(): void
    {
        [$repository, $transport, $job] = $this->fixture();
        $repository->expects($this->exactly(2))->method('saveJob');
        $transport->method('print')->willThrowException(new RuntimeException('Printer offline'));

        try {
            new PrintGateway($repository, $transport)->execute('tenant-id', 'job-id', new DateTimeImmutable());
            self::fail('The transport exception must be propagated.');
        } catch (RuntimeException $exception) {
            self::assertSame('Printer offline', $exception->getMessage());
        }

        self::assertSame(PrintJob::STATUS_FAILED, $job->status);
        self::assertSame('Printer offline', $job->lastError);
    }

    /** @return array{PrintRepository&\PHPUnit\Framework\MockObject\MockObject, PrintTransport&\PHPUnit\Framework\MockObject\MockObject, PrintJob} */
    private function fixture(): array
    {
        $now = new DateTimeImmutable();
        $printer = new Printer('printer-id', 'tenant-id', 'Pack printer', 'https://printer.example.com', 'PRINTER_TOKEN', true, 'user-id', $now);
        $job = new PrintJob('job-id', 'tenant-id', 'printer-id', 'document', 'DOC-1', 'PDF', 1, 'request-id', PrintJob::STATUS_QUEUED, 0, null, null, 'user-id', $now);
        $repository = $this->createMock(PrintRepository::class);
        $repository->method('job')->with('tenant-id', 'job-id')->willReturn($job);
        $repository->method('printer')->with('tenant-id', 'printer-id', true)->willReturn($printer);

        return [$repository, $this->createMock(PrintTransport::class), $job];
    }
}
