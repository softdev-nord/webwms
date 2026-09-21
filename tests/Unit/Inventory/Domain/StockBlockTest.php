<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Domain\StockBlockReasonDefinition;
use WebWMS\Inventory\Domain\StockBlockStatus;

final class StockBlockTest extends TestCase
{
    public function testReasonNormalizesInput(): void
    {
        $reason = new StockBlockReasonDefinition(' damage ', ' Beschädigung ', ' Karton defekt ', true);

        self::assertSame('DAMAGE', $reason->code);
        self::assertSame('Beschädigung', $reason->name);
        self::assertSame('Karton defekt', $reason->description);
    }

    public function testReasonRequiresCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StockBlockReasonDefinition('', 'Beschädigung', null, true);
    }

    public function testWorkflowOnlyAllowsReviewThenRelease(): void
    {
        self::assertTrue(StockBlockStatus::Open->canReview());
        self::assertFalse(StockBlockStatus::Open->canRelease());
        self::assertFalse(StockBlockStatus::Reviewed->canReview());
        self::assertTrue(StockBlockStatus::Reviewed->canRelease());
        self::assertFalse(StockBlockStatus::Released->canRelease());
    }
}
