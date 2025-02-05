<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Entity\SupplierOrderPos;
use WebWMS\Form\SupplierOrder\AddSupplierOrderType;
use WebWMS\Form\SupplierOrder\DeleteSupplierOrderType;
use WebWMS\Form\SupplierOrder\EditSupplierOrderType;
use WebWMS\Form\SupplierOrder\SupplierOrderPosType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\SupplierOrderFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'SupplierOrderFormHelperTest'
)]
#[CoversClass(SupplierOrderFormHelper::class)]
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
            ->expects($this->once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $supplierOrderFormHelper = new SupplierOrderFormHelper($formFactory);
        $form = $supplierOrderFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddSupplierOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddSupplierOrderType::class)
            ->willReturn($formInterface);

        $supplierOrderFormHelper = new SupplierOrderFormHelper($formFactory);
        $form = $supplierOrderFormHelper->addSupplierOrderForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditSupplierOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplierOrder = $this->createMock(SupplierOrder::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditSupplierOrderType::class, $supplierOrder)
            ->willReturn($formInterface);

        $supplierOrderFormHelper = new SupplierOrderFormHelper($formFactory);
        $form = $supplierOrderFormHelper->editSupplierOrderForm($supplierOrder);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteSupplierOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplierOrder = $this->createMock(SupplierOrder::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteSupplierOrderType::class, $supplierOrder)
            ->willReturn($formInterface);

        $supplierOrderFormHelper = new SupplierOrderFormHelper($formFactory);
        $form = $supplierOrderFormHelper->deleteSupplierOrderForm($supplierOrder);

        self::assertSame($formInterface, $form);
    }

    public function testAddSupplierOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(SupplierOrderPosType::class)
            ->willReturn($formInterface);

        $supplierOrderFormHelper = new SupplierOrderFormHelper($formFactory);
        $form = $supplierOrderFormHelper->addSupplierOrderPosForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditSupplierOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplierOrder = $this->createMock(SupplierOrderPos::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(SupplierOrderPosType::class, $supplierOrder)
            ->willReturn($formInterface);

        $supplierOrderFormHelper = new SupplierOrderFormHelper($formFactory);
        $form = $supplierOrderFormHelper->editSupplierOrderPosForm($supplierOrder);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteSupplierOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplierOrder = $this->createMock(SupplierOrderPos::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(SupplierOrderPosType::class, $supplierOrder)
            ->willReturn($formInterface);

        $supplierOrderFormHelper = new SupplierOrderFormHelper($formFactory);
        $form = $supplierOrderFormHelper->deleteSupplierOrderPosForm($supplierOrder);

        self::assertSame($formInterface, $form);
    }
}
