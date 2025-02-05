<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Logging\LoggingDataHandler;
use WebWMS\Service\LoggingService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'LoggingServiceTest'
)]
#[CoversClass(LoggingService::class)]
final class LoggingServiceTest extends TestCase
{
    private LoggingService $loggingService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(LoggingDataHandler::class);
        $this->loggingService = new LoggingService($this->mockObject);
    }

    public function testWrite(): void
    {
        $request = $this->createMock(Request::class);
        $message = 'Log message';
        $username = 'JohnDoe';

        $this->mockObject
            ->expects($this->once())
            ->method('write')
            ->with($request, $message, $username);

        $this->loggingService->write($request, $message, $username);
    }

    /**
     * @throws Exception
     */
    public function testGetAllLogs(): void
    {
        $jsonResponse = $this->createMock(JsonResponse::class);

        $this->mockObject
            ->expects($this->once())
            ->method('getAllLogs')
            ->willReturn($jsonResponse);

        $result = $this->loggingService->getAllLogs();

        self::assertSame($jsonResponse, $result);
    }
}
