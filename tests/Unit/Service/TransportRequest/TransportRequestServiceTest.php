<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\TransportRequest;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportRequestEntity;
use WebWMS\Service\DataHandlers\TransportRequest\TransportRequestDataHandler;
use WebWMS\Service\TransportRequest\TransportRequestService;

/**
 * @package:    WebWMS\Tests\Unit\Service\TransportRequestEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        TransportRequestServiceTest
 */
#[CoversClass(TransportRequestService::class)]
final class TransportRequestServiceTest extends TestCase
{
    private TransportRequestService $transportRequestService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(TransportRequestDataHandler::class);
        $this->transportRequestService = new TransportRequestService($this->mockObject);
    }

    public function testGetTransportRequestByIdReturnsNull(): void
    {
        $transportRequestId = 1;
        $this->mockObject
            ->expects(self::once())
            ->method('getTransportRequestById')
            ->with($transportRequestId)
            ->willReturn(null);

        $result = $this->transportRequestService->getTransportRequestById(1);

        self::assertNull($result);
    }

    public function testGetTransportRequestById(): void
    {
        $transportRequestId = 1;
        $transportRequest = new TransportRequestEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('getTransportRequestById')
            ->with($transportRequestId)
            ->willReturn($transportRequest);

        $result = $this->transportRequestService->getTransportRequestById(1);

        self::assertSame($transportRequest, $result);
    }

    public function testGetAllOpenTransportRequests(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('getAllOpenTransportRequests')
            ->willReturn(
                new JsonResponse()
            );

        $result = $this->transportRequestService->getAllOpenTransportRequests();

        self::assertInstanceOf(JsonResponse::class, $result);
        // self::assertEquals([['id' => 1, 'name' => 'Transport Request 1'], ['id' => 2, 'name' => 'Transport Request 2']], $result);
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

        $this->mockObject
            ->expects(self::once())
            ->method('createTransportRequest')
            ->with($request, $user, $clientIp);

        $this->transportRequestService->createTransportRequest($request, $user, $clientIp);
    }

    public function testGetLastStockUnit(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('getLastStockUnit')
            ->willReturn([]);

        $result = $this->transportRequestService->getLastStockUnit();

        self::assertIsArray($result);
    }

    public function testAddTransportRequest(): void
    {
        $transportRequestEntity = new TransportRequestEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addTransportRequest')
            ->with($transportRequestEntity);

        $this->transportRequestService->addTransportRequest($transportRequestEntity);
    }

    public function testUpdateTransportRequest(): void
    {
        $transportRequestEntity = new TransportRequestEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateTransportRequest')
            ->with($transportRequestEntity);

        $this->transportRequestService->updateTransportRequest($transportRequestEntity);
    }

    public function testDeleteTransportRequest(): void
    {
        $transportRequestEntity = new TransportRequestEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteTransportRequest')
            ->with($transportRequestEntity);

        $this->transportRequestService->deleteTransportRequest($transportRequestEntity);
    }

    public function testGetLastTransportRequestNr(): void
    {
        $lastTransportRequestId = 12345;
        $this->mockObject
            ->expects(self::once())
            ->method('getLastTransportRequestNr')
            ->willReturn($lastTransportRequestId);

        $result = $this->transportRequestService->getLastTransportRequestNr();

        self::assertEquals($lastTransportRequestId, $result);
    }
}
