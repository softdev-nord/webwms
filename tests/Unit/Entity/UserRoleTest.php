<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserRole;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserRoleTest'
)]
#[CoversClass(UserRole::class)]
final class UserRoleTest extends TestCase
{
    private UserRole $userRole;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRole = new UserRole();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->userRole->setId($id);
        self::assertSame($id, $this->userRole->getId());

        // Test setDescription() and getUsername()
        $description = 'Administrator';
        $this->userRole->setDescription($description);
        self::assertSame($description, $this->userRole->getDescription());

        // Test setUserRole() and getUserRole()
        $role = 'ROLE_ADMIN';
        $this->userRole->setUserRole($role);
        self::assertSame($role, $this->userRole->getUserRole());

        // Test setUserRole() and getUserRole()
        $role = 'read';
        $this->userRole->setUserRole($role);
        self::assertSame($role, $this->userRole->getUserRole());

        // Test setUserRights() and getUserRights()
        $rights = ['1', '2', '3', '4'];
        $this->userRole->setUserRights($rights);
        self::assertIsArray($this->userRole->getUserRights());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->userRole->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->userRole->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->userRole->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->userRole->getUpdatedAt());
    }
}
