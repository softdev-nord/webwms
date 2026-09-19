<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final class PrintJob
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_PRINTING = 'printing';
    public const STATUS_PRINTED = 'printed';
    public const STATUS_FAILED = 'failed';

    public function __construct(
        public readonly string $id,
        public readonly string $tenantId,
        public readonly string $printerId,
        public readonly string $documentType,
        public readonly string $documentReference,
        public readonly string $format,
        public readonly int $copies,
        public readonly string $idempotencyKey,
        public string $status,
        public int $attempts,
        public ?string $externalReference,
        public ?string $lastError,
        public readonly string $createdBy,
        public readonly DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $completedAt = null
    ) {
        foreach ([$id, $tenantId, $printerId, $documentReference, $idempotencyKey, $createdBy] as $identifier) {
            if (trim($identifier) === '') {
                throw new \InvalidArgumentException('A print job requires complete identifiers.');
            }
        }
        if (mb_strlen($documentReference) > 500 || mb_strlen($idempotencyKey) > 100) {
            throw new \InvalidArgumentException('Print job references exceed the supported length.');
        }
        if (!in_array($documentType, ['carrier_label', 'document', 'loading_manifest'], true)) {
            throw new \InvalidArgumentException('The print document type is unsupported.');
        }
        if (!in_array($format, ['PDF', 'ZPL'], true)) {
            throw new \InvalidArgumentException('The print format must be PDF or ZPL.');
        }
        if ($copies < 1 || $copies > 99) {
            throw new \InvalidArgumentException('Print copies must be between 1 and 99.');
        }
        if ($attempts < 0) {
            throw new \InvalidArgumentException('Print attempts cannot be negative.');
        }
        if (!in_array($status, [self::STATUS_QUEUED, self::STATUS_PRINTING, self::STATUS_PRINTED, self::STATUS_FAILED], true)) {
            throw new \InvalidArgumentException('The print job status is invalid.');
        }
    }

    public function start(): void
    {
        if (!in_array($this->status, [self::STATUS_QUEUED, self::STATUS_FAILED], true)) {
            throw new \DomainException('Only queued or failed print jobs can be started.');
        }
        $this->status = self::STATUS_PRINTING;
        ++$this->attempts;
        $this->lastError = null;
    }

    public function succeed(string $externalReference, DateTimeImmutable $at): void
    {
        if ($this->status !== self::STATUS_PRINTING || trim($externalReference) === '') {
            throw new \DomainException('Only a printing job can be completed.');
        }
        $this->status = self::STATUS_PRINTED;
        $this->externalReference = $externalReference;
        $this->completedAt = $at;
    }

    public function fail(string $message): void
    {
        if ($this->status !== self::STATUS_PRINTING || trim($message) === '') {
            throw new \DomainException('Only a printing job can fail with an error.');
        }
        $this->status = self::STATUS_FAILED;
        $this->lastError = mb_substr($message, 0, 1000);
    }
}
