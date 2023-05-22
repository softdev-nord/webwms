<?php

declare(strict_types=1);

namespace WebWMS\Tests\Functional\Service\Customer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Customer;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;

/**
 * @package:    WebWMS\Tests\Functional\Service\Customer
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerServiceTest
 *
 * @covers \WebWMS\Service\Customer\CustomerService
 */
final class CustomerServiceTest extends TestCase
{
    /**
     * @var (CustomerDataHandler&MockObject)|MockObject
     */
    private MockObject|CustomerDataHandler $customerDataHandler;
    private CustomerService $customerService;

    protected function setUp(): void
    {
        $this->customerDataHandler = $this->createMock(CustomerDataHandler::class);
        $this->customerService = new CustomerService($this->customerDataHandler);
    }

    public function testGetCustomerByIdReturnsNullWhenCustomerDoesNotExist(): void
    {
        $customerId = 1;
        $this->customerDataHandler
            ->expects(self::once())
            ->method('getCustomerById')
            ->with($customerId)
            ->willReturn(null);

        $result = $this->customerService->getCustomerById($customerId);

        self::assertNull($result);
    }

    public function testGetCustomerByIdReturnsCustomerWhenCustomerExists(): void
    {
        $customerId = 1;
        $customer = new Customer();
        $this->customerDataHandler
            ->expects(self::once())
            ->method('getCustomerById')
            ->with($customerId)
            ->willReturn($customer);

        $result = $this->customerService->getCustomerById($customerId);

        self::assertSame($customer, $result);
    }

    public function testGetCustomerByNrReturnsNullWhenCustomerDoesNotExist(): void
    {
        $customerNr = 60000;
        $this->customerDataHandler
            ->expects(self::once())
            ->method('getCustomerByNr')
            ->with($customerNr)
            ->willReturn(null);

        $result = $this->customerService->getCustomerByNr($customerNr);

        self::assertNull($result);
    }

    public function testGetCustomerByNrReturnsCustomerWhenCustomerExists(): void
    {
        $customerNr = 60000;
        $customer = new Customer();
        $this->customerDataHandler
            ->expects(self::once())
            ->method('getCustomerByNr')
            ->with($customerNr)
            ->willReturn($customer);

        $result = $this->customerService->getCustomerByNr($customerNr);

        self::assertSame($customer, $result);
    }

    public function testGetAllCustomersReturnsJsonResponse(): void
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

    public function testGetAllCustomersAjaxReturnsJsonResponse(): void
    {
        $customers = [
            new Customer(),
            new Customer(),
        ];
        $this->customerDataHandler
            ->expects(self::once())
            ->method('getCustomers')
            ->willReturn(new JsonResponse($customers));

        $result = $this->customerService->getAllCustomersAjax();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testAddCustomerCallsDataHandlerMethod(): void
    {
        $customer = new Customer();
        $this->customerDataHandler
            ->expects(self::once())
            ->method('addCustomer')
            ->with($customer);

        $this->customerService->addCustomer($customer);
    }

    public function testUpdateCustomerCallsDataHandlerMethod(): void
    {
        $customer = new Customer();
        $this->customerDataHandler
            ->expects(self::once())
            ->method('updateCustomer')
            ->with($customer);

        $this->customerService->updateCustomer($customer);
    }

    public function testDeleteCustomerCallsDataHandlerMethod(): void
    {
        $customer = new Customer();
        $this->customerDataHandler
            ->expects(self::once())
            ->method('deleteCustomer')
            ->with($customer);

        $this->customerService->deleteCustomer($customer);
    }

    public function testGetLastCustomerReturnsInt(): void
    {
        $lastCustomerId = 5;
        $this->customerDataHandler
            ->expects(self::once())
            ->method('getLastCustomer')
            ->willReturn($lastCustomerId);

        $result = $this->customerService->getLastCustomer();

        self::assertIsInt($result);
    }
}
