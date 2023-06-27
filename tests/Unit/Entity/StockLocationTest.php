<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationTest
 *
 * @covers \WebWMS\Entity\StockLocation
 */
final class StockLocationTest extends TestCase
{
    private StockLocation $stockLocation;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLocation = new StockLocation();
        $this->dateTime = new \DateTimeImmutable();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockLocation->setStockLocationId($stockLocationId);
        self::assertEquals($stockLocationId, $this->stockLocation->getStockLocationId());

        // Test setStockLocationLn() and getStockLocationLn()
        $stockLocationLn = 101;
        $this->stockLocation->setStockLocationLn($stockLocationLn);
        self::assertEquals($stockLocationLn, $this->stockLocation->getStockLocationLn());

        // Test setStockLocationFb() and getStockLocationFb()
        $stockLocationFb = 1;
        $this->stockLocation->setStockLocationFb($stockLocationFb);
        self::assertEquals($stockLocationFb, $this->stockLocation->getStockLocationFb());

        // Test setStockLocationSp() and getStockLocationSp()
        $stockLocationSp = 1;
        $this->stockLocation->setStockLocationSp($stockLocationSp);
        self::assertEquals($stockLocationSp, $this->stockLocation->getStockLocationSp());

        // Test setStockLocationTf() and getStockLocationTf()
        $stockLocationTf = 1;
        $this->stockLocation->setStockLocationTf($stockLocationTf);
        self::assertEquals($stockLocationTf, $this->stockLocation->getStockLocationTf());

        // Test setStockLocationCoordinate() and getStockLocationCoordinate()
        $stockLocationCoordinate = '101000100010001';
        $this->stockLocation->setStockLocationCoordinate($stockLocationCoordinate);
        self::assertEquals($stockLocationCoordinate, $this->stockLocation->getStockLocationCoordinate());

        // Test setStockLocationDesc() and getStockLocationDesc()
        $stockLocationDesc = 'Test Stock Location Description';
        $this->stockLocation->setStockLocationDesc($stockLocationDesc);
        self::assertEquals($stockLocationDesc, $this->stockLocation->getStockLocationDesc());

        // Test setStockLocationWidth() and getStockLocationWidth()
        $stockLocationWidth = 800.00;
        $this->stockLocation->setStockLocationWidth($stockLocationWidth);
        self::assertEquals($stockLocationWidth, $this->stockLocation->getStockLocationWidth());

        // Test setStockLocationDepth() and getStockLocationDepth()
        $stockLocationDepth = 1200.00;
        $this->stockLocation->setStockLocationDepth($stockLocationDepth);
        self::assertEquals($stockLocationDepth, $this->stockLocation->getStockLocationDepth());

        // Test setStockLocationHeight() and getStockLocationHeight()
        $stockLocationHeight = 2000.00;
        $this->stockLocation->setStockLocationHeight($stockLocationHeight);
        self::assertEquals($stockLocationHeight, $this->stockLocation->getStockLocationHeight());

        // Test setStockLocationZone() and getStockLocationZone()
        $stockLocationZone = 'BLOCK';
        $this->stockLocation->setStockLocationZone($stockLocationZone);
        self::assertEquals($stockLocationZone, $this->stockLocation->getStockLocationZone());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockLocation->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockLocation->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockLocation->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockLocation->getUpdatedAt());
    }
}
