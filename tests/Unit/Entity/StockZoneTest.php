<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockZoneEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneTest
 */
#[CoversClass(StockZoneEntity::class)]
final class StockZoneTest extends TestCase
{
    private StockZoneEntity $stockZoneEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->stockZoneEntity = new StockZoneEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockZoneEntity->setId($id);
        self::assertEquals($id, $this->stockZoneEntity->getId());

        // Test setStockZoneShortDesc() and getStockZoneShortDesc()
        $stockZoneShortDesc = 'KOMPAL100';
        $this->stockZoneEntity->setStockZoneShortDesc($stockZoneShortDesc);
        self::assertEquals($stockZoneShortDesc, $this->stockZoneEntity->getStockZoneShortDesc());

        // Test setStockZoneDescription() and getStockZoneDescription()
        $stockZoneDescription = 'Kommissionierlager für mit Pal. Höhe 100 cm';
        $this->stockZoneEntity->setStockZoneDescription($stockZoneDescription);
        self::assertEquals($stockZoneDescription, $this->stockZoneEntity->getStockZoneDescription());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockZoneEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockZoneEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockZoneEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockZoneEntity->getUpdatedAt());
    }
}
