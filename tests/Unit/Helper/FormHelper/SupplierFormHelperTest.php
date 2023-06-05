<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\Supplier;
use WebWMS\Form\Supplier\AddSupplierType;
use WebWMS\Form\Supplier\DeleteSupplierType;
use WebWMS\Form\Supplier\EditSupplierType;
use WebWMS\Helper\FormHelper\SupplierFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierFormHelperTest
 *
 * @covers \WebWMS\Helper\FormHelper\SupplierFormHelper
 */
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
            ->expects(self::once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $helper = new SupplierFormHelper($formFactory);
        $result = $helper->createForm($type, $data, $options);

        self::assertSame($formInterface, $result);
    }

    public function testAddSupplierForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(AddSupplierType::class)
            ->willReturn($formInterface);

        $helper = new SupplierFormHelper($formFactory);
        $result = $helper->addSupplierForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditSupplierForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplier = $this->createMock(Supplier::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditSupplierType::class, $supplier)
            ->willReturn($formInterface);

        $helper = new SupplierFormHelper($formFactory);
        $result = $helper->editSupplierForm($supplier);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteSupplierForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $supplier = $this->createMock(Supplier::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteSupplierType::class, $supplier)
            ->willReturn($formInterface);

        $helper = new SupplierFormHelper($formFactory);
        $result = $helper->deleteSupplierForm($supplier);

        self::assertSame($formInterface, $result);
    }
}
