<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockLayout;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockLayoutTest'
)]
#[CoversClass(StockLayout::class)]
final class StockLayoutTest extends TestCase
{
    private StockLayout $stockLayout;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLayout = new StockLayout();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockLayout->setId($id);
        self::assertSame($id, $this->stockLayout->getId());

        // Test setStockNr() and getStockNr()
        $stockNr = 100;
        $this->stockLayout->setStockNr($stockNr);
        self::assertSame($stockNr, $this->stockLayout->getStockNr());

        // Test setStockDescription() and getStockDescription()
        $stockDescription = 'Pal Regal';
        $this->stockLayout->setStockDescription($stockDescription);
        self::assertSame($stockDescription, $this->stockLayout->getStockDescription());

        // Test setStockLevel1() and getStockLevel1()
        $stockLevel1 = 1;
        $this->stockLayout->setStockLevel1($stockLevel1);
        self::assertSame($stockLevel1, $this->stockLayout->getStockLevel1());

        // Test setStockLevel2() and getStockLevel2()
        $stockLevel2 = 1;
        $this->stockLayout->setStockLevel2($stockLevel2);
        self::assertSame($stockLevel2, $this->stockLayout->getStockLevel2());

        // Test setStockLevel3() and getStockLevel3()
        $stockLevel3 = 1;
        $this->stockLayout->setStockLevel3($stockLevel3);
        self::assertSame($stockLevel3, $this->stockLayout->getStockLevel3());

        // Test setStockLevel4() and getStockLevel4()
        $stockLevel4 = 1;
        $this->stockLayout->setStockLevel4($stockLevel4);
        self::assertSame($stockLevel4, $this->stockLayout->getStockLevel4());

        // Test setStockModel() and getStockModel()
        $stockModel = 'L2';
        $this->stockLayout->setStockModel($stockModel);
        self::assertSame($stockModel, $this->stockLayout->getStockModel());

        // Test setStockTyp() and getStockTyp()
        $stockTyp = 'BLL';
        $this->stockLayout->setStockTyp($stockTyp);
        self::assertSame($stockTyp, $this->stockLayout->getStockTyp());

        // Test setStockLongDescription() and getStockLongDescription()
        $stockLongDescription = 'Test Stock Long Description';
        $this->stockLayout->setStockLongDescription($stockLongDescription);
        self::assertSame($stockLongDescription, $this->stockLayout->getStockLongDescription());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockLayout->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockLayout->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockLayout->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockLayout->getUpdatedAt());
    }
}
