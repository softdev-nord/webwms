<?php

declare(strict_types=1);

namespace WebWMS\Service\Logging;

use Psr\Log\LoggerInterface;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Logging',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'ProcessEventLogger'
)]
readonly class ProcessEventLogger
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Loggt Prozess-Event mit standardisiertem Format
     */
    public function log(ProcessLogEntry $entry): void
    {
        $logContext = $entry->toArray();

        // Wähle Log-Level basierend auf Ergebnis
        $level = match ($entry->getResult()) {
            'success' => 'info',
            'warning' => 'warning',
            'error' => 'error',
            default => 'debug',
        };

        // Log mit strukturiertem Format für ELK/Splunk
        $this->logger->log($level, "{process_code} {action}: {message}", $logContext);
    }

    /**
     * Schnelle Logging-Helper für Erfolg
     */
    public function success(
        string $processCode,
        string $action,
        string $username,
        string $clientIp,
        string $message,
        ?array $context = null,
        ?array $metrics = null,
    ): void {
        $this->log(new ProcessLogEntry(
            logId: $this->generateLogId(),
            processCode: $processCode,
            action: $action,
            username: $username,
            clientIp: $clientIp,
            result: 'success',
            message: $message,
            context: $context,
            metrics: $metrics,
        ));
    }

    /**
     * Schnelle Logging-Helper für Fehler
     */
    public function error(
        string $processCode,
        string $action,
        string $username,
        string $clientIp,
        string $message,
        ?\Throwable $exception = null,
        ?array $context = null,
    ): void {
        $this->log(new ProcessLogEntry(
            logId: $this->generateLogId(),
            processCode: $processCode,
            action: $action,
            username: $username,
            clientIp: $clientIp,
            result: 'error',
            message: $message,
            errorDetails: $exception ? [
                'type' => $exception::class,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ] : null,
            context: $context,
        ));
    }

    /**
     * Generiert eindeutige Log-ID für Request-Tracking
     */
    private function generateLogId(): string
    {
        return uniqid('LOG-', true);
    }
}

