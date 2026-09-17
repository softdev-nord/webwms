<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\PickList;

final class PickListTest extends TestCase
{
    public function testItNormalizesTheCode(): void
    {
        $list = new PickList(new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf430'), new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'), ' pick-001 ', [new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf421')], new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'), new DateTimeImmutable());
        self::assertSame('PICK-001', $list->code());
    }

    public function testItRejectsDuplicateAllocations(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $id = new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf421');
        new PickList(new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf430'), new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'), 'PICK-001', [$id, $id], new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'), new DateTimeImmutable());
    }
}
