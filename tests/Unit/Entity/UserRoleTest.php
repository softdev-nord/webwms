<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserRoleEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRoleTest
 */
#[CoversClass(UserRoleEntity::class)]
final class UserRoleTest extends TestCase
{
    private UserRoleEntity $userRoleEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRoleEntity = new UserRoleEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->userRoleEntity->setId($id);
        self::assertEquals($id, $this->userRoleEntity->getId());

        // Test setDescription() and getUsername()
        $description = 'Administrator';
        $this->userRoleEntity->setDescription($description);
        self::assertEquals($description, $this->userRoleEntity->getDescription());

        // Test setUserRole() and getUserRole()
        $role = 'ROLE_ADMIN';
        $this->userRoleEntity->setUserRole($role);
        self::assertEquals($role, $this->userRoleEntity->getUserRole());

        // Test setUserRole() and getUserRole()
        $role = 'read';
        $this->userRoleEntity->setUserRole($role);
        self::assertEquals($role, $this->userRoleEntity->getUserRole());

        // Test setUserRights() and getUserRights()
        $rights = ['1', '2', '3', '4'];
        $this->userRoleEntity->setUserRights($rights);
        self::assertIsArray($this->userRoleEntity->getUserRights());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->userRoleEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->userRoleEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->userRoleEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->userRoleEntity->getUpdatedAt());
    }
}
