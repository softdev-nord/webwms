<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\PackingPackage;

final class PackingPackageTest extends TestCase
{
    public function testItRejectsAPackageWithoutPositiveWeight(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PackingPackage(new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf440'), new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf441'), new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'), 'PKG-1', 0, [new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf421')], new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'), new DateTimeImmutable());
    }
}
