<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\ProductReference;
use WebWMS\Inventory\Domain\Sku;
use WebWMS\Inventory\Domain\StockPosting;

final class InventoryCoreTest extends TestCase
{
    public function testItNormalizesSkuAndProductName(): void
    {
        $product = new ProductReference(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new Sku(' article-01 '),
            ' Test article ',
            new DateTimeImmutable(),
        );

        self::assertSame('ARTICLE-01', $product->sku()->value());
        self::assertSame('Test article', $product->name());
    }

    public function testItRejectsAZeroQuantityPosting(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StockPosting(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf404'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf403'),
            0,
            'Inventory correction',
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }
}
