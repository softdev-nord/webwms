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
    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transportHistory = new TransportHistory();
        $this->dateTime = new \DateTime();
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGettersAndSetters(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->transportHistory->setId($id);
        self::assertEquals($id, $this->transportHistory->getId());

        // Test setSuId() and getSuId()
        $suId = 1;
        $this->transportHistory->setSuId($suId);
        self::assertSame($suId, $this->transportHistory->getSuId());

        // Test setTrNr() and getTrNr()
        $trNr = 10000;
        $this->transportHistory->setTrNr($trNr);
        self::assertSame($trNr, $this->transportHistory->getTrNr());

        // Test setTrPos() and getTrPos()
        $trPos = 1;
        $this->transportHistory->setTrPos($trPos);
        self::assertSame($trPos, $this->transportHistory->getTrPos());

        // Test setTrPrio() and getTrPrio()
        $trPrio = 1;
        $this->transportHistory->setTrPrio($trPrio);
        self::assertSame($trPrio, $this->transportHistory->getTrPrio());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->transportHistory->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->transportHistory->getArticleNr());

        // Test setTrQuantity() and getTrQuantity()
        $trQuantity = 80.000;
        $this->transportHistory->setTrQuantity($trQuantity);
        self::assertEquals($trQuantity, $this->transportHistory->getTrQuantity());

        // Test setStockCoordinate() and getStockCoordinate()
        $stockCoordinate = '101000100010001';
        $this->transportHistory->setStockCoordinate($stockCoordinate);
        self::assertEquals($stockCoordinate, $this->transportHistory->getStockCoordinate());

        // Test setStockNr() and getStockNr()
        $stockNr = 100;
        $this->transportHistory->setStockNr($stockNr);
        self::assertEquals($stockNr, $this->transportHistory->getStockNr());

        // Test setStockLevel1() and getStockLevel1()
        $stockLevel1 = 1;
        $this->transportHistory->setStockLevel1($stockLevel1);
        self::assertEquals($stockLevel1, $this->transportHistory->getStockLevel1());

        // Test setStockLevel2() and getStockLevel2()
        $stockLevel2 = 1;
        $this->transportHistory->setStockLevel2($stockLevel2);
        self::assertEquals($stockLevel2, $this->transportHistory->getStockLevel2());

        // Test setStockLevel3() and getStockLevel3()
        $stockLevel3 = 1;
        $this->transportHistory->setStockLevel3($stockLevel3);
        self::assertEquals($stockLevel3, $this->transportHistory->getStockLevel3());

        // Test setStockLevel4() and getStockLevel4()
        $stockLevel4 = 1;
        $this->transportHistory->setStockLevel4($stockLevel4);
        self::assertEquals($stockLevel4, $this->transportHistory->getStockLevel4());

        // Test setTrAccess() and getTrAccess()
        $trAccess = $this->dateTime;
        $this->transportHistory->setTrAccess($trAccess);
        self::assertEquals($trAccess, $this->transportHistory->getTrAccess());

        // Test setTrDispatch() and getTrDispatch()
        $trDispatch = $this->dateTime;
        $this->transportHistory->setTrDispatch($trDispatch);
        self::assertEquals($trDispatch, $this->transportHistory->getTrDispatch());

        // Test setTrState() and getTrState()
        $trState = 1;
        $this->transportHistory->setTrState($trState);
        self::assertEquals($trState, $this->transportHistory->getTrState());

        // Test setOrderUsername() and getOrderUsername()
        $orderUsername = 'rirrgang';
        $this->transportHistory->setOrderUsername($orderUsername);
        self::assertEquals($orderUsername, $this->transportHistory->getOrderUsername());

        // Test setBookingMethod() and getBookingMethod()
        $bookingMethod = 'WA202';
        $this->transportHistory->setBookingMethod($bookingMethod);
        self::assertEquals($bookingMethod, $this->transportHistory->getBookingMethod());

        // Test setDocId() and getDocId()
        $docId = 710000;
        $this->transportHistory->setDocId($docId);
        self::assertEquals($docId, $this->transportHistory->getDocId());

        // Test setOrderNr() and getOrderNr()
        $orderNr = 'VLS-01-710000';
        $this->transportHistory->setOrderNr($orderNr);
        self::assertEquals($orderNr, $this->transportHistory->getOrderNr());

        // Test setOrderPos() and getOrderPos()
        $orderPos = 1;
        $this->transportHistory->setOrderPos($orderPos);
        self::assertEquals($orderPos, $this->transportHistory->getOrderPos());

        // Test setCharge() and getCharge()
        $charge = '000-000-000';
        $this->transportHistory->setCharge($charge);
        self::assertEquals($charge, $this->transportHistory->getCharge());

        // Test setLoadingEquipment() and getLoadingEquipment()
        $loadingEquipment = 'PAL200';
        $this->transportHistory->setLoadingEquipment($loadingEquipment);
        self::assertEquals($loadingEquipment, $this->transportHistory->getLoadingEquipment());

        // Test setConfirmationState() and getConfirmationState()
        $confirmationState = 0;
        $this->transportHistory->setConfirmationState($confirmationState);
        self::assertEquals($confirmationState, $this->transportHistory->getConfirmationState());

        // Test setTrUsername() and getTrUsername()
        $trUsername = 'karlfeld';
        $this->transportHistory->setTrUsername($trUsername);
        self::assertEquals($trUsername, $this->transportHistory->getTrUsername());

        // Test setTrComputerIp() and getTrComputerIp()
        $trComputerIp = '123.123.123.123';
        $this->transportHistory->setTrComputerIp($trComputerIp);
        self::assertEquals($trComputerIp, $this->transportHistory->getTrComputerIp());

        // Test setTrBlocked() and getTrBlocked()
        $this->transportHistory->setTrBlocked(true);
        self::assertTrue($this->transportHistory->getTrBlocked());

        // Test setTrStartDate() and getTrStartDate()
        $trStartDate = $this->dateTime;
        $this->transportHistory->setTrStartDate($trStartDate);
        self::assertEquals($trStartDate, $this->transportHistory->getTrStartDate());

        // Test setTrEdited() and getTrEdited()
        $this->transportHistory->setTrEdited(false);
        self::assertFalse($this->transportHistory->getTrEdited());

        // Test setTrType() and getTrType()
        $trType = 1;
        $this->transportHistory->setTrType($trType);
        self::assertEquals($trType, $this->transportHistory->getTrType());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->transportHistory->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->transportHistory->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->transportHistory->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->transportHistory->getUpdatedAt());
    }
}
