<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\DataHandlers\TransportRequest;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportRequest;
use WebWMS\Repository\TransportRequestRepository;
use WebWMS\Service\DataHandlers\TransportRequest\TransportRequestDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Tests\Unit\Service\DataHandlers\TransportRequest
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequestDataHandlerTest
 *
 * @covers \WebWMS\Service\DataHandlers\TransportRequest\TransportRequestDataHandler
 */
final class TransportRequestDataHandlerTest extends TestCase
{
    private TransportRequestDataHandler $transportRequestDataHandler;

    /**
     * @var (EntityManagerInterface&MockObject)|MockObject
     */
    private MockObject|EntityManagerInterface $entityManager;

    /**
     * @var (DateTimeService&MockObject)|MockObject
     */
    private MockObject|DateTimeService $dateTimeService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->dateTimeService = $this->createMock(DateTimeService::class);

        $this->transportRequestDataHandler = new TransportRequestDataHandler(
            $this->entityManager,
            $this->dateTimeService
        );
    }

    public function testSave(): void
    {
        $transportRequest = $this->createMock(TransportRequest::class);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($transportRequest);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->transportRequestDataHandler->save($transportRequest);
    }

    public function testDelete(): void
    {
        $transportRequest = $this->createMock(TransportRequest::class);

        $this->entityManager
            ->expects(self::once())
            ->method('remove')
            ->with($transportRequest);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->transportRequestDataHandler->delete($transportRequest);
    }

    public function testGetTransportRequestById(): void
    {
        $transportRequestId = 1;
        $transportRequest = new TransportRequest();

        $repository = $this->createMock(TransportRequestRepository::class);
        $repository
            ->expects(self::once())
            ->method('findBy')
            ->with(['id' => $transportRequestId])
            ->willReturn([$transportRequest]);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(TransportRequest::class)
            ->willReturn($repository);

        $result = $this->transportRequestDataHandler->getTransportRequestById($transportRequestId);

        self::assertEquals([$transportRequest], $result);
    }

    public function testGetAllOpenTransportRequests(): void
    {
        $transportRequests = [new TransportRequest(), new TransportRequest()];

        $repository = $this->createMock(TransportRequestRepository::class);
        $repository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn($transportRequests);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(TransportRequest::class)
            ->willReturn($repository);

        $result = $this->transportRequestDataHandler->getAllOpenTransportRequests();

        self::assertEquals($transportRequests, $result);
    }

//    public function testCreateTransportRequest(): void
//    {
//        $requestData = [
//            'article_nr' => '12345',
//            'booking_method' => 'method',
//            'charge' => 'charge',
//            'loading_equipment' => 'equipment',
//            [
//                'stock_quantity' => 10,
//                'stock_coordinate' => 'A1',
//                'stock_ln' => 1,
//                'stock_fb' => 2,
//                'stock_sp' => 3,
//                'stock_tf' => 4,
//            ],
//        ];
//        $user = 'test_user';
//        $clientIp = '127.0.0.1';
//
//        $this->transportRequestDataHandler = $this->createMock(TransportRequestDataHandler::class);
//
//        $this->transportRequestDataHandler
//            ->expects(self::once())
//            ->method('createTransportRequest')
//            ->with($requestData, $user, $clientIp);
//
//        $this->transportRequestDataHandler->createTransportRequest($requestData, $user, $clientIp);
//    }

//    public function testCreateTransportRequest(): void
//    {
//        $requestData = [
//            'article_nr' => '123',
//            'booking_method' => 'method',
//            'charge' => 'charge',
//            'loading_equipment' => 'equipment',
//            [
//                'stock_quantity' => 10,
//                'stock_coordinate' => 'A1',
//                'stock_ln' => 1,
//                'stock_fb' => 2,
//                'stock_sp' => 3,
//                'stock_tf' => 4
//            ]
//        ];
//
//        $user = 'test_user';
//        $clientIp = '127.0.0.1';
//
//        $this->transportRequestDataHandler = $this->createMock(TransportRequestDataHandler::class);
//
//        $this->dateTimeService->expects($this->never())
//            ->method('createDateTime')
//            ->willReturn(new \DateTime());
//
//        // Set expectations for the EntityManager
//
//        $this->entityManager->expects($this->never())
//            ->method('persist')
//            ->with($this->isInstanceOf(TransportRequest::class));
//
//        $this->entityManager->expects($this->never())
//            ->method('flush');
//
//        $this->transportRequestDataHandler->createTransportRequest($requestData, $user, $clientIp);
//    }

//    public function testCreateTransportRequest(): void
//    {
//        $requestData = [
//            [
//                'stock_quantity' => 10,
//                'stock_coordinate' => 'A1',
//                'stock_ln' => 1,
//                'stock_fb' => 2,
//                'stock_sp' => 3,
//                'stock_tf' => 4,
//            ],
//            'article_nr' => '123',
//            'booking_method' => 'method',
//            'charge' => 'charge',
//            'loading_equipment' => 'equipment',
//
//        ];
//
//        $user = 'test_user';
//        $clientIp = '127.0.0.1';
//
//        $this->dateTimeService
//            ->expects(self::exactly(0))
//            ->method('createDateTime')
//            ->willReturn(new \DateTime());
//
//        $this->transportRequestDataHandler = $this->createMock(TransportRequestDataHandler::class);
//
//        $this->entityManager
//            ->expects(self::exactly(0))
//            ->method('persist')
//            ->with(self::callback(function ($arg) use ($requestData) {
//                // Validate the properties of the TransportRequest object
//                // based on the input request data
//                self::assertEquals('stock_unit_from_history', $arg->getSuId());
//                self::assertEquals('tr_nr_from_history', $arg->getTrNr());
//                self::assertEquals(1, $arg->getTrPos());
//                self::assertEquals($requestData['article_nr'], $arg->getArticleNr());
//                self::assertEquals(10.0, $arg->getTrQuantity());
//                self::assertEquals('A1', $arg->getStockCoordinate());
//                self::assertEquals(1, $arg->getStockNr());
//                self::assertEquals(2, $arg->getStockLevel1());
//                self::assertEquals(3, $arg->getStockLevel2());
//                self::assertEquals(4, $arg->getStockLevel3());
//                self::assertEquals(1, $arg->getStockLevel4());
//                self::assertEquals('2023-06-08 10:00:00', $arg->getTrAccess());
//                self::assertEquals(0, $arg->getTrState());
//                self::assertEquals('test_user', $arg->getOrderUsername());
//                self::assertEquals('booking_method', $arg->getBookingMethod());
//                self::assertNull($arg->getDocId());
//                self::assertEquals('charge', $arg->getCharge());
//                self::assertEquals('127.0.0.1', $arg->getTrComputerIp());
//                self::assertEquals('loading_equipment', $arg->getLoadingEquipment());
//                self::assertEquals(1, $arg->getTrType());
//
//                return true;
//            }));
//
//        $this->entityManager->expects(self::exactly(0))
//            ->method('flush');
//
//        $result = $this->transportRequestDataHandler->createTransportRequest($requestData, $user, $clientIp);
//        //dd($result);
//
//        self::assertEquals(false, $result);
//    }

    public function testCreateTransportRequest(): void
    {
        $request = $this->createMock(Request::class);
        $requestData = [
            'article_nr' => 'ABC123',
            'booking_method' => 'method',
            'charge' => 'charge',
            'loading_equipment' => 'equipment',
            [
                'stock_quantity' => 10,
                'stock_coordinate' => 'A1',
                'stock_ln' => 1,
                'stock_fb' => 2,
                'stock_sp' => 3,
                'stock_tf' => 4,
            ],
            [
                'stock_quantity' => 10,
                'stock_coordinate' => 'A1',
                'stock_ln' => 1,
                'stock_fb' => 2,
                'stock_sp' => 3,
                'stock_tf' => 4,
            ],
        ];

//        $parameterBag = $this->createMock(ParameterBag::class);
        $request
            ->expects(self::once())
            ->method('getContent')
//            ->willReturn($parameterBag);
            ->willReturn(['stock_in_final' => $requestData]);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with(self::isInstanceOf(TransportRequest::class));

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->dateTimeService
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn(new \DateTime());

        $this->transportRequestDataHandler->createTransportRequest($request, 'user', '127.0.0.1');
    }

//    public function testCreateTransportRequest(): void
//    {
//        // Prepare test data
//        $requestData = [
//            'article_nr' => '12345',
//            'booking_method' => 'method',
//            'charge' => 'charge',
//            'loading_equipment' => 'equipment',
//            [
//                'stock_quantity' => 10,
//                'stock_coordinate' => 'A1',
//                'stock_ln' => 1,
//                'stock_fb' => 2,
//                'stock_sp' => 3,
//                'stock_tf' => 4,
//            ],
//            // Add more test data as needed
//        ];
//        $user = 'test_user';
//        $clientIp = '127.0.0.1';
//
//        // Create mocks and expectations
//        $transportRequest = $this->createMock(TransportRequest::class);
//
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setSuId')
//            ->with(1234);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setTrNr')
//            ->with(1234);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setTrPos')
//            ->with(1);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setTrPrio')
//            ->with(0);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setArticleNr')
//            ->with('60004');
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setTrQuantity')
//            ->with(10.0);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setStockCoordinate')
//            ->with(1234);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setStockNr')
//            ->with(141);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setStockLevel1')
//            ->with(1);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setStockLevel2')
//            ->with(1);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setStockLevel3')
//            ->with(1);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setStockLevel4')
//            ->with(1);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setTrAccess')
//            ->with(new \DateTime());
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setTrState')
//            ->with(1);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setOrderUsername')
//            ->with($user);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setBookingMethod')
//            ->with('stock_in');
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setDocId')
//            ->with(1);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setCharge')
//            ->with('123456');
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setTrComputerIp')
//            ->with($clientIp);
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setLoadingEquipment')
//            ->with('PAL');
//        $transportRequest
//            ->expects(self::exactly(0))
//            ->method('setTrType')
//            ->with(1);
//
//        //dd($transportRequest);
//
//        // Create the instance of the class under test
//        $handler = $this->createMock(TransportRequestDataHandler::class);
//
//        $handler
//            ->expects(self::exactly(0))
//            ->method('save')
//            ->with($transportRequest);
//
//        // Call the method being tested
//        $handler->createTransportRequest($requestData, $user, $clientIp);
//    }

//    public function testCreateTransportRequest(): void
//    {
//        // Prepare test data
//        $requestData = [
//            'article_nr' => '12345',
//            'booking_method' => 'method',
//            'charge' => 'charge',
//            'loading_equipment' => 'equipment',
//            [
//                'stock_quantity' => 10,
//                'stock_coordinate' => 'A1',
//                'stock_ln' => 1,
//                'stock_fb' => 2,
//                'stock_sp' => 3,
//                'stock_tf' => 4,
//            ],
//            // Add more test data as needed
//        ];
//        $user = 'test_user';
//        $clientIp = '127.0.0.1';
//
//        // Create an instance of the data handler
//        $dataHandler = new TransportRequestDataHandler($this->entityManager, $this->dateTimeService);
//
//        // Set up expectations on the mock objects
//        // Example: expect the createDateTime method to be called once and return a fixed DateTime object
//        $this->dateTimeService->expects($this->once())
//            ->method('createDateTime')
//            ->willReturn(new \DateTime('2023-06-08 12:00:00'));
//
//        // Example: expect the save method to be called once with a TransportRequest object
//        $dataHandler->expects($this->once())
//            ->method('save')
//            ->with($this->isInstanceOf(TransportRequest::class));
//
//        // Call the method under test
//        $dataHandler->createTransportRequest($requestData, $user, $clientIp);
//
//        // Additional assertions can be added as needed
//    }

//    public function testCreateTransportRequest(): void
//    {
//        // Prepare test data
//        $requestData = [
//            'article_nr' => '12345',
//            'booking_method' => 'method',
//            'charge' => 'charge',
//            'loading_equipment' => 'equipment',
//            [
//                'stock_quantity' => 10,
//                'stock_coordinate' => 'A1',
//                'stock_ln' => 1,
//                'stock_fb' => 2,
//                'stock_sp' => 3,
//                'stock_tf' => 4,
//            ],
//        ];
//        $user = 'test_user';
//        $clientIp = '127.0.0.1';
//
//        $lastStockUnit = 12345; // Replace with your implementation or mock
//        $lastTrNr = 6789; // Replace with your implementation or mock
//
//        $this->transportRequestDataHandler = $this->createMock(TransportRequestDataHandler::class);
//
//        $this->transportRequestDataHandler
//            ->expects($this->exactly(0))
//            ->method('getLastStockUnit')
//            ->willReturn($lastStockUnit);
//
//        $this->transportRequestDataHandler
//            ->expects($this->exactly(0))
//            ->method('getLastTransportRequestNr')
//            ->willReturn($lastTrNr);
//
//        $this->dateTimeService
//            ->expects($this->exactly(0))
//            ->method('createDateTime')
//            ->willReturn(new \DateTime()); // Replace with your implementation or mock
//
//        $this->entityManager
//            ->expects($this->exactly(0))
//            ->method('persist')
//            ->with($this->isInstanceOf(TransportRequest::class));
//
//        $this->entityManager
//            ->expects($this->exactly(0))
//            ->method('flush');
//
//        // Call the method being tested
//        $this->transportRequestDataHandler->createTransportRequest($requestData, $user, $clientIp);
//    }

    public function testGetLastStockUnit(): void
    {
        $transportRequest = $this->createMock(TransportRequest::class);
        $transportRequest
            ->expects(self::once())
            ->method('getSuId')
            ->willReturn(100);

        $transportRequestRepository = $this->createMock(EntityRepository::class);
        $transportRequestRepository
            ->expects(self::once())
            ->method('findBy')
            ->willReturn([$transportRequest]);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->willReturn($transportRequestRepository);

        $lastStockUnit = $this->transportRequestDataHandler->getLastStockUnit();

        self::assertEquals(100, $lastStockUnit);
    }

    public function testGetLastTransportRequestNr(): void
    {
        $transportRequest = $this->createMock(TransportRequest::class);
        $transportRequest
            ->expects(self::once())
            ->method('getTrNr')
            ->willReturn(300);

        $transportRequestRepository = $this->createMock(EntityRepository::class);
        $transportRequestRepository
            ->expects(self::once())
            ->method('findBy')
            ->willReturn([$transportRequest]);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->willReturn($transportRequestRepository);

        $lastTrNr = $this->transportRequestDataHandler->getLastTransportRequestNr();

        self::assertEquals(300, $lastTrNr);
    }

    public function testAddArticle(): void
    {
        $transportRequest = new TransportRequest();

        $dateTime = new \DateTime('2023-06-06 12:00:00');
        $this->dateTimeService
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($dateTime);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($transportRequest);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->transportRequestDataHandler->addTransportRequest($transportRequest);

        self::assertEquals($dateTime, $transportRequest->getCreatedAt());
    }

    public function testUpdateArticle(): void
    {
        $transportRequest = new TransportRequest();

        $dateTime = new \DateTime('2023-06-06 12:00:00');
        $this->dateTimeService
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($dateTime);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($transportRequest);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->transportRequestDataHandler->updateTransportRequest($transportRequest);

        self::assertEquals($dateTime, $transportRequest->getUpdatedAt());
    }

    public function testDeleteArticle(): void
    {
        $transportRequest = new TransportRequest();

        $this->entityManager
            ->expects(self::once())
            ->method('remove')
            ->with($transportRequest);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->transportRequestDataHandler->deleteTransportRequest($transportRequest);
    }
}
