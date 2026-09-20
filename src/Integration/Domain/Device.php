<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use DateTimeImmutable;

final readonly class Device
{
    public function __construct(
        public string $id,
        public string $tenantId,
        public string $code,
        public string $name,
        public string $type,
        public bool $active,
        public string $createdBy,
        public DateTimeImmutable $createdAt,
    ) {
        foreach ([$id, $tenantId, $createdBy] as $identifier) {
            if (trim($identifier) === '') {
                throw new \InvalidArgumentException('A device requires complete identifiers.');
            }
        }
        if (preg_match('/^[A-Z0-9][A-Z0-9_-]{1,39}$/', $code) !== 1) {
            throw new \InvalidArgumentException('A device code must contain 2 to 40 uppercase characters.');
        }
        if (trim($name) === '' || mb_strlen($name) > 100) {
            throw new \InvalidArgumentException('A device name must contain 1 to 100 characters.');
        }
        if (!in_array($type, ['barcode_scanner', 'mde', 'mobile_browser'], true)) {
            throw new \InvalidArgumentException('The device type is unsupported.');
        }
    }
}
