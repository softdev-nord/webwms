<?php

declare(strict_types=1);

namespace WebWMS\Administration\Domain\Access;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Shared\Domain\Model\AggregateRoot;

final class UserAccount extends AggregateRoot
{
    /** @var array<string, RoleId> */
    private array $roles = [];

    private function __construct(
        private readonly UserId $id,
        private readonly TenantId $tenantId,
        private readonly string $email,
        private string $displayName,
        private string $passwordHash,
        private UserStatus $status,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * @param list<RoleId> $roles
     */
    public static function create(
        UserId $id,
        TenantId $tenantId,
        string $email,
        string $displayName,
        string $passwordHash,
        array $roles,
        DateTimeImmutable $now,
    ): self {
        $email = strtolower(trim($email));
        $displayName = trim($displayName);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 255) {
            throw new InvalidArgumentException('A user email must be valid.');
        }

        if ($displayName === '' || mb_strlen($displayName) > 255) {
            throw new InvalidArgumentException('A display name must contain 1 to 255 characters.');
        }

        if (trim($passwordHash) === '') {
            throw new InvalidArgumentException('A password hash must not be empty.');
        }

        $user = new self(
            $id,
            $tenantId,
            $email,
            $displayName,
            $passwordHash,
            UserStatus::Active,
            $now,
            $now,
        );

        foreach ($roles as $roleId) {
            $user->roles[$roleId->value()] = $roleId;
        }

        $user->recordThat(new UserCreated($id, $tenantId, $now));

        return $user;
    }

    public function assignRole(RoleId $roleId, DateTimeImmutable $now): void
    {
        $this->assertActive();

        if (isset($this->roles[$roleId->value()])) {
            return;
        }

        $this->roles[$roleId->value()] = $roleId;
        $this->updatedAt = $now;
    }

    public function removeRole(RoleId $roleId, DateTimeImmutable $now): void
    {
        $this->assertActive();

        if (!isset($this->roles[$roleId->value()])) {
            return;
        }

        unset($this->roles[$roleId->value()]);
        $this->updatedAt = $now;
    }

    public function deactivate(DateTimeImmutable $now): void
    {
        if ($this->status === UserStatus::Inactive) {
            return;
        }

        $this->status = UserStatus::Inactive;
        $this->updatedAt = $now;
    }

    public function activate(DateTimeImmutable $now): void
    {
        if ($this->status === UserStatus::Active) {
            return;
        }

        $this->status = UserStatus::Active;
        $this->updatedAt = $now;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function status(): UserStatus
    {
        return $this->status;
    }

    /** @return list<RoleId> */
    public function roles(): array
    {
        return array_values($this->roles);
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function assertActive(): void
    {
        if ($this->status !== UserStatus::Active) {
            throw new DomainException('Roles of an inactive user cannot be changed.');
        }
    }
}
