<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\RetryDeadLetterCommand;
use WebWMS\Integration\Application\RetryDeadLetterHandler;
use WebWMS\Integration\Domain\OutboxRepository;

class RetryDeadLetterHandlerTest extends TestCase
{
    public function testItDelegatesTheTenantScopedManualRetry(): void
    {
        $at = new DateTimeImmutable('2026-09-19 12:00:00');
        $repository = $this->createMock(OutboxRepository::class);
        $repository->expects($this->once())->method('retryDeadLetter')->with('message', 'tenant', 'user', $at);

        (new RetryDeadLetterHandler($repository))(new RetryDeadLetterCommand('message', 'tenant', 'user', $at));
    }
}
