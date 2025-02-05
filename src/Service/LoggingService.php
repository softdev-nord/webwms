<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Logging\LoggingDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'LoggingService'
)]
readonly class LoggingService
{
    public function __construct(
        private LoggingDataHandler $loggingDataHandler,
    ) {
    }

    public function write(Request $request, string $message, string $username): void
    {
        $this->loggingDataHandler->write($request, $message, $username);
    }

    /**
     * @throws Exception
     */
    public function getAllLogs(): JsonResponse
    {
        return $this->loggingDataHandler->getAllLogs();
    }
}
