<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\Customer;
use WebWMS\Form\Customer\AddCustomerType;
use WebWMS\Form\Customer\DeleteCustomerType;
use WebWMS\Form\Customer\EditCustomerType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\CustomerFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'CustomerFormHelperTest'
)]
#[CoversClass(CustomerFormHelper::class)]
final class CustomerFormHelperTest extends TestCase
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

        $customerFormHelper = new CustomerFormHelper($formFactory);
        $form = $customerFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddCustomerForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddCustomerType::class)
            ->willReturn($formInterface);

        $customerFormHelper = new CustomerFormHelper($formFactory);
        $form = $customerFormHelper->addCustomerForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditCustomerForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customer = $this->createMock(Customer::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditCustomerType::class, $customer)
            ->willReturn($formInterface);

        $customerFormHelper = new CustomerFormHelper($formFactory);
        $form = $customerFormHelper->editCustomerForm($customer);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteCustomerForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $customer = $this->createMock(Customer::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteCustomerType::class, $customer)
            ->willReturn($formInterface);

        $customerFormHelper = new CustomerFormHelper($formFactory);
        $form = $customerFormHelper->deleteCustomerForm($customer);

        self::assertSame($formInterface, $form);
    }
}
