<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\ConfirmPickTaskCommand;
use WebWMS\Inventory\Application\ConfirmPickTaskHandler;
use WebWMS\Inventory\Domain\InventoryRepository;

final class ConfirmPickTaskHandlerTest extends TestCase
{
    public function testItRejectsAnUnknownOutcomeBeforeCallingTheRepository(): void
    {
        $inventory = $this->createMock(InventoryRepository::class);
        $inventory->expects(self::never())->method('confirmPick');
        $handler = new ConfirmPickTaskHandler($inventory);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A pick outcome must be either "picked" or "shortage".');

        $handler(new ConfirmPickTaskCommand(
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf421',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8',
            'damaged',
            null,
            'Not pickable',
            '018f6b7f-75d2-7c4e-8c33-31f91b1cf302',
            new DateTimeImmutable(),
        ));
    }
}
