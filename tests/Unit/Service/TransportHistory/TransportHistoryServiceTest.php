<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\TransportHistory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportHistoryEntity;
use WebWMS\Service\DataHandlers\TransportHistory\TransportHistoryDataHandler;
use WebWMS\Service\TransportHistory\TransportHistoryService;

/**
 * @package:    WebWMS\Tests\Unit\Service\TransportHistoryEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        TransportHistoryServiceTest
 */
#[CoversClass(TransportHistoryService::class)]
final class TransportHistoryServiceTest extends TestCase
{
    private TransportHistoryService $transportHistoryService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(TransportHistoryDataHandler::class);
        $this->transportHistoryService = new TransportHistoryService($this->mockObject);
    }

    public function testGetTransportHistoryById(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('getTransportHistoryById')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Transport History']);

        $result = $this->transportHistoryService->getTransportHistoryById(1);

        self::assertEquals(['id' => 1, 'name' => 'Transport History'], $result);
    }

    public function testGetAllTransportHistories(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('getAllTransportHistories')
            ->willReturn(
                [
                    ['id' => 1, 'name' => 'Transport History 1'],
                    ['id' => 2, 'name' => 'Transport History 2'],
                ]
            );

        $result = $this->transportHistoryService->getAllTransportHistories();

        self::assertEquals([['id' => 1, 'name' => 'Transport History 1'], ['id' => 2, 'name' => 'Transport History 2']], $result);
    }

    public function testCreateTransportHistory(): void
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
            ->method('createTransportHistory')
            ->with($request, $user, $clientIp);

        $this->transportHistoryService->createTransportHistory($request, $user, $clientIp);
    }

    public function testGetLastStockUnit(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('getLastStockUnit')
            ->willReturn([]);

        $result = $this->transportHistoryService->getLastStockUnit();

        self::assertIsArray($result);
    }

    public function testAddTransportHistory(): void
    {
        $transportHistoryEntity = new TransportHistoryEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addTransportHistory')
            ->with($transportHistoryEntity);

        $this->transportHistoryService->addTransportHistory($transportHistoryEntity);
    }

    public function testUpdateTransportHistory(): void
    {
        $transportHistoryEntity = new TransportHistoryEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateTransportHistory')
            ->with($transportHistoryEntity);

        $this->transportHistoryService->updateTransportHistory($transportHistoryEntity);
    }

    public function testDeleteTransportHistory(): void
    {
        $transportHistoryEntity = new TransportHistoryEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteTransportHistory')
            ->with($transportHistoryEntity);

        $this->transportHistoryService->deleteTransportHistory($transportHistoryEntity);
    }

    public function testGetLastTransportHistoryNr(): void
    {
        $lastTransportHistoryId = 12345;
        $this->mockObject
            ->expects(self::once())
            ->method('getLastTransportHistoryNr')
            ->willReturn($lastTransportHistoryId);

        $result = $this->transportHistoryService->getLastTransportHistoryNr();

        self::assertEquals($lastTransportHistoryId, $result);
    }
}
