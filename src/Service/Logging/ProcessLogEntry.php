<?php

declare(strict_types=1);

namespace WebWMS\Service\Logging;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Logging',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'ProcessLogEntry'
)]
readonly class ProcessLogEntry
{
    public function __construct(
        /** Eindeutige Log-ID für Request-Tracking */
        private string $logId,

        /** Prozess-Code (SI101, SO102, etc.) */
        private string $processCode,

        /** Aktion im Prozess (start, complete, cancel, etc.) */
        private string $action,

        /** Benutzer der Aktion */
        private string $username,

        /** Client-IP */
        private string $clientIp,

        /** Ergebnis (success, error, warning) */
        private string $result,

        /** Ausführliche Meldung */
        private string $message,

        /** Fehler-Details falls vorhanden */
        private ?array $errorDetails = null,

        /** Performance-Metriken */
        private ?array $metrics = null,

        /** Kontext-Daten (Entitäts-IDs, etc.) */
        private ?array $context = null,
    ) {
    }

    public function getLogId(): string { return $this->logId; }
    public function getProcessCode(): string { return $this->processCode; }
    public function getAction(): string { return $this->action; }
    public function getUsername(): string { return $this->username; }
    public function getClientIp(): string { return $this->clientIp; }
    public function getResult(): string { return $this->result; }
    public function getMessage(): string { return $this->message; }
    public function getErrorDetails(): ?array { return $this->errorDetails; }
    public function getMetrics(): ?array { return $this->metrics; }
    public function getContext(): ?array { return $this->context; }

    /**
     * Serialisiere für strukturiertes Logging
     */
    public function toArray(): array
    {
        return [
            'log_id' => $this->logId,
            'process_code' => $this->processCode,
            'action' => $this->action,
            'username' => $this->username,
            'client_ip' => $this->clientIp,
            'result' => $this->result,
            'message' => $this->message,
            'error_details' => $this->errorDetails,
            'metrics' => $this->metrics,
            'context' => $this->context,
        ];
    }
}

