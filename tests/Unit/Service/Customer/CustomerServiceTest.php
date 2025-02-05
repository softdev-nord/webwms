<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Customer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Customer;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Customer',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'CustomerServiceTest'
)]
#[CoversClass(CustomerService::class)]
final class CustomerServiceTest extends TestCase
{
    private CustomerService $customerService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(CustomerDataHandler::class);
        $this->customerService = new CustomerService($this->mockObject);
    }

    public function testGetCustomerById(): void
    {
        $customerId = 1;
        $customer = new Customer();

        $this->mockObject->expects($this->once())
            ->method('getCustomerById')
            ->with($customerId)
            ->willReturn($customer);

        $result = $this->customerService->getCustomerById($customerId);

        self::assertSame($customer, $result);
    }

    public function testGetCustomerByNr(): void
    {
        $customerNr = 123;
        $customer = new Customer();

        $this->mockObject->expects($this->once())
            ->method('getCustomerByNr')
            ->with($customerNr)
            ->willReturn($customer);

        $result = $this->customerService->getCustomerByNr($customerNr);

        self::assertSame($customer, $result);
    }

    public function testGetAllCustomers(): void
    {
        $customers = [
            new Customer(),
            new Customer(),
        ];

        $this->mockObject
            ->expects($this->once())
            ->method('getAllCustomers')
            ->willReturn($customers);

        $jsonResponse = $this->customerService->getAllCustomers();

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testGetAllCustomersAjax(): void
    {
        $jsonResponse = $this->createMock(JsonResponse::class);
        $customerNrInput = '123';

        $this->mockObject->expects($this->once())
            ->method('getCustomers')
            ->willReturn($jsonResponse);

        $result = $this->customerService->getAllCustomersAjax($customerNrInput);

        self::assertSame($jsonResponse, $result);
    }

    public function testAddCustomer(): void
    {
        $customer = new Customer();
        $this->mockObject
            ->expects($this->once())
            ->method('addCustomer')
            ->with($customer);

        $this->customerService->addCustomer($customer);
    }

    public function testUpdateCustomer(): void
    {
        $customer = new Customer();
        $this->mockObject
            ->expects($this->once())
            ->method('updateCustomer')
            ->with($customer);

        $this->customerService->updateCustomer($customer);
    }

    public function testDeleteCustomer(): void
    {
        $customer = new Customer();
        $this->mockObject
            ->expects($this->once())
            ->method('deleteCustomer')
            ->with($customer);

        $this->customerService->deleteCustomer($customer);
    }

    public function testGetLastCustomer(): void
    {
        $lastCustomer = new Customer();

        $this->mockObject->expects($this->once())
            ->method('getLastCustomer')
            ->willReturn($lastCustomer);

        $customer = $this->customerService->getLastCustomer();

        self::assertSame($lastCustomer, $customer);
    }
}
