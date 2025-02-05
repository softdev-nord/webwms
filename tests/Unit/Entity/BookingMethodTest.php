<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\BookingMethod;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'BookingMethodTest'
)]
#[CoversClass(BookingMethod::class)]
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
        self::assertSame($id, $this->bookingMethod->getId());

        // Test setConfirmation() and getConfirmation()
        $confirmation = 2;
        $this->bookingMethod->setConfirmation($confirmation);
        self::assertSame($confirmation, $this->bookingMethod->getConfirmation());

        // Test setMovementType() and getMovementType()
        $movementType = 'inbound';
        $this->bookingMethod->setMovementType($movementType);
        self::assertSame($movementType, $this->bookingMethod->getMovementType());

        // Test setDescription() and getDescription()
        $description = 'Test description';
        $this->bookingMethod->setDescription($description);
        self::assertSame($description, $this->bookingMethod->getDescription());

        // Test setAnsteuerung() and getAnsteuerung()
        $ansteuerung = 3;
        $this->bookingMethod->setAnsteuerung($ansteuerung);
        self::assertSame($ansteuerung, $this->bookingMethod->getAnsteuerung());

        // Test setUpload() and getUpload()
        $upload = 4;
        $this->bookingMethod->setUpload($upload);
        self::assertSame($upload, $this->bookingMethod->getUpload());

        // Test setStatistics() and getStatistics()
        $statistics = 5;
        $this->bookingMethod->setStatistics($statistics);
        self::assertSame($statistics, $this->bookingMethod->getStatistics());

        // Test setPriority() and getPriority()
        $priority = 6;
        $this->bookingMethod->setPriority($priority);
        self::assertSame($priority, $this->bookingMethod->getPriority());

        // Test setTidDescription() and getTidDescription()
        $tidDescription = 7;
        $this->bookingMethod->setTidDescription($tidDescription);
        self::assertSame($tidDescription, $this->bookingMethod->getTidDescription());
    }
}
