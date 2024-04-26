<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockLayoutEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        StockLayoutTest
 */
#[CoversClass(StockLayoutEntity::class)]
final class StockLayoutTest extends TestCase
{
    private StockLayoutEntity $stockLayoutEntity;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLayoutEntity = new StockLayoutEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockLayoutEntity->setId($id);
        self::assertEquals($id, $this->stockLayoutEntity->getId());

        // Test setStockNr() and getStockNr()
        $stockNr = 100;
        $this->stockLayoutEntity->setStockNr($stockNr);
        self::assertEquals($stockNr, $this->stockLayoutEntity->getStockNr());

        // Test setStockDescription() and getStockDescription()
        $stockDescription = 'Pal Regal';
        $this->stockLayoutEntity->setStockDescription($stockDescription);
        self::assertEquals($stockDescription, $this->stockLayoutEntity->getStockDescription());

        // Test setStockLevel1() and getStockLevel1()
        $stockLevel1 = 1;
        $this->stockLayoutEntity->setStockLevel1($stockLevel1);
        self::assertEquals($stockLevel1, $this->stockLayoutEntity->getStockLevel1());

        // Test setStockLevel2() and getStockLevel2()
        $stockLevel2 = 1;
        $this->stockLayoutEntity->setStockLevel2($stockLevel2);
        self::assertEquals($stockLevel2, $this->stockLayoutEntity->getStockLevel2());

        // Test setStockLevel3() and getStockLevel3()
        $stockLevel3 = 1;
        $this->stockLayoutEntity->setStockLevel3($stockLevel3);
        self::assertEquals($stockLevel3, $this->stockLayoutEntity->getStockLevel3());

        // Test setStockLevel4() and getStockLevel4()
        $stockLevel4 = 1;
        $this->stockLayoutEntity->setStockLevel4($stockLevel4);
        self::assertEquals($stockLevel4, $this->stockLayoutEntity->getStockLevel4());

        // Test setStockModel() and getStockModel()
        $stockModel = 'L2';
        $this->stockLayoutEntity->setStockModel($stockModel);
        self::assertEquals($stockModel, $this->stockLayoutEntity->getStockModel());

        // Test setStockTyp() and getStockTyp()
        $stockTyp = 'BLL';
        $this->stockLayoutEntity->setStockTyp($stockTyp);
        self::assertEquals($stockTyp, $this->stockLayoutEntity->getStockTyp());

        // Test setStockLongDescription() and getStockLongDescription()
        $stockLongDescription = 'Test Stock Long Description';
        $this->stockLayoutEntity->setStockLongDescription($stockLongDescription);
        self::assertEquals($stockLongDescription, $this->stockLayoutEntity->getStockLongDescription());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockLayoutEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockLayoutEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockLayoutEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockLayoutEntity->getUpdatedAt());
    }
}
