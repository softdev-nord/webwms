<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class Measurement
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $deviceId,
        public string $targetType,
        public string $targetId,
        public ?int $weightGrams,
        public ?int $lengthMillimeters,
        public ?int $widthMillimeters,
        public ?int $heightMillimeters,
        public string $requestId,
        public string $status,
        public ?string $message,
        public string $measuredBy,
        public DateTimeImmutable $measuredAt,
    ) {
        foreach ([$id, $tenantId, $deviceId, $targetId, $requestId, $measuredBy] as $identifier) {
            if (trim($identifier) === '') {
                throw new \InvalidArgumentException('A measurement requires complete identifiers.');
            }
        }
        if (!in_array($targetType, ['package', 'product'], true)) {
            throw new \InvalidArgumentException('The measurement target type is unsupported.');
        }
        if (!in_array($status, ['accepted', 'rejected'], true)) {
            throw new \InvalidArgumentException('The measurement status is unsupported.');
        }
        if ($weightGrams === null && $lengthMillimeters === null) {
            throw new \InvalidArgumentException('A measurement requires weight or dimensions.');
        }
        foreach ([$weightGrams, $lengthMillimeters, $widthMillimeters, $heightMillimeters] as $value) {
            if ($value !== null && $value <= 0) {
                throw new \InvalidArgumentException('Measurement values must be positive.');
            }
        }
        $dimensions = [$lengthMillimeters, $widthMillimeters, $heightMillimeters];
        if (count(array_filter($dimensions, static fn (?int $value): bool => $value !== null)) !== 0
            && count(array_filter($dimensions, static fn (?int $value): bool => $value !== null)) !== 3) {
            throw new \InvalidArgumentException('Length, width and height must be supplied together.');
        }
        if ($message !== null && mb_strlen($message) > 500) {
            throw new \InvalidArgumentException('A measurement message must not exceed 500 characters.');
        }
    }
}
