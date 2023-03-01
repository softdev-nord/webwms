<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Service\DataHandlers\Logging\LoggingDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        LoggingService
 */
class LoggingService
{
    public function __construct(
        private LoggingDataHandler $loggingDataHandler
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
