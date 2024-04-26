<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\CustomerOrderEntity;
use WebWMS\Entity\CustomerOrderPosEntity;
use WebWMS\Form\CustomerOrder\AddCustomerOrderType;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;
use WebWMS\Form\CustomerOrder\DeleteCustomerOrderType;
use WebWMS\Form\CustomerOrder\EditCustomerOrderType;
use WebWMS\Helper\FormHelper\CustomerOrderFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        CustomerOrderFormHelperTest
 */
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
            ->expects(self::once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $result = $customerOrderFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $result);
    }

    public function testAddCustomerOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(AddCustomerOrderType::class)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $result = $customerOrderFormHelper->addCustomerOrderForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditCustomerOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customerOrder = $this->createMock(CustomerOrderEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditCustomerOrderType::class, $customerOrder)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $result = $customerOrderFormHelper->editCustomerOrderForm($customerOrder);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteCustomerOrderForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customerOrder = $this->createMock(CustomerOrderEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteCustomerOrderType::class, $customerOrder)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $result = $customerOrderFormHelper->deleteCustomerOrderForm($customerOrder);

        self::assertSame($formInterface, $result);
    }

    public function testAddCustomerOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(CustomerOrderPosType::class)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $result = $customerOrderFormHelper->addCustomerOrderPosForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditCustomerOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customerOrder = $this->createMock(CustomerOrderPosEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(CustomerOrderPosType::class, $customerOrder)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $result = $customerOrderFormHelper->editCustomerOrderPosForm($customerOrder);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteCustomerOrderPosForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customerOrder = $this->createMock(CustomerOrderPosEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(CustomerOrderPosType::class, $customerOrder)
            ->willReturn($formInterface);

        $customerOrderFormHelper = new CustomerOrderFormHelper($formFactory);
        $result = $customerOrderFormHelper->deleteCustomerOrderPosForm($customerOrder);

        self::assertSame($formInterface, $result);
    }
}
