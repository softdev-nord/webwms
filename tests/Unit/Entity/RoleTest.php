<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Role;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        RoleTest
 *
 * @covers \WebWMS\Entity\Role
 */
final class RoleTest extends TestCase
{
    private Role $role;

    private \DateTime $dateTime;

    private ArrayCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = new Role();
        $this->dateTime = new \DateTime();
        $this->collection = new ArrayCollection();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->role->setId($id);
        self::assertEquals($id, $this->role->getId());

        // Test setName() and getName()
        $name = 'Test Role Name';
        $this->role->setName($name);
        self::assertEquals($name, $this->role->getName());

        // Test setRole() and getRole()
        $role = 'Role User';
        $this->role->setRole($role);
        self::assertEquals($role, $this->role->getRole());

        // Test setUsers() and getUsers()
        $user = $this->collection;
        $this->role->setUsers($user);
        self::assertEquals($user, $this->role->getUsers());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->role->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->role->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->role->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->role->getUpdatedAt());
    }
}
