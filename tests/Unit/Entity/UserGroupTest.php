<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserGroup;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserGroupTest'
)]
#[CoversClass(UserGroup::class)]
final class UserGroupTest extends TestCase
{
    private UserGroup $userGroup;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userGroup = new UserGroup();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->userGroup->setId($id);
        self::assertSame($id, $this->userGroup->getId());

        // Test setGroup() and getGroup()
        $group = 'GROUP_SUPER_ADMIN';
        $this->userGroup->setGroup($group);
        self::assertSame($group, $this->userGroup->getGroup());

        // Test setDescription() and getUsername()
        $description = 'Administratoren';
        $this->userGroup->setDescription($description);
        self::assertSame($description, $this->userGroup->getDescription());

        // Test setRoles() and getRoles()
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $this->userGroup->setRoles($roles);
        self::assertSame($roles, $this->userGroup->getRoles());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->userGroup->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->userGroup->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->userGroup->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->userGroup->getUpdatedAt());
    }

    public function testToString(): void
    {
        $group = 'GROUP_SUPER_ADMIN';
        $this->userGroup->setGroup($group);

        self::assertSame($group, (string) $this->userGroup);
    }

    public function testHasRole(): void
    {
        $userGroup = new UserGroup();
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $userGroup->setRoles($roles);

        self::assertTrue($userGroup->hasRole('ROLE_ADMIN'));
        self::assertTrue($userGroup->hasRole('ROLE_USER'));
        self::assertFalse($userGroup->hasRole('ROLE_GUEST'));
    }

    public function testAddRole(): void
    {
        $userGroup = new UserGroup();
        $userGroup->setRoles(['ROLE_ADMIN']);
        $userGroup->addRole('ROLE_USER');
        $userGroup->addRole('ROLE_ADMIN');

        $expectedRoles = ['ROLE_ADMIN', 'ROLE_USER'];
        self::assertSame($expectedRoles, $userGroup->getRoles());
    }

    public function testRemoveRole(): void
    {
        $userGroup = new UserGroup();
        $userGroup->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $userGroup->removeRole('ROLE_ADMIN');
        $userGroup->removeRole('ROLE_GUEST');

        $expectedRoles = ['ROLE_USER'];
        self::assertSame($expectedRoles, $userGroup->getRoles());
    }
}
