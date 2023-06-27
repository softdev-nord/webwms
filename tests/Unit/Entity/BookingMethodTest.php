<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\BookingMethod;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BookingMethodTest
 *
 * @covers \WebWMS\Entity\BookingMethod
 */
final class BookingMethodTest extends TestCase
{
    private BookingMethod $bookingMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookingMethod = new BookingMethod();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->bookingMethod->setId($id);
        self::assertEquals($id, $this->bookingMethod->getId());

        // Test setConfirmation() and getConfirmation()
        $confirmation = 2;
        $this->bookingMethod->setConfirmation($confirmation);
        self::assertEquals($confirmation, $this->bookingMethod->getConfirmation());

        // Test setMovementType() and getMovementType()
        $movementType = 'inbound';
        $this->bookingMethod->setMovementType($movementType);
        self::assertEquals($movementType, $this->bookingMethod->getMovementType());

        // Test setDescription() and getDescription()
        $description = 'Test description';
        $this->bookingMethod->setDescription($description);
        self::assertEquals($description, $this->bookingMethod->getDescription());

        // Test setAnsteuerung() and getAnsteuerung()
        $ansteuerung = 3;
        $this->bookingMethod->setAnsteuerung($ansteuerung);
        self::assertEquals($ansteuerung, $this->bookingMethod->getAnsteuerung());

        // Test setUpload() and getUpload()
        $upload = 4;
        $this->bookingMethod->setUpload($upload);
        self::assertEquals($upload, $this->bookingMethod->getUpload());

        // Test setStatistics() and getStatistics()
        $statistics = 5;
        $this->bookingMethod->setStatistics($statistics);
        self::assertEquals($statistics, $this->bookingMethod->getStatistics());

        // Test setPriority() and getPriority()
        $priority = 6;
        $this->bookingMethod->setPriority($priority);
        self::assertEquals($priority, $this->bookingMethod->getPriority());

        // Test setTidDescription() and getTidDescription()
        $tidDescription = 7;
        $this->bookingMethod->setTidDescription($tidDescription);
        self::assertEquals($tidDescription, $this->bookingMethod->getTidDescription());
    }
}
