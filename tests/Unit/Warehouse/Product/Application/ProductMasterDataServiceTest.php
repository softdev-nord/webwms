<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Warehouse\Product\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Warehouse\Product\Application\ProductMasterDataService;

class ProductMasterDataServiceTest extends TestCase
{
    public function testCreateRejectsAnInvalidGtinBeforeWritingData(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->never())->method('insert');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('GTIN');

        (new ProductMasterDataService($connection))->create(
            'product-id',
            'tenant-id',
            ['sku' => 'SKU-001', 'name' => 'Test item', 'base_unit' => 'PCS', 'gtin' => 'invalid'],
            'actor-id',
            new DateTimeImmutable(),
        );
    }
}
