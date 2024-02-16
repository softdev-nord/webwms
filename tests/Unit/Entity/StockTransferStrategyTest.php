<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockTransferStrategyEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockTransferStrategyTest
 */
#[CoversClass(StockTransferStrategyEntity::class)]
final class StockTransferStrategyTest extends TestCase
{
    private StockTransferStrategyEntity $stockTransferStrategyEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->stockTransferStrategyEntity = new StockTransferStrategyEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockTransferStrategyEntity->setId($id);
        self::assertEquals($id, $this->stockTransferStrategyEntity->getId());

        // Test setShortCode() and getShortCode()
        $shortCode = 'FIFO';
        $this->stockTransferStrategyEntity->setShortCode($shortCode);
        self::assertEquals($shortCode, $this->stockTransferStrategyEntity->getShortCode());

        // Test setDescription() and getDescription()
        $description = 'FIFO';
        $this->stockTransferStrategyEntity->setDescription($description);
        self::assertEquals($description, $this->stockTransferStrategyEntity->getDescription());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockTransferStrategyEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockTransferStrategyEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockTransferStrategyEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockTransferStrategyEntity->getUpdatedAt());
    }
}
