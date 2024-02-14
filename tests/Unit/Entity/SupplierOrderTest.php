<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\SupplierEntity;
use WebWMS\Entity\SupplierOrderEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderTest
 */
#[CoversClass(SupplierOrderEntity::class)]
final class SupplierOrderTest extends TestCase
{
    private SupplierOrderEntity $supplierOrderEntity;

    private DateTime $dateTime;

    private ArrayCollection $collection;

    private SupplierEntity $supplierEntity;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->supplierOrderEntity = new SupplierOrderEntity();
        $this->dateTime = new DateTime();
        $this->collection = new ArrayCollection();
        $this->supplierEntity = new SupplierEntity();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 710000;
        $this->supplierOrderEntity->setId($id);
        self::assertEquals($id, $this->supplierOrderEntity->getId());

        // Test setSupplierOrderId() and getSupplierOrderId()
        $supplierOrderId = 100000;
        $this->supplierOrderEntity->setSupplierOrderId($supplierOrderId);
        self::assertEquals($supplierOrderId, $this->supplierOrderEntity->getSupplierOrderId());

        // Test setUsrId() and getUsrId()
        $usrId = 1;
        $this->supplierOrderEntity->setUsrId($usrId);
        self::assertEquals($usrId, $this->supplierOrderEntity->getUsrId());

        // Test setSupplierId() and getSupplierId()
        $supplierId = 1;
        $this->supplierOrderEntity->setSupplierId($supplierId);
        self::assertEquals($supplierId, $this->supplierOrderEntity->getSupplierId());

        // Test setSupplierOrderNr() and getSupplierOrderNr()
        $supplierOrderNr = 'EBE-01-100000';
        $this->supplierOrderEntity->setSupplierOrderNr($supplierOrderNr);
        self::assertEquals($supplierOrderNr, $this->supplierOrderEntity->getSupplierOrderNr());

        // Test setSupplierOrderReference() and getSupplierOrderReference()
        $supplierOrderReference = 'Test Referenz';
        $this->supplierOrderEntity->setSupplierOrderReference($supplierOrderReference);
        self::assertEquals($supplierOrderReference, $this->supplierOrderEntity->getSupplierOrderReference());

        // Test setSupplierOrderCreationDate() and getSupplierOrderCreationDate()
        $supplierOrderCreationDate = $this->dateTime;
        $this->supplierOrderEntity->setSupplierOrderCreationDate($supplierOrderCreationDate);
        self::assertEquals($supplierOrderCreationDate, $this->supplierOrderEntity->getSupplierOrderCreationDate());

        // Test setSupplierOrderDate() and getSupplierOrderDate()
        $supplierOrderDate = $this->dateTime;
        $this->supplierOrderEntity->setSupplierOrderDate($supplierOrderDate);
        self::assertEquals($supplierOrderDate, $this->supplierOrderEntity->getSupplierOrderDate());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->supplierOrderEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->supplierOrderEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->supplierOrderEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->supplierOrderEntity->getUpdatedAt());

        // Test getSupplierOrderPos()
        $supplierOrderPos = $this->collection;
        self::assertEquals($supplierOrderPos, $this->supplierOrderEntity->getSupplierOrderPos());

        // Test setSupplier() and getSupplier()
        $supplier = $this->supplierEntity;
        $this->supplierOrderEntity->setSupplier($supplier);
        self::assertEquals($supplier, $this->supplierOrderEntity->getSupplier());
    }
}
