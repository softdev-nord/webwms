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
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class TransportRequestTest extends TestCase
{
    private TransportRequest $transportRequest;
    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transportRequest = new TransportRequest();
        $this->dateTime = new \DateTime();
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGettersAndSetters(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->transportRequest->setId($id);
        self::assertEquals($id, $this->transportRequest->getId());

        // Test setSuId() and getSuId()
        $suId = 1;
        $this->transportRequest->setSuId($suId);
        self::assertSame($suId, $this->transportRequest->getSuId());

        // Test setTrNr() and getTrNr()
        $trNr = 10000;
        $this->transportRequest->setTrNr($trNr);
        self::assertSame($trNr, $this->transportRequest->getTrNr());

        // Test setTrPos() and getTrPos()
        $trPos = 1;
        $this->transportRequest->setTrPos($trPos);
        self::assertSame($trPos, $this->transportRequest->getTrPos());

        // Test setTrPrio() and getTrPrio()
        $trPrio = 1;
        $this->transportRequest->setTrPrio($trPrio);
        self::assertSame($trPrio, $this->transportRequest->getTrPrio());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->transportRequest->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->transportRequest->getArticleNr());

        // Test setTrQuantity() and getTrQuantity()
        $trQuantity = 80.000;
        $this->transportRequest->setTrQuantity($trQuantity);
        self::assertEquals($trQuantity, $this->transportRequest->getTrQuantity());

        // Test setStockCoordinate() and getStockCoordinate()
        $stockCoordinate = '101000100010001';
        $this->transportRequest->setStockCoordinate($stockCoordinate);
        self::assertEquals($stockCoordinate, $this->transportRequest->getStockCoordinate());

        // Test setStockNr() and getStockNr()
        $stockNr = 100;
        $this->transportRequest->setStockNr($stockNr);
        self::assertEquals($stockNr, $this->transportRequest->getStockNr());

        // Test setStockLevel1() and getStockLevel1()
        $stockLevel1 = 1;
        $this->transportRequest->setStockLevel1($stockLevel1);
        self::assertEquals($stockLevel1, $this->transportRequest->getStockLevel1());

        // Test setStockLevel2() and getStockLevel2()
        $stockLevel2 = 1;
        $this->transportRequest->setStockLevel2($stockLevel2);
        self::assertEquals($stockLevel2, $this->transportRequest->getStockLevel2());

        // Test setStockLevel3() and getStockLevel3()
        $stockLevel3 = 1;
        $this->transportRequest->setStockLevel3($stockLevel3);
        self::assertEquals($stockLevel3, $this->transportRequest->getStockLevel3());

        // Test setStockLevel4() and getStockLevel4()
        $stockLevel4 = 1;
        $this->transportRequest->setStockLevel4($stockLevel4);
        self::assertEquals($stockLevel4, $this->transportRequest->getStockLevel4());

        // Test setTrAccess() and getTrAccess()
        $trAccess = $this->dateTime;
        $this->transportRequest->setTrAccess($trAccess);
        self::assertEquals($trAccess, $this->transportRequest->getTrAccess());

        // Test setTrDispatch() and getTrDispatch()
        $trDispatch = $this->dateTime;
        $this->transportRequest->setTrDispatch($trDispatch);
        self::assertEquals($trDispatch, $this->transportRequest->getTrDispatch());

        // Test setTrState() and getTrState()
        $trState = 1;
        $this->transportRequest->setTrState($trState);
        self::assertEquals($trState, $this->transportRequest->getTrState());

        // Test setOrderUsername() and getOrderUsername()
        $orderUsername = 'rirrgang';
        $this->transportRequest->setOrderUsername($orderUsername);
        self::assertEquals($orderUsername, $this->transportRequest->getOrderUsername());

        // Test setBookingMethod() and getBookingMethod()
        $bookingMethod = 'WA202';
        $this->transportRequest->setBookingMethod($bookingMethod);
        self::assertEquals($bookingMethod, $this->transportRequest->getBookingMethod());

        // Test setDocId() and getDocId()
        $docId = 710000;
        $this->transportRequest->setDocId($docId);
        self::assertEquals($docId, $this->transportRequest->getDocId());

        // Test setOrderNr() and getOrderNr()
        $orderNr = 'VLS-01-710000';
        $this->transportRequest->setOrderNr($orderNr);
        self::assertEquals($orderNr, $this->transportRequest->getOrderNr());

        // Test setOrderPos() and getOrderPos()
        $orderPos = 1;
        $this->transportRequest->setOrderPos($orderPos);
        self::assertEquals($orderPos, $this->transportRequest->getOrderPos());

        // Test setCharge() and getCharge()
        $charge = '000-000-000';
        $this->transportRequest->setCharge($charge);
        self::assertEquals($charge, $this->transportRequest->getCharge());

        // Test setLoadingEquipment() and getLoadingEquipment()
        $loadingEquipment = 'PAL200';
        $this->transportRequest->setLoadingEquipment($loadingEquipment);
        self::assertEquals($loadingEquipment, $this->transportRequest->getLoadingEquipment());

        // Test setConfirmationState() and getConfirmationState()
        $confirmationState = 0;
        $this->transportRequest->setConfirmationState($confirmationState);
        self::assertEquals($confirmationState, $this->transportRequest->getConfirmationState());

        // Test setTrUsername() and getTrUsername()
        $trUsername = 'karlfeld';
        $this->transportRequest->setTrUsername($trUsername);
        self::assertEquals($trUsername, $this->transportRequest->getTrUsername());

        // Test setTrComputerIp() and getTrComputerIp()
        $trComputerIp = '123.123.123.123';
        $this->transportRequest->setTrComputerIp($trComputerIp);
        self::assertEquals($trComputerIp, $this->transportRequest->getTrComputerIp());

        // Test setTrBlocked() and getTrBlocked()
        $this->transportRequest->setTrBlocked(true);
        self::assertTrue($this->transportRequest->getTrBlocked());

        // Test setTrStartDate() and getTrStartDate()
        $trStartDate = $this->dateTime;
        $this->transportRequest->setTrStartDate($trStartDate);
        self::assertEquals($trStartDate, $this->transportRequest->getTrStartDate());

        // Test setTrEdited() and getTrEdited()
        $this->transportRequest->setTrEdited(false);
        self::assertFalse($this->transportRequest->getTrEdited());

        // Test setTrType() and getTrType()
        $trType = 1;
        $this->transportRequest->setTrType($trType);
        self::assertEquals($trType, $this->transportRequest->getTrType());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->transportRequest->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->transportRequest->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->transportRequest->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->transportRequest->getUpdatedAt());
    }
}
