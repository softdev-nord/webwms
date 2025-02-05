<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Form\CustomerOrder\AddCustomerOrderType;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;
use WebWMS\Form\CustomerOrder\DeleteCustomerOrderType;
use WebWMS\Form\CustomerOrder\EditCustomerOrderType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\CustomerOrderFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'CustomerOrderFormHelperTest'
)]
#[CoversClass(CustomerOrderFormHelper::class)]
final class CustomerOrderFormHelperTest extends TestCase
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

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $form = $customerOrderFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddCustomerOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddCustomerOrderType::class)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $form = $customerOrderFormHelper->addCustomerOrderForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditCustomerOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customerOrder = $this->createMock(CustomerOrder::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditCustomerOrderType::class, $customerOrder)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $form = $customerOrderFormHelper->editCustomerOrderForm($customerOrder);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteCustomerOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customerOrder = $this->createMock(CustomerOrder::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteCustomerOrderType::class, $customerOrder)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $form = $customerOrderFormHelper->deleteCustomerOrderForm($customerOrder);

        self::assertSame($formInterface, $form);
    }

    public function testAddCustomerOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(CustomerOrderPosType::class)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $form = $customerOrderFormHelper->addCustomerOrderPosForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditCustomerOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customerOrder = $this->createMock(CustomerOrderPos::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(CustomerOrderPosType::class, $customerOrder)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $form = $customerOrderFormHelper->editCustomerOrderPosForm($customerOrder);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteCustomerOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customerOrder = $this->createMock(CustomerOrderPos::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(CustomerOrderPosType::class, $customerOrder)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $form = $customerOrderFormHelper->deleteCustomerOrderPosForm($customerOrder);

        self::assertSame($formInterface, $form);
    }
}
