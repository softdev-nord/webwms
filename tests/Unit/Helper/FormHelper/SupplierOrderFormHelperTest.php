<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Entity\SupplierOrderPos;
use WebWMS\Form\SupplierOrder\AddSupplierOrderType;
use WebWMS\Form\SupplierOrder\DeleteSupplierOrderType;
use WebWMS\Form\SupplierOrder\EditSupplierOrderType;
use WebWMS\Form\SupplierOrder\SupplierOrderPosType;
use WebWMS\Helper\FormHelper\SupplierOrderFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderFormHelperTest
 *
 * @covers \WebWMS\Helper\FormHelper\SupplierOrderFormHelper
 */
final class SupplierOrderFormHelperTest extends TestCase
{
    public function testCreateForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $type = 'SomeType';
        $data = null;
        $options = [];

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $helper = new SupplierOrderFormHelper($formFactory);
        $result = $helper->createForm($type, $data, $options);

        self::assertSame($formInterface, $result);
    }

    public function testAddSupplierOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(AddSupplierOrderType::class)
            ->willReturn($formInterface);

        $helper = new SupplierOrderFormHelper($formFactory);
        $result = $helper->addSupplierOrderForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditSupplierOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplierOrder = $this->createMock(SupplierOrder::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditSupplierOrderType::class, $supplierOrder)
            ->willReturn($formInterface);

        $helper = new SupplierOrderFormHelper($formFactory);
        $result = $helper->editSupplierOrderForm($supplierOrder);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteSupplierOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplierOrder = $this->createMock(SupplierOrder::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteSupplierOrderType::class, $supplierOrder)
            ->willReturn($formInterface);

        $helper = new SupplierOrderFormHelper($formFactory);
        $result = $helper->deleteSupplierOrderForm($supplierOrder);

        self::assertSame($formInterface, $result);
    }

    public function testAddSupplierOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(SupplierOrderPosType::class)
            ->willReturn($formInterface);

        $helper = new SupplierOrderFormHelper($formFactory);
        $result = $helper->addSupplierOrderPosForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditSupplierOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplierOrder = $this->createMock(SupplierOrderPos::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(SupplierOrderPosType::class, $supplierOrder)
            ->willReturn($formInterface);

        $helper = new SupplierOrderFormHelper($formFactory);
        $result = $helper->editSupplierOrderPosForm($supplierOrder);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteSupplierOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplierOrder = $this->createMock(SupplierOrderPos::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(SupplierOrderPosType::class, $supplierOrder)
            ->willReturn($formInterface);

        $helper = new SupplierOrderFormHelper($formFactory);
        $result = $helper->deleteSupplierOrderPosForm($supplierOrder);

        self::assertSame($formInterface, $result);
    }
}
