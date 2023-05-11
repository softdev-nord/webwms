<?php

declare(strict_types=1);

namespace WebWMS\Tests\Functional\Service\DataHandlers\Customer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Customer;
use WebWMS\Repository\CustomerRepository;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerDataHandlerTest.
 *
 * @covers \WebWMS\Service\DataHandlers\Customer\CustomerDataHandler
 */
final class CustomerDataHandlerTest extends KernelTestCase
{
    /**
     * @var (EntityManagerInterface&MockObject)|MockObject
     */
    private MockObject|EntityManagerInterface $entityManagerMock;

    /**
     * @var (MockObject&DateTimeService)|MockObject
     */
    private MockObject|DateTimeService $dateTimeServiceMock;

    private CustomerDataHandler $customerDataHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dateTimeServiceMock = $this->getMockBuilder(DateTimeService::class)
            ->getMock();

        $this->customerDataHandler = new CustomerDataHandler(
            $this->entityManagerMock,
            $this->dateTimeServiceMock,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->entityManagerMock);
        unset($this->dateTimeServiceMock);
        unset($this->customerDataHandler);
    }

    public function testSave(): void
    {
        $customer = new Customer();

        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('persist')
            ->with($customer);
        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('flush');

        $this->customerDataHandler->save($customer);
    }

    public function testDelete(): void
    {
        $customer = new Customer();

        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('remove')
            ->with($customer);
        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('flush');

        $this->customerDataHandler->delete($customer);
    }

    public function testGetCustomerById(): void
    {
        $customer = new Customer();
        $customerId = 1;
        $customer->setCustomerId($customerId);

        $repositoryMock = $this->createMock(CustomerRepository::class);
        $repositoryMock->expects(self::once())
            ->method('find')
            ->with($customerId)
            ->willReturn($customer);

        $this->entityManagerMock->expects(self::once())
            ->method('getRepository')
            ->with(Customer::class)
            ->willReturn($repositoryMock);

        $result = $this->customerDataHandler->getCustomerById($customerId);

        self::assertSame($customer, $result);
    }

    public function testGetCustomerByNr(): void
    {
        $customer = new Customer();
        $customerNr = 60000;
        $customer->setCustomerNr($customerNr);

        $repositoryMock = $this->createMock(CustomerRepository::class);
        $repositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['customerNr' => $customerNr])
            ->willReturn($customer);

        $this->entityManagerMock->expects(self::once())
            ->method('getRepository')
            ->with(Customer::class)
            ->willReturn($repositoryMock);

        $result = $this->customerDataHandler->getCustomerByNr($customerNr);

        self::assertSame($customer, $result);
    }

    public function testGetCustomersReturnsJsonResponse(): void
    {
        $_GET['name_customer'] = 'John';
        $_GET['numOfBoxCustomer'] = 'customer_name';

        $stmt = $this->createMock(Result::class);
        $stmt->method('fetchAssociative')
            ->willReturn([
                'customer_nr' => '1',
                'customer_name' => 'John Doe',
                'customer_address_addition' => '',
                'customer_address_street' => '123 Main St',
                'customer_address_street_nr' => '1A',
                'customer_country_code' => 'US',
                'customer_zip_code' => '12345',
                'customer_city' => 'Anytown',
                'customer_id' => 'abc123',
            ]);
        $connection = $this->createMock(Connection::class);
        $connection->method('executeQuery')
            ->with(self::stringContains('SELECT customer_nr'))
            ->willReturn($stmt);
        $this->entityManagerMock->method('getConnection')
            ->willReturn($connection);

        $response = $this->customerDataHandler->getCustomers();
        self::assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode((string) $response->getContent(), true);
        self::assertIsArray($data);
    }

    public function testGetAllCustomers(): void
    {
        $customers = [
            [
                'id' => 1,
                'name' => 'John Doe',
            ],
            [
                'id' => 2,
                'name' => 'Jane Doe',
            ],
        ];
        $queryMock = $this->createMock(AbstractQuery::class);
        $queryMock->method('getArrayResult')->willReturn($customers);
        $queryBuilderMock = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $queryBuilderMock->method('select')->willReturnSelf();
        $queryBuilderMock->method('from')->willReturnSelf();
        $queryBuilderMock->method('getQuery')->willReturn($queryMock);
        $this->entityManagerMock->method('createQueryBuilder')->willReturn($queryBuilderMock);

        $result = $this->customerDataHandler->getAllCustomers();

        self::assertIsArray($result);
        self::assertCount(2, $result);
        self::assertEquals('John Doe', $result[0]['name']);
        self::assertEquals('Jane Doe', $result[1]['name']);
    }

    public function testAddCustomer(): void
    {
        $createdAt = new \DateTime();
        $this->dateTimeServiceMock
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($createdAt);

        $customerMock = $this->getMockBuilder(Customer::class)
            ->getMock();
        $customerMock
            ->expects(self::once())
            ->method('setCreatedAt')
            ->with($createdAt);

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($customerMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->customerDataHandler->addCustomer($customerMock);
    }

    public function testUpdateCustomer(): void
    {
        $updatedAt = new \DateTime();
        $this->dateTimeServiceMock
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($updatedAt);

        $customerMock = $this->getMockBuilder(Customer::class)
            ->getMock();
        $customerMock
            ->expects(self::once())
            ->method('setUpdatedAt')
            ->with($updatedAt);

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($customerMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->customerDataHandler->updateCustomer($customerMock);
    }

    public function testDeleteCustomer(): void
    {
        $customerMock = $this->getMockBuilder(Customer::class)
            ->getMock();

        $this->entityManagerMock
            ->expects(self::once())
            ->method('remove')
            ->with($customerMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->customerDataHandler->deleteCustomer($customerMock);
    }
}
