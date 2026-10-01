<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

readonly class IntegrationExchangeJob
{
    /** @param list<array<string, scalar|null>> $rows */
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $direction,
        public string $format,
        public string $resourceType,
        public ?string $sourceReference,
        public array $rows,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
    ) {
        if (!in_array($direction, ['import', 'export'], true)) {
            throw new InvalidArgumentException('An integration job direction must be import or export.');
        }

        if (!in_array($format, ['json', 'xml', 'csv', 'xlsx', 'idoc'], true)) {
            throw new InvalidArgumentException('The integration format is not supported.');
        }

        foreach ([$id, $tenantId, $resourceType, $createdBy] as $value) {
            if (trim($value) === '') {
                throw new InvalidArgumentException('An integration job requires complete identifiers and a resource type.');
            }
        }
    }
}
