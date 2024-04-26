<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserGroupEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        UserGroupTest
 */
#[CoversClass(UserGroupEntity::class)]
final class UserGroupTest extends TestCase
{
    private UserGroupEntity $userGroupEntity;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userGroupEntity = new UserGroupEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->userGroupEntity->setId($id);
        self::assertEquals($id, $this->userGroupEntity->getId());

        // Test setGroup() and getGroup()
        $group = 'GROUP_SUPER_ADMIN';
        $this->userGroupEntity->setGroup($group);
        self::assertEquals($group, $this->userGroupEntity->getGroup());

        // Test setDescription() and getUsername()
        $description = 'Administratoren';
        $this->userGroupEntity->setDescription($description);
        self::assertEquals($description, $this->userGroupEntity->getDescription());

        // Test setRoles() and getRoles()
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $this->userGroupEntity->setRoles($roles);
        self::assertEquals($roles, $this->userGroupEntity->getRoles());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->userGroupEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->userGroupEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->userGroupEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->userGroupEntity->getUpdatedAt());
    }

    public function testToString(): void
    {
        $group = 'GROUP_SUPER_ADMIN';
        $this->userGroupEntity->setGroup($group);

        self::assertEquals($group, (string) $this->userGroupEntity);
    }

    public function testHasRole(): void
    {
        $userGroupEntity = new UserGroupEntity();
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $userGroupEntity->setRoles($roles);

        self::assertTrue($userGroupEntity->hasRole('ROLE_ADMIN'));
        self::assertTrue($userGroupEntity->hasRole('ROLE_USER'));
        self::assertFalse($userGroupEntity->hasRole('ROLE_GUEST'));
    }

    public function testAddRole(): void
    {
        $userGroupEntity = new UserGroupEntity();
        $userGroupEntity->setRoles(['ROLE_ADMIN']);
        $userGroupEntity->addRole('ROLE_USER');
        $userGroupEntity->addRole('ROLE_ADMIN');

        $expectedRoles = ['ROLE_ADMIN', 'ROLE_USER'];
        self::assertEquals($expectedRoles, $userGroupEntity->getRoles());
    }

    public function testRemoveRole(): void
    {
        $userGroupEntity = new UserGroupEntity();
        $userGroupEntity->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $userGroupEntity->removeRole('ROLE_ADMIN');
        $userGroupEntity->removeRole('ROLE_GUEST');

        $expectedRoles = ['ROLE_USER'];
        self::assertEquals($expectedRoles, $userGroupEntity->getRoles());
    }
}
