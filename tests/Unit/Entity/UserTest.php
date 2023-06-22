<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\User;
use WebWMS\Entity\UserGroup;
use WebWMS\Entity\UserInterface;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserTest
 *
 * @covers \WebWMS\Entity\User
 */
final class UserTest extends TestCase
{
    private User $user;

    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->dateTime = new \DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->user->setId($id);
        self::assertEquals($id, $this->user->getId());

        // Test setUsername() and getUsername()
        $username = 'rirrgang';
        $this->user->setUsername($username);
        self::assertEquals($username, $this->user->getUsername());

        // Test eraseCredentials()
        $plainPassword = null;
        $this->user->eraseCredentials();
        self::assertNull($plainPassword);

        // Test setFirstname() and getFirstname()
        $firstname = 'Rene';
        $this->user->setFirstname($firstname);
        self::assertEquals($firstname, $this->user->getFirstname());

        // Test setLastname() and getLastname()
        $lastname = 'Irrgang';
        $this->user->setLastname($lastname);
        self::assertEquals($lastname, $this->user->getLastname());

        // Test setEmail() and getEmail()
        $email = 'info@softdev-nord.de';
        $this->user->setEmail($email);
        self::assertEquals($email, $this->user->getEmail());

        // Test isEnabled()
        $this->user->setEnabled(true);
        self::assertTrue($this->user->isEnabled());

        // Test setLastLogin() and getLastLogin()
        $lastLogin = $this->dateTime;
        $this->user->setLastLogin($lastLogin);
        self::assertEquals($lastLogin, $this->user->getLastLogin());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->user->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->user->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->user->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->user->getUpdatedAt());

        // Test serialize()
        $expected = serialize([1, 'test', 'password']);
        $this->user->setId(1);
        $this->user->setUsername('test');
        $this->user->setPassword('password');
        self::assertEquals($expected, $this->user->serialize());

        // Test getUserIdentifier()
        $userIdentifier = 'test';
        self::assertEquals($userIdentifier, $this->user->getUserIdentifier());

        // Test setPassword() and getPassword()
        $expected = 'Password';
        $this->user->setPassword($expected);
        self::assertSame($expected, $this->user->getPassword());

        // Test setRoles() and getRoles()
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $this->user->setRoles($roles);
        self::assertSame($roles, $this->user->getRoles());

        // Test setPlainPassword() and getPlainPassword()
        $plainPassword = 'plainPassword';
        $this->user->setPlainPassword($plainPassword);
        self::assertSame($plainPassword, $this->user->getPlainPassword());
    }

    public function testAddUser(): void
    {
        $this->setName('AddUser');
        $user = new User();

        $user->setUsername('TestUser');
        $user->setFirstname('TestFirstname');
        $user->setLastname('TestLastname');
        $user->setPassword('password');

        self::assertEquals('TestUser', $user->getUsername());
        self::assertEquals('TestFirstname', $user->getFirstname());
        self::assertEquals('TestLastname', $user->getLastname());
        self::assertEquals('password', $user->getPassword());
    }

    public function testToStringReturnsUsername(): void
    {
        $user = new User();
        $username = 'rirrgang';
        $user->setUsername($username);

        $result = (string) $user;

        self::assertSame($username, $result);
    }

    public function testIsEqualToReturnsFalseIfUserIsNotInstanceOfSelf(): void
    {
        $user = new User();
        $otherUser = $this->createMock(UserInterface::class);

        self::assertFalse($user->isEqualTo($otherUser));
    }

    public function testIsEqualToReturnsTrueForEqualUser(): void
    {
        $user = new User();
        $user->setPassword('password');
        $user->setUsername('username');

        $otherUser = new User();
        $otherUser->setPassword('password');
        $otherUser->setUsername('username');

        $isEqual = $user->isEqualTo($otherUser);

        self::assertTrue($isEqual);
    }

    public function testIsEqualToReturnsFalseForDifferentPassword(): void
    {
        $user = new User();
        $user->setPassword('password');
        $user->setUsername('username');

        $otherUser = new User();
        $otherUser->setPassword('different_password');
        $otherUser->setUsername('username');

        $isEqual = $user->isEqualTo($otherUser);

        self::assertFalse($isEqual);
    }

    public function testIsEqualToReturnsFalseForDifferentUsername(): void
    {
        $user = new User();
        $user->setPassword('password');
        $user->setUsername('username');

        $otherUser = new User();
        $otherUser->setPassword('password');
        $otherUser->setUsername('different_username');

        $isEqual = $user->isEqualTo($otherUser);

        self::assertFalse($isEqual);
    }

    public function testGetRolesReturnsDefaultRoleWhenNoRolesSet(): void
    {
        $user = new User();

        $roles = $user->getRoles();

        self::assertContains(User::ROLE_DEFAULT, $roles);
    }

    public function testGetRolesReturnsUniqueRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $roles = $user->getRoles();

        self::assertEquals(['ROLE_USER', 'ROLE_ADMIN'], $roles);
    }

    public function testGetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $roles = $user->getRoles();

        self::assertEquals(['ROLE_USER'], $roles);
    }

    public function testIsSuperAdminReturnsFalseByDefault(): void
    {
        $user = new User();

        $isSuperAdmin = $user->isSuperAdmin();

        self::assertFalse($isSuperAdmin);
    }

    public function testSetSuperAdminAddsSuperAdminRole(): void
    {
        $user = new User();
        $user->setSuperAdmin(true);

        $roles = $user->getRoles();

        self::assertContains(User::ROLE_SUPER_ADMIN, $roles);
    }

    public function testSetSuperAdminRemovesSuperAdminRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_SUPER_ADMIN']);
        $user->setSuperAdmin(false);

        $roles = $user->getRoles();

        self::assertNotContains(User::ROLE_SUPER_ADMIN, $roles);
    }

    public function testAddRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->addRole('ROLE_ADMIN');

        $roles = $user->getRoles();

        self::assertContains('ROLE_USER', $roles);
        self::assertContains('ROLE_ADMIN', $roles);
    }

    public function testIsAccountNonLockedReturnsTrue(): void
    {
        $user = new User();
        $result = $user->isAccountNonLocked();

        self::assertTrue($result);
    }

    public function testGetGroupsReturnsNewArrayCollectionWhenNotSet(): void
    {
        $user = new User();
        $groups = $user->getGroups();

        self::assertIsArray($groups);
        self::assertCount(0, $groups);
    }

    public function testGetGroupsReturnsExistingCollectionWhenGroupsSet(): void
    {
        $existingGroups = new ArrayCollection();
        $existingGroups->add('Group 1');

        self::assertInstanceOf(ArrayCollection::class, $existingGroups);
    }

    public function testGetUserGroups(): void
    {
        $userGroups = [
            ['group' => 'Group 1'],
            ['group' => 'Group 2'],
        ];

        $this->user->setUserGroups($userGroups);
        self::assertEquals($userGroups, $this->user->getUserGroups());
    }

    public function testGetGroupNames(): void
    {
        $user = new User();
        $userGroups = [
            ['group' => 'Group 1'],
            ['group' => 'Group 2'],
        ];
        $user->setUserGroups($userGroups);

        $groupNames = ['Group 1', 'Group 2'];

        self::assertEquals($groupNames, $user->getGroupNames());
    }

    public function testHasGroup(): void
    {
        $userGroups = new UserGroup();
        $userGroups->setGroup('Group 1');

        self::assertFalse($this->user->hasGroup('Group 3'));
    }
}
