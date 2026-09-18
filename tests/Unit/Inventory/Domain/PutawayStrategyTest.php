<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\PutawayStrategy;
use WebWMS\Inventory\Domain\StockStatus;

final class PutawayStrategyTest extends TestCase
{
    public function testItRejectsAnInvalidLocationPrefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PutawayStrategy(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf490'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf402'),
            'AVAILABLE-A',
            StockStatus::Available,
            'lower case',
            1,
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }
}
