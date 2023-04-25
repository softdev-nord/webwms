<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\TransportHistory;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportHistoryTest
 *
 * @covers \WebWMS\Entity\TransportHistory
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class TransportHistoryTest extends TestCase
{
    private TransportHistory $transportHistory;
    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transportHistory = new TransportHistory();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->transportHistory);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('id');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('id');
        $this->transportHistory->setId($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetSuId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('suId');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getSuId());
    }

    public function testSetSuId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('suId');
        $this->transportHistory->setSuId($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trNr');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrNr());
    }

    public function testSetTrNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trNr');
        $this->transportHistory->setTrNr($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrPos(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trPos');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrPos());
    }

    public function testSetTrPos(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trPos');
        $this->transportHistory->setTrPos($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrPrio(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trPrio');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrPrio());
    }

    public function testSetTrPrio(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trPrio');
        $this->transportHistory->setTrPrio($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetArtNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('articleNr');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getArtNr());
    }

    public function testSetArtNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('articleNr');
        $this->transportHistory->setArtNr($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrQuantity(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trQuantity');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrQuantity());
    }

    public function testSetTrQuantity(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trQuantity');
        $this->transportHistory->setTrQuantity($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetStockCoordinate(): void
    {
        $expected = 'stockCoordinate';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockCoordinate');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getStockCoordinate());
    }

    public function testSetStockCoordinate(): void
    {
        $expected = 'stockCoordinate';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockCoordinate');
        $this->transportHistory->setStockCoordinate($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetStockNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockNr');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getStockNr());
    }

    public function testSetStockNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockNr');
        $this->transportHistory->setStockNr($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetStockLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockLevel1');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getStockLevel1());
    }

    public function testSetStockLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockLevel1');
        $this->transportHistory->setStockLevel1($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetStockLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockLevel2');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getStockLevel2());
    }

    public function testSetStockLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockLevel2');
        $this->transportHistory->setStockLevel2($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetStockLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockLevel3');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getStockLevel3());
    }

    public function testSetStockLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockLevel3');
        $this->transportHistory->setStockLevel3($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetStockLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockLevel4');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getStockLevel4());
    }

    public function testSetStockLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('stockLevel4');
        $this->transportHistory->setStockLevel4($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrAccess(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trAccess');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrAccess());
    }

    public function testSetTrAccess(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trAccess');
        $this->transportHistory->setTrAccess($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrDispatch(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trDispatch');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrDispatch());
    }

    public function testSetTrDispatch(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trDispatch');
        $this->transportHistory->setTrDispatch($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrState(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trState');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrState());
    }

    public function testSetTrState(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trState');
        $this->transportHistory->setTrState($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetOrderUsername(): void
    {
        $expected = 'orderUsername';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('orderUsername');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getOrderUsername());
    }

    public function testSetOrderUsername(): void
    {
        $expected = 'orderUsername';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('orderUsername');
        $this->transportHistory->setOrderUsername($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetBookingMethod(): void
    {
        $expected = 'bookingMethod';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('bookingMethod');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getBookingMethod());
    }

    public function testSetBookingMethod(): void
    {
        $expected = 'bookingMethod';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('bookingMethod');
        $this->transportHistory->setBookingMethod($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetDocId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('docId');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getDocId());
    }

    public function testSetDocId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('docId');
        $this->transportHistory->setDocId($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetOrderNr(): void
    {
        $expected = 'orderNr';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('orderNr');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getOrderNr());
    }

    public function testSetOrderNr(): void
    {
        $expected = 'orderNr';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('orderNr');
        $this->transportHistory->setOrderNr($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetOrderPos(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('orderPos');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getOrderPos());
    }

    public function testSetOrderPos(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('orderPos');
        $this->transportHistory->setOrderPos($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetCharge(): void
    {
        $expected = 'charge';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('charge');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getCharge());
    }

    public function testSetCharge(): void
    {
        $expected = 'charge';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('charge');
        $this->transportHistory->setCharge($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetLoadingEquipment(): void
    {
        $expected = 'loadingEquipment';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('loadingEquipment');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getLoadingEquipment());
    }

    public function testSetLoadingEquipment(): void
    {
        $expected = 'loadingEquipment';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('loadingEquipment');
        $this->transportHistory->setLoadingEquipment($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetConfirmationState(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('confirmationState');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getConfirmationState());
    }

    public function testSetConfirmationState(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('confirmationState');
        $this->transportHistory->setConfirmationState($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrUsername(): void
    {
        $expected = 'trUsername';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trUsername');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrUsername());
    }

    public function testSetTrUsername(): void
    {
        $expected = 'trUsername';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trUsername');
        $this->transportHistory->setTrUsername($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrComputerIp(): void
    {
        $expected = 'trComputerIp';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trComputerIp');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrComputerIp());
    }

    public function testSetTrComputerIp(): void
    {
        $expected = 'trComputerIp';
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trComputerIp');
        $this->transportHistory->setTrComputerIp($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrBlocked(): void
    {
        $expected = true;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trBlocked');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrBlocked());
    }

    public function testSetTrBlocked(): void
    {
        $expected = false;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trBlocked');
        $this->transportHistory->setTrBlocked($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrStartDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trStartDate');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrStartDate());
    }

    public function testSetTrStartDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trStartDate');
        $this->transportHistory->setTrStartDate($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrEdited(): void
    {
        $expected = true;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trEdited');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrEdited());
    }

    public function testSetTrEdited(): void
    {
        $expected = true;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trEdited');
        $this->transportHistory->setTrEdited($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetTrType(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trType');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getTrType());
    }

    public function testSetTrType(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('trType');
        $this->transportHistory->setTrType($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('createdAt');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('createdAt');
        $this->transportHistory->setCreatedAt($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('updatedAt');
        $property->setValue($this->transportHistory, $expected);
        self::assertSame($expected, $this->transportHistory->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportHistory::class))
            ->getProperty('updatedAt');
        $this->transportHistory->setUpdatedAt($expected);
        self::assertSame($expected, $property->getValue($this->transportHistory));
    }
}
