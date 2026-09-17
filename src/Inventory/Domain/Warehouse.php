<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Site\SiteId;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class Warehouse
{
    public function __construct(
        private InventoryId $id,
        private TenantId $tenantId,
        private SiteId $siteId,
        private string $code,
        private string $name,
        private DateTimeImmutable $createdAt,
    ) {
        if (preg_match('/^[A-Z0-9][A-Z0-9_-]{1,19}$/', $code) !== 1) {
            throw new InvalidArgumentException('A warehouse code must contain 2 to 20 uppercase characters.');
        }

        if (trim($name) === '' || mb_strlen($name) > 255) {
            throw new InvalidArgumentException('A warehouse name must contain 1 to 255 characters.');
        }
    }

    public function id(): InventoryId { return $this->id; }
    public function tenantId(): TenantId { return $this->tenantId; }
    public function siteId(): SiteId { return $this->siteId; }
    public function code(): string { return $this->code; }
    public function name(): string { return trim($this->name); }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
