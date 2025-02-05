<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockLocation;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockLocationTest'
)]
#[CoversClass(StockLocation::class)]
final class StockLocationTest extends TestCase
{
    private StockLocation $stockLocation;

    private DateTimeImmutable $dateTimeImmutable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLocation = new StockLocation();
        $this->dateTimeImmutable = new DateTimeImmutable();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockLocation->setStockLocationId($stockLocationId);
        self::assertSame($stockLocationId, $this->stockLocation->getStockLocationId());

        // Test setStockLocationLn() and getStockLocationLn()
        $stockLocationLn = 101;
        $this->stockLocation->setStockLocationLn($stockLocationLn);
        self::assertSame($stockLocationLn, $this->stockLocation->getStockLocationLn());

        // Test setStockLocationFb() and getStockLocationFb()
        $stockLocationFb = 1;
        $this->stockLocation->setStockLocationFb($stockLocationFb);
        self::assertSame($stockLocationFb, $this->stockLocation->getStockLocationFb());

        // Test setStockLocationSp() and getStockLocationSp()
        $stockLocationSp = 1;
        $this->stockLocation->setStockLocationSp($stockLocationSp);
        self::assertSame($stockLocationSp, $this->stockLocation->getStockLocationSp());

        // Test setStockLocationTf() and getStockLocationTf()
        $stockLocationTf = 1;
        $this->stockLocation->setStockLocationTf($stockLocationTf);
        self::assertSame($stockLocationTf, $this->stockLocation->getStockLocationTf());

        // Test setStockLocationCoordinate() and getStockLocationCoordinate()
        $stockLocationCoordinate = '101000100010001';
        $this->stockLocation->setStockLocationCoordinate($stockLocationCoordinate);
        self::assertSame($stockLocationCoordinate, $this->stockLocation->getStockLocationCoordinate());

        // Test setStockLocationDesc() and getStockLocationDesc()
        $stockLocationDesc = 'Test Stock Location Description';
        $this->stockLocation->setStockLocationDesc($stockLocationDesc);
        self::assertSame($stockLocationDesc, $this->stockLocation->getStockLocationDesc());

        // Test setStockLocationWidth() and getStockLocationWidth()
        $stockLocationWidth = 800.00;
        $this->stockLocation->setStockLocationWidth($stockLocationWidth);
        self::assertSame($stockLocationWidth, $this->stockLocation->getStockLocationWidth());

        // Test setStockLocationDepth() and getStockLocationDepth()
        $stockLocationDepth = 1200.00;
        $this->stockLocation->setStockLocationDepth($stockLocationDepth);
        self::assertSame($stockLocationDepth, $this->stockLocation->getStockLocationDepth());

        // Test setStockLocationHeight() and getStockLocationHeight()
        $stockLocationHeight = 2000.00;
        $this->stockLocation->setStockLocationHeight($stockLocationHeight);
        self::assertSame($stockLocationHeight, $this->stockLocation->getStockLocationHeight());

        // Test setStockLocationZone() and getStockLocationZone()
        $stockLocationZone = 'BLOCK';
        $this->stockLocation->setStockLocationZone($stockLocationZone);
        self::assertSame($stockLocationZone, $this->stockLocation->getStockLocationZone());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTimeImmutable;
        $this->stockLocation->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockLocation->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTimeImmutable;
        $this->stockLocation->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockLocation->getUpdatedAt());
    }
}
