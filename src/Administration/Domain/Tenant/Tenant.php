<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Tenant;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;
use WebWMS\Shared\Domain\Model\AggregateRoot;

final class Tenant extends AggregateRoot
{
    private function __construct(
        private readonly TenantId $id,
        private string $name,
        private TenantStatus $status,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(TenantId $id, string $name, DateTimeImmutable $now): self
    {
        $name = self::normalizeName($name);
        $tenant = new self($id, $name, TenantStatus::Active, $now, $now);
        $tenant->recordThat(new TenantCreated($id, $name, $now));

        return $tenant;
    }

    public function rename(string $name, DateTimeImmutable $now): void
    {
        $this->assertActive();
        $name = self::normalizeName($name);

        if ($name === $this->name) {
            return;
        }

        $this->name = $name;
        $this->updatedAt = $now;
    }

    public function deactivate(DateTimeImmutable $now): void
    {
        if ($this->status === TenantStatus::Inactive) {
            return;
        }

        $this->status = TenantStatus::Inactive;
        $this->updatedAt = $now;
    }

    public function id(): TenantId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function status(): TenantStatus
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
            throw new InvalidArgumentException('A tenant name must not be empty.');
        }

        if (mb_strlen($name) > 255) {
            throw new InvalidArgumentException('A tenant name must not exceed 255 characters.');
        }

        return $name;
    }

    private function assertActive(): void
    {
        if ($this->status !== TenantStatus::Active) {
            throw new DomainException('An inactive tenant cannot be changed.');
        }
    }
}
