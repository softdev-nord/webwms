<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\BookingMethodEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        BookingMethodTest
 */
#[CoversClass(BookingMethodEntity::class)]
final class BookingMethodTest extends TestCase
{
    private BookingMethodEntity $bookingMethodEntity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookingMethodEntity = new BookingMethodEntity();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->bookingMethodEntity->setId($id);
        self::assertEquals($id, $this->bookingMethodEntity->getId());

        // Test setConfirmation() and getConfirmation()
        $confirmation = 2;
        $this->bookingMethodEntity->setConfirmation($confirmation);
        self::assertEquals($confirmation, $this->bookingMethodEntity->getConfirmation());

        // Test setMovementType() and getMovementType()
        $movementType = 'inbound';
        $this->bookingMethodEntity->setMovementType($movementType);
        self::assertEquals($movementType, $this->bookingMethodEntity->getMovementType());

        // Test setDescription() and getDescription()
        $description = 'Test description';
        $this->bookingMethodEntity->setDescription($description);
        self::assertEquals($description, $this->bookingMethodEntity->getDescription());

        // Test setAnsteuerung() and getAnsteuerung()
        $ansteuerung = 3;
        $this->bookingMethodEntity->setAnsteuerung($ansteuerung);
        self::assertEquals($ansteuerung, $this->bookingMethodEntity->getAnsteuerung());

        // Test setUpload() and getUpload()
        $upload = 4;
        $this->bookingMethodEntity->setUpload($upload);
        self::assertEquals($upload, $this->bookingMethodEntity->getUpload());

        // Test setStatistics() and getStatistics()
        $statistics = 5;
        $this->bookingMethodEntity->setStatistics($statistics);
        self::assertEquals($statistics, $this->bookingMethodEntity->getStatistics());

        // Test setPriority() and getPriority()
        $priority = 6;
        $this->bookingMethodEntity->setPriority($priority);
        self::assertEquals($priority, $this->bookingMethodEntity->getPriority());

        // Test setTidDescription() and getTidDescription()
        $tidDescription = 7;
        $this->bookingMethodEntity->setTidDescription($tidDescription);
        self::assertEquals($tidDescription, $this->bookingMethodEntity->getTidDescription());
    }
}
