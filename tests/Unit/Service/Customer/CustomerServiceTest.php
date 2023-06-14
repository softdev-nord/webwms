<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Customer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Customer;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\Customer
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerServiceTest
 *
 * @covers \WebWMS\Service\Customer\CustomerService
 */
final class CustomerServiceTest extends TestCase
{
    private CustomerService $customerService;

    private MockObject $customerDataHandler;

    protected function setUp(): void
    {
        $this->customerDataHandler = $this->createMock(CustomerDataHandler::class);
        $this->customerService = new CustomerService($this->customerDataHandler);
    }

    public function testGetCustomerById(): void
    {
        $customerId = 1;
        $customer = new Customer();

        $this->customerDataHandler->expects(self::once())
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

        $this->customerDataHandler->expects(self::once())
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

        $this->customerDataHandler
            ->expects(self::once())
            ->method('getAllCustomers')
            ->willReturn($customers);

        $result = $this->customerService->getAllCustomers();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetAllCustomersAjax(): void
    {
        $jsonResponse = $this->createMock(JsonResponse::class);
        $customerNrInput = '123';

        $this->customerDataHandler->expects(self::once())
            ->method('getCustomers')
            ->willReturn($jsonResponse);

        $result = $this->customerService->getAllCustomersAjax($customerNrInput);

        self::assertSame($jsonResponse, $result);
    }

    public function testAddCustomer(): void
    {
        $article = new Customer();
        $this->customerDataHandler
            ->expects(self::once())
            ->method('addCustomer')
            ->with($article);

        $this->customerService->addCustomer($article);
    }

    public function testUpdateCustomer(): void
    {
        $customer = new Customer();
        $this->customerDataHandler
            ->expects(self::once())
            ->method('updateCustomer')
            ->with($customer);

        $this->customerService->updateCustomer($customer);
    }

    public function testDeleteCustomer(): void
    {
        $customer = new Customer();
        $this->customerDataHandler
            ->expects(self::once())
            ->method('deleteCustomer')
            ->with($customer);

        $this->customerService->deleteCustomer($customer);
    }

    public function testGetLastCustomer(): void
    {
        $lastCustomer = new Customer();

        $this->customerDataHandler->expects(self::once())
            ->method('getLastCustomer')
            ->willReturn($lastCustomer);

        $result = $this->customerService->getLastCustomer();

        self::assertSame($lastCustomer, $result);
    }
}
