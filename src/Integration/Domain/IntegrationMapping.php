<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

readonly class IntegrationMapping
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $systemType,
        public string $messageType,
        public string $sourceField,
        public string $targetField,
        public string $transformation,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
    ) {
        if (!in_array($systemType, ['erp', 'sap_idoc', 'commerce'], true)) {
            throw new InvalidArgumentException('The mapping system type is not supported.');
        }

        if (!in_array($transformation, ['copy', 'trim', 'uppercase', 'lowercase', 'integer', 'decimal'], true)) {
            throw new InvalidArgumentException('The mapping transformation is not supported.');
        }

        foreach ([$id, $tenantId, $messageType, $sourceField, $targetField, $createdBy] as $value) {
            if (trim($value) === '') {
                throw new InvalidArgumentException('An integration mapping requires complete fields.');
            }
        }
    }
}
