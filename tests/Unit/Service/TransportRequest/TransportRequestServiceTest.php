<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\TransportRequest;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportRequest;
use WebWMS\Service\DataHandlers\TransportRequest\TransportRequestDataHandler;
use WebWMS\Service\TransportRequest\TransportRequestService;

/**
 * @package:    WebWMS\Tests\Unit\Service\TransportRequest
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequestServiceTest
 *
 * @covers \WebWMS\Service\TransportRequest\TransportRequestService
 */
final class TransportRequestServiceTest extends TestCase
{
    private TransportRequestService $transportRequestService;

    private MockObject $transportRequestDataHandler;

    protected function setUp(): void
    {
        $this->transportRequestDataHandler = $this->createMock(TransportRequestDataHandler::class);
        $this->transportRequestService = new TransportRequestService($this->transportRequestDataHandler);
    }

    public function testGetTransportRequestById(): void
    {
        $this->transportRequestDataHandler
            ->expects(self::once())
            ->method('getTransportRequestById')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Transport Request']);

        $result = $this->transportRequestService->getTransportRequestById(1);

        self::assertEquals(['id' => 1, 'name' => 'Transport Request'], $result);
    }

    public function testGetAllOpenTransportRequests(): void
    {
        $this->transportRequestDataHandler
            ->expects(self::once())
            ->method('getAllOpenTransportRequests')
            ->willReturn(
                [
                    ['id' => 1, 'name' => 'Transport Request 1'],
                    ['id' => 2, 'name' => 'Transport Request 2'],
                ]
            );

        $result = $this->transportRequestService->getAllOpenTransportRequests();

        self::assertEquals([['id' => 1, 'name' => 'Transport Request 1'], ['id' => 2, 'name' => 'Transport Request 2']], $result);
    }

    public function testCreateTransportRequest(): void
    {
        $request = new Request([], [
            'stock_in_final' => [
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
            ],
        ]);
        $user = 'test_user';
        $clientIp = '127.0.0.1';

        $this->transportRequestDataHandler
            ->expects(self::once())
            ->method('createTransportRequest')
            ->with($request, $user, $clientIp);

        $this->transportRequestService->createTransportRequest($request, $user, $clientIp);
    }

    public function testGetLastStockUnit(): void
    {
        $this->transportRequestDataHandler
            ->expects(self::once())
            ->method('getLastStockUnit')
            ->willReturn(20);

        $result = $this->transportRequestService->getLastStockUnit();

        self::assertEquals(20, $result);
    }

    public function testAddTransportRequest(): void
    {
        $transportRequest = new TransportRequest();
        $this->transportRequestDataHandler
            ->expects(self::once())
            ->method('addTransportRequest')
            ->with($transportRequest);

        $this->transportRequestService->addTransportRequest($transportRequest);
    }

    public function testUpdateTransportRequest(): void
    {
        $transportRequest = new TransportRequest();
        $this->transportRequestDataHandler
            ->expects(self::once())
            ->method('updateTransportRequest')
            ->with($transportRequest);

        $this->transportRequestService->updateTransportRequest($transportRequest);
    }

    public function testDeleteTransportRequest(): void
    {
        $transportRequest = new TransportRequest();
        $this->transportRequestDataHandler
            ->expects(self::once())
            ->method('deleteTransportRequest')
            ->with($transportRequest);

        $this->transportRequestService->deleteTransportRequest($transportRequest);
    }

    public function testGetLastTransportRequestNr(): void
    {
        $lastTransportRequestId = 12345;
        $this->transportRequestDataHandler
            ->expects(self::once())
            ->method('getLastTransportRequestNr')
            ->willReturn($lastTransportRequestId);

        $result = $this->transportRequestService->getLastTransportRequestNr();

        self::assertEquals($lastTransportRequestId, $result);
    }
}
