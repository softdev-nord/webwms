<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockTransferStrategy;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockTransferStrategyTest'
)]
#[CoversClass(StockTransferStrategy::class)]
final class StockTransferStrategyTest extends TestCase
{
    private StockTransferStrategy $stockTransferStrategy;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockTransferStrategy = new StockTransferStrategy();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->stockTransferStrategy->setId($id);
        self::assertSame($id, $this->stockTransferStrategy->getId());

        // Test setShortCode() and getShortCode()
        $shortCode = 'FIFO';
        $this->stockTransferStrategy->setShortCode($shortCode);
        self::assertSame($shortCode, $this->stockTransferStrategy->getShortCode());

        // Test setDescription() and getDescription()
        $description = 'FIFO';
        $this->stockTransferStrategy->setDescription($description);
        self::assertSame($description, $this->stockTransferStrategy->getDescription());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->stockTransferStrategy->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->stockTransferStrategy->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->stockTransferStrategy->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->stockTransferStrategy->getUpdatedAt());
    }
}
