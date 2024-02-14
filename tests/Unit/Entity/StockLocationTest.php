<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTimeImmutable;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockLocationEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationTest
 */
#[CoversClass(StockLocationEntity::class)]
final class StockLocationTest extends TestCase
{
    private StockLocationEntity $stockLocationEntity;

    private DateTimeImmutable $dateTimeImmutable;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLocationEntity = new StockLocationEntity();
        $this->dateTimeImmutable = new DateTimeImmutable();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setStockLocationId() and getStockLocationId()
        $stockLocationId = 1;
        $this->stockLocationEntity->setStockLocationId($stockLocationId);
        self::assertEquals($stockLocationId, $this->stockLocationEntity->getStockLocationId());

        // Test setStockLocationLn() and getStockLocationLn()
        $stockLocationLn = 101;
        $this->stockLocationEntity->setStockLocationLn($stockLocationLn);
        self::assertEquals($stockLocationLn, $this->stockLocationEntity->getStockLocationLn());

        // Test setStockLocationFb() and getStockLocationFb()
        $stockLocationFb = 1;
        $this->stockLocationEntity->setStockLocationFb($stockLocationFb);
        self::assertEquals($stockLocationFb, $this->stockLocationEntity->getStockLocationFb());

        // Test setStockLocationSp() and getStockLocationSp()
        $stockLocationSp = 1;
        $this->stockLocationEntity->setStockLocationSp($stockLocationSp);
        self::assertEquals($stockLocationSp, $this->stockLocationEntity->getStockLocationSp());

        // Test setStockLocationTf() and getStockLocationTf()
        $stockLocationTf = 1;
        $this->stockLocationEntity->setStockLocationTf($stockLocationTf);
        self::assertEquals($stockLocationTf, $this->stockLocationEntity->getStockLocationTf());

        // Test setStockLocationCoordinate() and getStockLocationCoordinate()
        $stockLocationCoordinate = '101000100010001';
        $this->stockLocationEntity->setStockLocationCoordinate($stockLocationCoordinate);
        self::assertEquals($stockLocationCoordinate, $this->stockLocationEntity->getStockLocationCoordinate());

        // Test setStockLocationDesc() and getStockLocationDesc()
        $stockLocationDesc = 'Test Stock Location Description';
        $this->stockLocationEntity->setStockLocationDesc($stockLocationDesc);
        self::assertEquals($stockLocationDesc, $this->stockLocationEntity->getStockLocationDesc());

        // Test setStockLocationWidth() and getStockLocationWidth()
        $stockLocationWidth = 800.00;
        $this->stockLocationEntity->setStockLocationWidth($stockLocationWidth);
        self::assertEquals($stockLocationWidth, $this->stockLocationEntity->getStockLocationWidth());

        // Test setStockLocationDepth() and getStockLocationDepth()
        $stockLocationDepth = 1200.00;
        $this->stockLocationEntity->setStockLocationDepth($stockLocationDepth);
        self::assertEquals($stockLocationDepth, $this->stockLocationEntity->getStockLocationDepth());

        // Test setStockLocationHeight() and getStockLocationHeight()
        $stockLocationHeight = 2000.00;
        $this->stockLocationEntity->setStockLocationHeight($stockLocationHeight);
        self::assertEquals($stockLocationHeight, $this->stockLocationEntity->getStockLocationHeight());

        // Test setStockLocationZone() and getStockLocationZone()
        $stockLocationZone = 'BLOCK';
        $this->stockLocationEntity->setStockLocationZone($stockLocationZone);
        self::assertEquals($stockLocationZone, $this->stockLocationEntity->getStockLocationZone());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTimeImmutable;
        $this->stockLocationEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockLocationEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTimeImmutable;
        $this->stockLocationEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockLocationEntity->getUpdatedAt());
    }
}
