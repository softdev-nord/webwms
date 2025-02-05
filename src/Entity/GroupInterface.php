<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'GroupAwareUser'
)]
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
