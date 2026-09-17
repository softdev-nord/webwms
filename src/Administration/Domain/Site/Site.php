<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Site;

use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Shared\Domain\Model\AggregateRoot;

final class Site extends AggregateRoot
{
    private function __construct(
        private readonly SiteId $id,
        private readonly TenantId $tenantId,
        private readonly SiteCode $code,
        private string $name,
        private string $timezone,
        private SiteStatus $status,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(
        SiteId $id,
        TenantId $tenantId,
        SiteCode $code,
        string $name,
        string $timezone,
        DateTimeImmutable $now,
    ): self {
        $name = self::normalizeName($name);
        $timezone = self::validateTimezone($timezone);
        $site = new self($id, $tenantId, $code, $name, $timezone, SiteStatus::Active, $now, $now);
        $site->recordThat(new SiteCreated($id, $tenantId, $code, $now));

        return $site;
    }

    public function changeDetails(string $name, string $timezone, DateTimeImmutable $now): void
    {
        $this->assertActive();
        $name = self::normalizeName($name);
        $timezone = self::validateTimezone($timezone);

        if ($name === $this->name && $timezone === $this->timezone) {
            return;
        }

        $this->name = $name;
        $this->timezone = $timezone;
        $this->updatedAt = $now;
    }

    public function deactivate(DateTimeImmutable $now): void
    {
        if ($this->status === SiteStatus::Inactive) {
            return;
        }

        $this->status = SiteStatus::Inactive;
        $this->updatedAt = $now;
    }

    public function activate(DateTimeImmutable $now): void
    {
        if ($this->status === SiteStatus::Active) {
            return;
        }

        $this->status = SiteStatus::Active;
        $this->updatedAt = $now;
    }

    public function id(): SiteId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function code(): SiteCode
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function timezone(): string
    {
        return $this->timezone;
    }

    public function status(): SiteStatus
    {
        return $this->status;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private static function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('A site name must not be empty.');
        }

        if (mb_strlen($name) > 255) {
            throw new InvalidArgumentException('A site name must not exceed 255 characters.');
        }

        return $name;
    }

    private static function validateTimezone(string $timezone): string
    {
        $timezone = trim($timezone);

        if (!in_array($timezone, DateTimeZone::listIdentifiers(), true)) {
            throw new InvalidArgumentException('A site timezone must be a valid IANA timezone.');
        }

        return $timezone;
    }

    private function assertActive(): void
    {
        if ($this->status !== SiteStatus::Active) {
            throw new DomainException('An inactive site cannot be changed.');
        }
    }
}
