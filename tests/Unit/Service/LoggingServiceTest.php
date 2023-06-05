<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Service\DataHandlers\Logging\LoggingDataHandler;
use WebWMS\Service\LoggingService;

/**
 * @package:    WebWMS\Tests\Unit\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LoggingServiceTest
 *
 * @covers \WebWMS\Service\LoggingService
 */
final class LoggingServiceTest extends TestCase
{
    private LoggingService $loggingService;

    /**
     * @var (LoggingDataHandler&MockObject)|MockObject
     */
    private MockObject|LoggingDataHandler $loggingDataHandler;

    protected function setUp(): void
    {
        $this->loggingDataHandler = $this->createMock(LoggingDataHandler::class);
        $this->loggingService = new LoggingService($this->loggingDataHandler);
    }

    public function testWrite(): void
    {
        $request = $this->createMock(Request::class);
        $message = 'Log message';
        $username = 'JohnDoe';

        $this->loggingDataHandler
            ->expects(self::once())
            ->method('write')
            ->with($request, $message, $username);

        $this->loggingService->write($request, $message, $username);
    }

    /**
     * @throws \Exception
     */
    public function testGetAllLogs(): void
    {
        $jsonResponse = $this->createMock(JsonResponse::class);

        $this->loggingDataHandler
            ->expects(self::once())
            ->method('getAllLogs')
            ->willReturn($jsonResponse);

        $result = $this->loggingService->getAllLogs();

        self::assertSame($jsonResponse, $result);
    }
}
