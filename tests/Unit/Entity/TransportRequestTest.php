<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\TransportRequest;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequestTest
 *
 * @covers \WebWMS\Entity\TransportRequest
 */
final class TransportRequestTest extends TestCase
{
    private TransportRequest $transportRequest;
    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transportRequest = new TransportRequest();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->transportRequest);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('id');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('id');
        $this->transportRequest->setId($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetSuId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('suId');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getSuId());
    }

    public function testSetSuId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('suId');
        $this->transportRequest->setSuId($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trNr');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrNr());
    }

    public function testSetTrNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trNr');
        $this->transportRequest->setTrNr($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrPos(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trPos');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrPos());
    }

    public function testSetTrPos(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trPos');
        $this->transportRequest->setTrPos($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrPrio(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trPrio');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrPrio());
    }

    public function testSetTrPrio(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trPrio');
        $this->transportRequest->setTrPrio($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetArtNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('articleNr');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getArtNr());
    }

    public function testSetArtNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('articleNr');
        $this->transportRequest->setArtNr($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrQuantity(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trQuantity');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrQuantity());
    }

    public function testSetTrQuantity(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trQuantity');
        $this->transportRequest->setTrQuantity($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetStockCoordinate(): void
    {
        $expected = 'stockCoordinate';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockCoordinate');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getStockCoordinate());
    }

    public function testSetStockCoordinate(): void
    {
        $expected = 'stockCoordinate';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockCoordinate');
        $this->transportRequest->setStockCoordinate($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetStockNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockNr');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getStockNr());
    }

    public function testSetStockNr(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockNr');
        $this->transportRequest->setStockNr($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetStockLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockLevel1');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getStockLevel1());
    }

    public function testSetStockLevel1(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockLevel1');
        $this->transportRequest->setStockLevel1($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetStockLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockLevel2');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getStockLevel2());
    }

    public function testSetStockLevel2(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockLevel2');
        $this->transportRequest->setStockLevel2($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetStockLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockLevel3');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getStockLevel3());
    }

    public function testSetStockLevel3(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockLevel3');
        $this->transportRequest->setStockLevel3($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetStockLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockLevel4');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getStockLevel4());
    }

    public function testSetStockLevel4(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('stockLevel4');
        $this->transportRequest->setStockLevel4($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrAccess(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trAccess');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrAccess());
    }

    public function testSetTrAccess(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trAccess');
        $this->transportRequest->setTrAccess($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrDispatch(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trDispatch');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrDispatch());
    }

    public function testSetTrDispatch(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trDispatch');
        $this->transportRequest->setTrDispatch($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrState(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trState');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrState());
    }

    public function testSetTrState(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trState');
        $this->transportRequest->setTrState($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetOrderUsername(): void
    {
        $expected = 'orderUsername';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('orderUsername');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getOrderUsername());
    }

    public function testSetOrderUsername(): void
    {
        $expected = 'orderUsername';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('orderUsername');
        $this->transportRequest->setOrderUsername($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetBookingMethod(): void
    {
        $expected = 'bookingMethod';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('bookingMethod');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getBookingMethod());
    }

    public function testSetBookingMethod(): void
    {
        $expected = 'bookingMethod';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('bookingMethod');
        $this->transportRequest->setBookingMethod($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetDocId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('docId');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getDocId());
    }

    public function testSetDocId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('docId');
        $this->transportRequest->setDocId($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetOrderNr(): void
    {
        $expected = 'orderNr';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('orderNr');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getOrderNr());
    }

    public function testSetOrderNr(): void
    {
        $expected = 'orderNr';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('orderNr');
        $this->transportRequest->setOrderNr($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetOrderPos(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('orderPos');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getOrderPos());
    }

    public function testSetOrderPos(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('orderPos');
        $this->transportRequest->setOrderPos($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetCharge(): void
    {
        $expected = 'charge';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('charge');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getCharge());
    }

    public function testSetCharge(): void
    {
        $expected = 'charge';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('charge');
        $this->transportRequest->setCharge($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetLoadingEquipment(): void
    {
        $expected = 'loadingEquipment';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('loadingEquipment');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getLoadingEquipment());
    }

    public function testSetLoadingEquipment(): void
    {
        $expected = 'loadingEquipment';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('loadingEquipment');
        $this->transportRequest->setLoadingEquipment($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetConfirmationState(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('confirmationState');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getConfirmationState());
    }

    public function testSetConfirmationState(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('confirmationState');
        $this->transportRequest->setConfirmationState($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrUsername(): void
    {
        $expected = 'trUsername';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trUsername');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrUsername());
    }

    public function testSetTrUsername(): void
    {
        $expected = 'trUsername';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trUsername');
        $this->transportRequest->setTrUsername($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrComputerIp(): void
    {
        $expected = 'trComputerIp';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trComputerIp');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrComputerIp());
    }

    public function testSetTrComputerIp(): void
    {
        $expected = 'trComputerIp';
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trComputerIp');
        $this->transportRequest->setTrComputerIp($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrBlocked(): void
    {
        $expected = true;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trBlocked');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrBlocked());
    }

    public function testSetTrBlocked(): void
    {
        $expected = false;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trBlocked');
        $this->transportRequest->setTrBlocked($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrStartDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trStartDate');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrStartDate());
    }

    public function testSetTrStartDate(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trStartDate');
        $this->transportRequest->setTrStartDate($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrEdited(): void
    {
        $expected = true;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trEdited');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrEdited());
    }

    public function testSetTrEdited(): void
    {
        $expected = true;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trEdited');
        $this->transportRequest->setTrEdited($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetTrType(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trType');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getTrType());
    }

    public function testSetTrType(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('trType');
        $this->transportRequest->setTrType($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('createdAt');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('createdAt');
        $this->transportRequest->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('updatedAt');
        $property->setValue($this->transportRequest, $expected);
        $this->assertSame($expected, $this->transportRequest->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(TransportRequest::class))
            ->getProperty('updatedAt');
        $this->transportRequest->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->transportRequest));
    }
}
