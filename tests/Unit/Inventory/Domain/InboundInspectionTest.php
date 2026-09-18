<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InboundInspection;
use WebWMS\Inventory\Domain\InboundQualityDecision;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\QualityCheckAnswer;
use WebWMS\Inventory\Domain\StockDimensions;

final class InboundInspectionTest extends TestCase
{
    public function testItRejectsAcceptanceWithAFailedCheck(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InboundInspection(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf480'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf481'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf403'),
            InboundQualityDecision::Accept,
            [new QualityCheckAnswer('Packaging intact?', false, 'Damaged corner')],
            new StockDimensions(),
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }

    public function testBlockedDecisionMapsToBlockedStock(): void
    {
        self::assertSame('blocked', InboundQualityDecision::Block->stockStatus()->value);
    }
}
