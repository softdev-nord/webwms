<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class StockDimensions
{
    private ?string $batchNumber;

    private ?string $serialNumber;

    private ?DateTimeImmutable $expiresAt;

    public function __construct(
        private StockStatus $status = StockStatus::Available,
        ?string $batchNumber = null,
        ?string $serialNumber = null,
        ?DateTimeImmutable $expiresAt = null,
    ) {
        $this->batchNumber = $this->normalizeIdentifier($batchNumber, 'batch number');
        $this->serialNumber = $this->normalizeIdentifier($serialNumber, 'serial number');
        $this->expiresAt = $expiresAt?->setTime(0, 0);
    }

    public static function fromInput(
        string $status,
        ?string $batchNumber = null,
        ?string $serialNumber = null,
        ?DateTimeImmutable $expiresAt = null,
    ): self {
        $stockStatus = StockStatus::tryFrom(mb_strtolower(trim($status)));

        if ($stockStatus === null) {
            throw new InvalidArgumentException('The stock status is not supported.');
        }

        return new self($stockStatus, $batchNumber, $serialNumber, $expiresAt);
    }

    public function status(): StockStatus
    {
        return $this->status;
    }

    public function batchNumber(): ?string
    {
        return $this->batchNumber;
    }

    public function serialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function expiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function key(): string
    {
        return hash('sha256', implode('|', [
            $this->status->value,
            $this->batchNumber ?? '',
            $this->serialNumber ?? '',
            $this->expiresAt?->format('Y-m-d') ?? '',
        ]));
    }

    private function normalizeIdentifier(?string $value, string $field): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $normalized = mb_strtoupper(trim($value));

        if (mb_strlen($normalized) > 100) {
            throw new InvalidArgumentException(sprintf('The %s must not exceed 100 characters.', $field));
        }

        return $normalized;
    }
}
