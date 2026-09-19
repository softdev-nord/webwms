<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\OutboxPublisher;
use WebWMS\Integration\Domain\OutboxMessage;
use WebWMS\Integration\Domain\OutboxRepository;
use WebWMS\Integration\Domain\OutboxTransport;

final class OutboxPublisherTest extends TestCase
{
    public function testItPublishesAndCompletesAClaimedMessage(): void
    {
        $now = new DateTimeImmutable('2026-09-19 12:00:00');
        $message = $this->message(1);
        $repository = $this->createMock(OutboxRepository::class);
        $repository->expects(self::once())->method('claimDue')->with(25, $now, $now->modify('-300 seconds'))->willReturn([$message]);
        $repository->expects(self::once())->method('markPublished')->with($message, $now);
        $repository->expects(self::never())->method('markFailed');
        $transport = $this->createMock(OutboxTransport::class);
        $transport->expects(self::once())->method('publish')->with($message);

        $report = (new OutboxPublisher($repository, $transport))->publishDue(25, $now);

        self::assertSame(1, $report->claimed);
        self::assertSame(1, $report->published);
        self::assertSame(0, $report->retryScheduled);
        self::assertSame(0, $report->deadLettered);
    }

    #[DataProvider('failedAttempts')]
    public function testItSchedulesExponentialRetriesAndEventuallyDeadLetters(
        int $attempt,
        ?string $expectedNextAttempt,
        int $expectedRetries,
        int $expectedDeadLetters
    ): void {
        $now = new DateTimeImmutable('2026-09-19 12:00:00');
        $message = $this->message($attempt);
        $repository = $this->createMock(OutboxRepository::class);
        $repository->method('claimDue')->willReturn([$message]);
        $repository->expects(self::once())->method('markFailed')->with(
            $message,
            'queue unavailable',
            $now,
            $expectedNextAttempt === null ? null : new DateTimeImmutable($expectedNextAttempt),
        );
        $transport = $this->createMock(OutboxTransport::class);
        $transport->method('publish')->willThrowException(new \RuntimeException('queue unavailable'));

        $report = (new OutboxPublisher($repository, $transport))->publishDue(10, $now);

        self::assertSame($expectedRetries, $report->retryScheduled);
        self::assertSame($expectedDeadLetters, $report->deadLettered);
    }

    /** @return iterable<string, array{int, ?string, int, int}> */
    public static function failedAttempts(): iterable
    {
        yield 'first failure' => [1, '2026-09-19 12:00:30', 1, 0];
        yield 'third failure' => [3, '2026-09-19 12:02:00', 1, 0];
        yield 'fifth failure' => [5, null, 0, 1];
    }

    private function message(int $attempt): OutboxMessage
    {
        return new OutboxMessage(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf501',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            'fulfillment.shipment.dispatched',
            'shipment',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf450',
            ['status' => 'dispatched'],
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf302',
            new DateTimeImmutable('2026-09-19 11:59:00'),
            $attempt,
        );
    }
}
