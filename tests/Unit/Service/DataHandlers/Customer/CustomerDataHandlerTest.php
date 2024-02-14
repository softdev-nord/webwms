<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\DataHandlers\Customer;

use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerEntity;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Tests\Unit\Service\DataHandlers\CustomerEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerDataHandlerTest
 */
#[CoversClass(CustomerDataHandler::class)]
final class CustomerDataHandlerTest extends TestCase
{
    private CustomerDataHandler $customerDataHandler;

    private MockObject $entityManager;

    private MockObject $dateTimeService;

    #[Override]
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->dateTimeService = $this->createMock(DateTimeService::class);

        $this->customerDataHandler = new CustomerDataHandler(
            $this->entityManager,
            $this->dateTimeService
        );
    }

    public function testSave(): void
    {
        $customer = $this->createMock(CustomerEntity::class);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($customer);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->customerDataHandler->save($customer);
    }

    public function testDelete(): void
    {
        $customer = $this->createMock(CustomerEntity::class);

        $this->entityManager
            ->expects(self::once())
            ->method('remove')
            ->with($customer);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->customerDataHandler->delete($customer);
    }

    public function testGetCustomerById(): void
    {
        $customerId = 123;
        $expectedCustomer = $this->createMock(CustomerEntity::class);
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('find')
            ->with($customerId)
            ->willReturn($expectedCustomer);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(CustomerEntity::class)
            ->willReturn($repository);

        $customer = $this->customerDataHandler->getCustomerById($customerId);

        self::assertSame($expectedCustomer, $customer);
    }

    public function testGetCustomerByNr(): void
    {
        $customerNr = 12345;
        $expectedCustomer = $this->createMock(CustomerEntity::class);
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['customerNr' => $customerNr])
            ->willReturn($expectedCustomer);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(CustomerEntity::class)
            ->willReturn($repository);

        $customer = $this->customerDataHandler->getCustomerByNr($customerNr);

        self::assertSame($expectedCustomer, $customer);
    }

    public function testGetAllCustomers(): void
    {
        $customer1 = new CustomerEntity();
        $customer1->setCustomerName('Aldi Zeven');

        $customer2 = new CustomerEntity();
        $customer2->setCustomerName('Aldi Buxtehude');

        $customers = [$customer1, $customer2];

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $queryBuilder
            ->expects(self::once())
            ->method('select')
            ->with('c')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('from')
            ->with(CustomerEntity::class, 'c')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->expects(self::once())
            ->method('getArrayResult')
            ->willReturn($customers);

        $this->entityManager
            ->expects(self::once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $result = $this->customerDataHandler->getAllCustomers();

        self::assertEquals($customers, $result);
    }

    public function testGetCustomer(): void
    {
        $customerNrInput = '12345';

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $queryBuilder
            ->expects(self::once())
            ->method('select')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('from')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('where')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->expects(self::once())
            ->method('getArrayResult')
            ->willReturn([
                [
                    'customerNr' => '60000',
                    'customerName' => 'ALDI Zeven',
                    'customerAddressAddition' => 'ALDI Zeven',
                    'customerAddressStreet' => 'Nord-West-Ring',
                    'customerAddressStreetNr' => '5',
                    'customerCountryCode' => 'DE',
                    'customerZipCode' => '27404',
                    'customerCity' => 'Zeven',
                    'customerId' => 1,
                ],
                [
                    'customerNr' => '60001',
                    'customerName' => 'ALDI Buxtehude',
                    'customerAddressAddition' => 'ALDI Buxtehude',
                    'customerAddressStreet' => 'Stader Straße',
                    'customerAddressStreetNr' => '10',
                    'customerCountryCode' => 'DE',
                    'customerZipCode' => '21614',
                    'customerCity' => 'Buxtehude',
                    'customerId' => 2,
                ],
            ]);

        $this->entityManager
            ->expects(self::once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $jsonResponse = $this->customerDataHandler->getCustomers($customerNrInput);

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testAddCustomer(): void
    {
        $customerEntity = new CustomerEntity();

        $dateTime = new DateTime('2023-06-06 12:00:00');
        $this->dateTimeService
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($dateTime);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($customerEntity);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->customerDataHandler->addCustomer($customerEntity);

        self::assertEquals($dateTime, $customerEntity->getCreatedAt());
    }

    public function testUpdateCustomer(): void
    {
        $customerEntity = new CustomerEntity();

        $dateTime = new DateTime('2023-06-06 12:00:00');
        $this->dateTimeService
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($dateTime);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($customerEntity);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->customerDataHandler->updateCustomer($customerEntity);

        self::assertEquals($dateTime, $customerEntity->getUpdatedAt());
    }

    public function testDeleteCustomer(): void
    {
        $customerEntity = new CustomerEntity();

        $this->entityManager
            ->expects(self::once())
            ->method('remove')
            ->with($customerEntity);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->customerDataHandler->deleteCustomer($customerEntity);
    }

    public function testGetLastCustomer(): void
    {
        $customerEntity = new CustomerEntity();
        $customerEntity->setCustomerId(123);

        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('findBy')
            ->with([], ['customerId' => 'DESC'], 1, 0)
            ->willReturn([$customerEntity]);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(CustomerEntity::class)
            ->willReturn($repository);

        $result = $this->customerDataHandler->getLastCustomer();

        self::assertSame($customerEntity, $result);
    }
}
