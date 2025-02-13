<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    interface: 'GroupAwareUser'
)]
interface GroupInterface
{
    public function addRole(string $role): self;

    public function getGroup(): string;

    public function setGroup(string $group): self;

    public function hasRole(string $role): bool;

    /** @return array<string> */
    public function getRoles(): array;

    /** @param array<string> $roles */
    public function setRoles(array $roles): self;

    public function removeRole(string $role): self;
}
