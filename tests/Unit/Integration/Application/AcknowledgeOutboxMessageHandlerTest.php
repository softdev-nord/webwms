<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\AcknowledgeOutboxMessageCommand;
use WebWMS\Integration\Application\AcknowledgeOutboxMessageHandler;
use WebWMS\Integration\Domain\OutboxRepository;

final class AcknowledgeOutboxMessageHandlerTest extends TestCase
{
    public function testItDelegatesTheTenantScopedAcknowledgement(): void
    {
        $outbox = $this->createMock(OutboxRepository::class);
        $outbox->expects(self::once())->method('acknowledge');
        $handler = new AcknowledgeOutboxMessageHandler($outbox);

        $handler(new AcknowledgeOutboxMessageCommand(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf501',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf302',
            new DateTimeImmutable(),
        ));
    }
}
