<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

use DateTimeImmutable;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Shared\Domain\Model\AggregateRoot;

final class Role extends AggregateRoot
{
    /** @var array<string, PermissionKey> */
    private array $permissions = [];

    private function __construct(
        private readonly RoleId $id,
        private readonly TenantId $tenantId,
        private readonly string $code,
        private string $name,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * @param list<PermissionKey> $permissions
     */
    public static function create(
        RoleId $id,
        TenantId $tenantId,
        string $code,
        string $name,
        array $permissions,
        DateTimeImmutable $now,
    ): self {
        $code = strtoupper(trim($code));
        $name = trim($name);

        if (preg_match('/^ROLE_[A-Z][A-Z0-9_]{1,44}$/', $code) !== 1) {
            throw new InvalidArgumentException('A role code must use the ROLE_NAME format.');
        }

        if ($name === '' || mb_strlen($name) > 150) {
            throw new InvalidArgumentException('A role name must contain 1 to 150 characters.');
        }

        $role = new self($id, $tenantId, $code, $name, $now, $now);

        foreach ($permissions as $permission) {
            $role->permissions[$permission->value()] = $permission;
        }

        $role->recordThat(new RoleCreated($id, $tenantId, $now));

        return $role;
    }

    public function grant(PermissionKey $permission, DateTimeImmutable $now): void
    {
        if (isset($this->permissions[$permission->value()])) {
            return;
        }

        $this->permissions[$permission->value()] = $permission;
        $this->updatedAt = $now;
    }

    public function revoke(PermissionKey $permission, DateTimeImmutable $now): void
    {
        if (!isset($this->permissions[$permission->value()])) {
            return;
        }

        unset($this->permissions[$permission->value()]);
        $this->updatedAt = $now;
    }

    public function id(): RoleId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return list<PermissionKey> */
    public function permissions(): array
    {
        return array_values($this->permissions);
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
