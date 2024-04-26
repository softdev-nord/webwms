<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Customer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerEntity;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\CustomerEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        CustomerServiceTest
 */
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
        $customerEntity = new CustomerEntity();

        $this->mockObject->expects(self::once())
            ->method('getCustomerById')
            ->with($customerId)
            ->willReturn($customerEntity);

        $result = $this->customerService->getCustomerById($customerId);

        self::assertSame($customerEntity, $result);
    }

    public function testGetCustomerByNr(): void
    {
        $customerNr = 123;
        $customerEntity = new CustomerEntity();

        $this->mockObject->expects(self::once())
            ->method('getCustomerByNr')
            ->with($customerNr)
            ->willReturn($customerEntity);

        $result = $this->customerService->getCustomerByNr($customerNr);

        self::assertSame($customerEntity, $result);
    }

    public function testGetAllCustomers(): void
    {
        $customers = [
            new CustomerEntity(),
            new CustomerEntity(),
        ];

        $this->mockObject
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

        $this->mockObject->expects(self::once())
            ->method('getCustomers')
            ->willReturn($jsonResponse);

        $result = $this->customerService->getAllCustomersAjax($customerNrInput);

        self::assertSame($jsonResponse, $result);
    }

    public function testAddCustomer(): void
    {
        $customerEntity = new CustomerEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addCustomer')
            ->with($customerEntity);

        $this->customerService->addCustomer($customerEntity);
    }

    public function testUpdateCustomer(): void
    {
        $customerEntity = new CustomerEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateCustomer')
            ->with($customerEntity);

        $this->customerService->updateCustomer($customerEntity);
    }

    public function testDeleteCustomer(): void
    {
        $customerEntity = new CustomerEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteCustomer')
            ->with($customerEntity);

        $this->customerService->deleteCustomer($customerEntity);
    }

    public function testGetLastCustomer(): void
    {
        $customerEntity = new CustomerEntity();

        $this->mockObject->expects(self::once())
            ->method('getLastCustomer')
            ->willReturn($customerEntity);

        $result = $this->customerService->getLastCustomer();

        self::assertSame($customerEntity, $result);
    }
}
