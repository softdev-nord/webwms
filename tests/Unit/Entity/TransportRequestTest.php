<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\TransportRequestEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        TransportRequestTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
#[CoversClass(TransportRequestEntity::class)]
final class TransportRequestTest extends TestCase
{
    private TransportRequestEntity $transportRequestEntity;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transportRequestEntity = new TransportRequestEntity();
        $this->dateTime = new DateTime();
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGettersAndSetters(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->transportRequestEntity->setId($id);
        self::assertEquals($id, $this->transportRequestEntity->getId());

        // Test setSuId() and getSuId()
        $suId = 1;
        $this->transportRequestEntity->setSuId($suId);
        self::assertSame($suId, $this->transportRequestEntity->getSuId());

        // Test setTrNr() and getTrNr()
        $trNr = 10000;
        $this->transportRequestEntity->setTrNr($trNr);
        self::assertSame($trNr, $this->transportRequestEntity->getTrNr());

        // Test setTrPos() and getTrPos()
        $trPos = 1;
        $this->transportRequestEntity->setTrPos($trPos);
        self::assertSame($trPos, $this->transportRequestEntity->getTrPos());

        // Test setTrPrio() and getTrPrio()
        $trPrio = 1;
        $this->transportRequestEntity->setTrPrio($trPrio);
        self::assertSame($trPrio, $this->transportRequestEntity->getTrPrio());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->transportRequestEntity->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->transportRequestEntity->getArticleNr());

        // Test setTrQuantity() and getTrQuantity()
        $trQuantity = 80.000;
        $this->transportRequestEntity->setTrQuantity($trQuantity);
        self::assertEquals($trQuantity, $this->transportRequestEntity->getTrQuantity());

        // Test setStockCoordinate() and getStockCoordinate()
        $stockCoordinate = '101000100010001';
        $this->transportRequestEntity->setStockCoordinate($stockCoordinate);
        self::assertEquals($stockCoordinate, $this->transportRequestEntity->getStockCoordinate());

        // Test setStockNr() and getStockNr()
        $stockNr = 100;
        $this->transportRequestEntity->setStockNr($stockNr);
        self::assertEquals($stockNr, $this->transportRequestEntity->getStockNr());

        // Test setStockLevel1() and getStockLevel1()
        $stockLevel1 = 1;
        $this->transportRequestEntity->setStockLevel1($stockLevel1);
        self::assertEquals($stockLevel1, $this->transportRequestEntity->getStockLevel1());

        // Test setStockLevel2() and getStockLevel2()
        $stockLevel2 = 1;
        $this->transportRequestEntity->setStockLevel2($stockLevel2);
        self::assertEquals($stockLevel2, $this->transportRequestEntity->getStockLevel2());

        // Test setStockLevel3() and getStockLevel3()
        $stockLevel3 = 1;
        $this->transportRequestEntity->setStockLevel3($stockLevel3);
        self::assertEquals($stockLevel3, $this->transportRequestEntity->getStockLevel3());

        // Test setStockLevel4() and getStockLevel4()
        $stockLevel4 = 1;
        $this->transportRequestEntity->setStockLevel4($stockLevel4);
        self::assertEquals($stockLevel4, $this->transportRequestEntity->getStockLevel4());

        // Test setTrAccess() and getTrAccess()
        $trAccess = $this->dateTime;
        $this->transportRequestEntity->setTrAccess($trAccess);
        self::assertEquals($trAccess, $this->transportRequestEntity->getTrAccess());

        // Test setTrDispatch() and getTrDispatch()
        $trDispatch = $this->dateTime;
        $this->transportRequestEntity->setTrDispatch($trDispatch);
        self::assertEquals($trDispatch, $this->transportRequestEntity->getTrDispatch());

        // Test setTrState() and getTrState()
        $trState = 1;
        $this->transportRequestEntity->setTrState($trState);
        self::assertEquals($trState, $this->transportRequestEntity->getTrState());

        // Test setOrderUsername() and getOrderUsername()
        $orderUsername = 'rirrgang';
        $this->transportRequestEntity->setOrderUsername($orderUsername);
        self::assertEquals($orderUsername, $this->transportRequestEntity->getOrderUsername());

        // Test setBookingMethod() and getBookingMethod()
        $bookingMethod = 'WA202';
        $this->transportRequestEntity->setBookingMethod($bookingMethod);
        self::assertEquals($bookingMethod, $this->transportRequestEntity->getBookingMethod());

        // Test setDocId() and getDocId()
        $docId = 710000;
        $this->transportRequestEntity->setDocId($docId);
        self::assertEquals($docId, $this->transportRequestEntity->getDocId());

        // Test setOrderNr() and getOrderNr()
        $orderNr = 'VLS-01-710000';
        $this->transportRequestEntity->setOrderNr($orderNr);
        self::assertEquals($orderNr, $this->transportRequestEntity->getOrderNr());

        // Test setOrderPos() and getOrderPos()
        $orderPos = 1;
        $this->transportRequestEntity->setOrderPos($orderPos);
        self::assertEquals($orderPos, $this->transportRequestEntity->getOrderPos());

        // Test setCharge() and getCharge()
        $charge = '000-000-000';
        $this->transportRequestEntity->setCharge($charge);
        self::assertEquals($charge, $this->transportRequestEntity->getCharge());

        // Test setLoadingEquipment() and getLoadingEquipment()
        $loadingEquipment = 'PAL200';
        $this->transportRequestEntity->setLoadingEquipment($loadingEquipment);
        self::assertEquals($loadingEquipment, $this->transportRequestEntity->getLoadingEquipment());

        // Test setConfirmationState() and getConfirmationState()
        $confirmationState = 0;
        $this->transportRequestEntity->setConfirmationState($confirmationState);
        self::assertEquals($confirmationState, $this->transportRequestEntity->getConfirmationState());

        // Test setTrUsername() and getTrUsername()
        $trUsername = 'karlfeld';
        $this->transportRequestEntity->setTrUsername($trUsername);
        self::assertEquals($trUsername, $this->transportRequestEntity->getTrUsername());

        // Test setTrComputerIp() and getTrComputerIp()
        $trComputerIp = '123.123.123.123';
        $this->transportRequestEntity->setTrComputerIp($trComputerIp);
        self::assertEquals($trComputerIp, $this->transportRequestEntity->getTrComputerIp());

        // Test setTrBlocked() and getTrBlocked()
        $this->transportRequestEntity->setTrBlocked(true);
        self::assertTrue($this->transportRequestEntity->getTrBlocked());

        // Test setTrStartDate() and getTrStartDate()
        $trStartDate = $this->dateTime;
        $this->transportRequestEntity->setTrStartDate($trStartDate);
        self::assertEquals($trStartDate, $this->transportRequestEntity->getTrStartDate());

        // Test setTrEdited() and getTrEdited()
        $this->transportRequestEntity->setTrEdited(false);
        self::assertFalse($this->transportRequestEntity->getTrEdited());

        // Test setTrType() and getTrType()
        $trType = 1;
        $this->transportRequestEntity->setTrType($trType);
        self::assertEquals($trType, $this->transportRequestEntity->getTrType());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->transportRequestEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->transportRequestEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->transportRequestEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->transportRequestEntity->getUpdatedAt());
    }
}
