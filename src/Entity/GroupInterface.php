<?php

declare(strict_types=1);

namespace WebWMS\Entity;

interface GroupInterface
{
    public function addRole(string $role): void;

    public function getGroup(): string;

    public function setGroup(string $group): void;

    public function hasRole(string $role): bool;

    /** @return array<string> */
    public function getRoles(): array;

    /** @param array<string> $roles */
    public function setRoles(array $roles): void;

    public function removeRole(string $role): void;
}
