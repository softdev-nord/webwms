<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\Supplier;
use WebWMS\Form\Supplier\AddSupplierType;
use WebWMS\Form\Supplier\DeleteSupplierType;
use WebWMS\Form\Supplier\EditSupplierType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\SupplierFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'SupplierFormHelperTest'
)]
#[CoversClass(SupplierFormHelper::class)]
final class SupplierFormHelperTest extends TestCase
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

        $supplierFormHelper = new SupplierFormHelper($formFactory);
        $form = $supplierFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddSupplierForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddSupplierType::class)
            ->willReturn($formInterface);

        $supplierFormHelper = new SupplierFormHelper($formFactory);
        $form = $supplierFormHelper->addSupplierForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditSupplierForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplier = $this->createMock(Supplier::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditSupplierType::class, $supplier)
            ->willReturn($formInterface);

        $supplierFormHelper = new SupplierFormHelper($formFactory);
        $form = $supplierFormHelper->editSupplierForm($supplier);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteSupplierForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplier = $this->createMock(Supplier::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteSupplierType::class, $supplier)
            ->willReturn($formInterface);

        $supplierFormHelper = new SupplierFormHelper($formFactory);
        $form = $supplierFormHelper->deleteSupplierForm($supplier);

        self::assertSame($formInterface, $form);
    }
}
