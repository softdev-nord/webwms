<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\TransportHistoryEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        TransportHistoryTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
#[CoversClass(TransportHistoryEntity::class)]
final class TransportHistoryTest extends TestCase
{
    private TransportHistoryEntity $transportHistoryEntity;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transportHistoryEntity = new TransportHistoryEntity();
        $this->dateTime = new DateTime();
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGettersAndSetters(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->transportHistoryEntity->setId($id);
        self::assertEquals($id, $this->transportHistoryEntity->getId());

        // Test setSuId() and getSuId()
        $suId = 1;
        $this->transportHistoryEntity->setSuId($suId);
        self::assertSame($suId, $this->transportHistoryEntity->getSuId());

        // Test setTrNr() and getTrNr()
        $trNr = 10000;
        $this->transportHistoryEntity->setTrNr($trNr);
        self::assertSame($trNr, $this->transportHistoryEntity->getTrNr());

        // Test setTrPos() and getTrPos()
        $trPos = 1;
        $this->transportHistoryEntity->setTrPos($trPos);
        self::assertSame($trPos, $this->transportHistoryEntity->getTrPos());

        // Test setTrPrio() and getTrPrio()
        $trPrio = 1;
        $this->transportHistoryEntity->setTrPrio($trPrio);
        self::assertSame($trPrio, $this->transportHistoryEntity->getTrPrio());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->transportHistoryEntity->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->transportHistoryEntity->getArticleNr());

        // Test setTrQuantity() and getTrQuantity()
        $trQuantity = 80.000;
        $this->transportHistoryEntity->setTrQuantity($trQuantity);
        self::assertEquals($trQuantity, $this->transportHistoryEntity->getTrQuantity());

        // Test setStockCoordinate() and getStockCoordinate()
        $stockCoordinate = '101000100010001';
        $this->transportHistoryEntity->setStockCoordinate($stockCoordinate);
        self::assertEquals($stockCoordinate, $this->transportHistoryEntity->getStockCoordinate());

        // Test setStockNr() and getStockNr()
        $stockNr = 100;
        $this->transportHistoryEntity->setStockNr($stockNr);
        self::assertEquals($stockNr, $this->transportHistoryEntity->getStockNr());

        // Test setStockLevel1() and getStockLevel1()
        $stockLevel1 = 1;
        $this->transportHistoryEntity->setStockLevel1($stockLevel1);
        self::assertEquals($stockLevel1, $this->transportHistoryEntity->getStockLevel1());

        // Test setStockLevel2() and getStockLevel2()
        $stockLevel2 = 1;
        $this->transportHistoryEntity->setStockLevel2($stockLevel2);
        self::assertEquals($stockLevel2, $this->transportHistoryEntity->getStockLevel2());

        // Test setStockLevel3() and getStockLevel3()
        $stockLevel3 = 1;
        $this->transportHistoryEntity->setStockLevel3($stockLevel3);
        self::assertEquals($stockLevel3, $this->transportHistoryEntity->getStockLevel3());

        // Test setStockLevel4() and getStockLevel4()
        $stockLevel4 = 1;
        $this->transportHistoryEntity->setStockLevel4($stockLevel4);
        self::assertEquals($stockLevel4, $this->transportHistoryEntity->getStockLevel4());

        // Test setTrAccess() and getTrAccess()
        $trAccess = $this->dateTime;
        $this->transportHistoryEntity->setTrAccess($trAccess);
        self::assertEquals($trAccess, $this->transportHistoryEntity->getTrAccess());

        // Test setTrDispatch() and getTrDispatch()
        $trDispatch = $this->dateTime;
        $this->transportHistoryEntity->setTrDispatch($trDispatch);
        self::assertEquals($trDispatch, $this->transportHistoryEntity->getTrDispatch());

        // Test setTrState() and getTrState()
        $trState = 1;
        $this->transportHistoryEntity->setTrState($trState);
        self::assertEquals($trState, $this->transportHistoryEntity->getTrState());

        // Test setOrderUsername() and getOrderUsername()
        $orderUsername = 'rirrgang';
        $this->transportHistoryEntity->setOrderUsername($orderUsername);
        self::assertEquals($orderUsername, $this->transportHistoryEntity->getOrderUsername());

        // Test setBookingMethod() and getBookingMethod()
        $bookingMethod = 'WA202';
        $this->transportHistoryEntity->setBookingMethod($bookingMethod);
        self::assertEquals($bookingMethod, $this->transportHistoryEntity->getBookingMethod());

        // Test setDocId() and getDocId()
        $docId = 710000;
        $this->transportHistoryEntity->setDocId($docId);
        self::assertEquals($docId, $this->transportHistoryEntity->getDocId());

        // Test setOrderNr() and getOrderNr()
        $orderNr = 'VLS-01-710000';
        $this->transportHistoryEntity->setOrderNr($orderNr);
        self::assertEquals($orderNr, $this->transportHistoryEntity->getOrderNr());

        // Test setOrderPos() and getOrderPos()
        $orderPos = 1;
        $this->transportHistoryEntity->setOrderPos($orderPos);
        self::assertEquals($orderPos, $this->transportHistoryEntity->getOrderPos());

        // Test setCharge() and getCharge()
        $charge = '000-000-000';
        $this->transportHistoryEntity->setCharge($charge);
        self::assertEquals($charge, $this->transportHistoryEntity->getCharge());

        // Test setLoadingEquipment() and getLoadingEquipment()
        $loadingEquipment = 'PAL200';
        $this->transportHistoryEntity->setLoadingEquipment($loadingEquipment);
        self::assertEquals($loadingEquipment, $this->transportHistoryEntity->getLoadingEquipment());

        // Test setConfirmationState() and getConfirmationState()
        $confirmationState = 0;
        $this->transportHistoryEntity->setConfirmationState($confirmationState);
        self::assertEquals($confirmationState, $this->transportHistoryEntity->getConfirmationState());

        // Test setTrUsername() and getTrUsername()
        $trUsername = 'karlfeld';
        $this->transportHistoryEntity->setTrUsername($trUsername);
        self::assertEquals($trUsername, $this->transportHistoryEntity->getTrUsername());

        // Test setTrComputerIp() and getTrComputerIp()
        $trComputerIp = '123.123.123.123';
        $this->transportHistoryEntity->setTrComputerIp($trComputerIp);
        self::assertEquals($trComputerIp, $this->transportHistoryEntity->getTrComputerIp());

        // Test setTrBlocked() and getTrBlocked()
        $this->transportHistoryEntity->setTrBlocked(true);
        self::assertTrue($this->transportHistoryEntity->getTrBlocked());

        // Test setTrStartDate() and getTrStartDate()
        $trStartDate = $this->dateTime;
        $this->transportHistoryEntity->setTrStartDate($trStartDate);
        self::assertEquals($trStartDate, $this->transportHistoryEntity->getTrStartDate());

        // Test setTrEdited() and getTrEdited()
        $this->transportHistoryEntity->setTrEdited(false);
        self::assertFalse($this->transportHistoryEntity->getTrEdited());

        // Test setTrType() and getTrType()
        $trType = 1;
        $this->transportHistoryEntity->setTrType($trType);
        self::assertEquals($trType, $this->transportHistoryEntity->getTrType());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->transportHistoryEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->transportHistoryEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->transportHistoryEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->transportHistoryEntity->getUpdatedAt());
    }
}
