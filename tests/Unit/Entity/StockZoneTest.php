<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockZone;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockZoneTest'
)]
#[CoversClass(StockZone::class)]
final class StockZoneTest extends TestCase
{
    private StockZone $stockZone;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockZone = new StockZone();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockZone->setId($id);
        self::assertSame($id, $this->stockZone->getId());

        // Test setStockZoneShortDesc() and getStockZoneShortDesc()
        $stockZoneShortDesc = 'KOMPAL100';
        $this->stockZone->setStockZoneShortDesc($stockZoneShortDesc);
        self::assertSame($stockZoneShortDesc, $this->stockZone->getStockZoneShortDesc());

        // Test setStockZoneDescription() and getStockZoneDescription()
        $stockZoneDescription = 'Kommissionierlager für mit Pal. Höhe 100 cm';
        $this->stockZone->setStockZoneDescription($stockZoneDescription);
        self::assertSame($stockZoneDescription, $this->stockZone->getStockZoneDescription());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockZone->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockZone->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockZone->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockZone->getUpdatedAt());
    }
}
