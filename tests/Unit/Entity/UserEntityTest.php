<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserEntity;
use WebWMS\Entity\UserGroupEntity;
use WebWMS\Entity\UserInterface;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserEntityTest
 */
#[CoversClass(UserEntity::class)]
final class UserEntityTest extends TestCase
{
    private UserEntity $userEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->userEntity = new UserEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->userEntity->setId($id);
        self::assertEquals($id, $this->userEntity->getId());

        // Test setUsername() and getUsername()
        $username = 'rirrgang';
        $this->userEntity->setUsername($username);
        self::assertEquals($username, $this->userEntity->getUsername());

        // Test eraseCredentials()
        $plainPassword = null;
        $this->userEntity->eraseCredentials();
        self::assertNull($plainPassword);

        // Test setFirstname() and getFirstname()
        $firstname = 'Rene';
        $this->userEntity->setFirstname($firstname);
        self::assertEquals($firstname, $this->userEntity->getFirstname());

        // Test setLastname() and getLastname()
        $lastname = 'Irrgang';
        $this->userEntity->setLastname($lastname);
        self::assertEquals($lastname, $this->userEntity->getLastname());

        // Test setEmail() and getEmail()
        $email = 'info@softdev-nord.de';
        $this->userEntity->setEmail($email);
        self::assertEquals($email, $this->userEntity->getEmail());

        // Test isEnabled()
        $this->userEntity->setEnabled(true);
        self::assertTrue($this->userEntity->isEnabled());

        // Test setLastLogin() and getLastLogin()
        $lastLogin = $this->dateTime;
        $this->userEntity->setLastLogin($lastLogin);
        self::assertEquals($lastLogin, $this->userEntity->getLastLogin());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->userEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->userEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->userEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->userEntity->getUpdatedAt());

        // Test serialize()
        $expected = serialize([1, 'test', 'password']);
        $this->userEntity->setId(1);
        $this->userEntity->setUsername('test');
        $this->userEntity->setPassword('password');
        self::assertEquals($expected, $this->userEntity->serialize());

        // Test getUserIdentifier()
        $userIdentifier = 'test';
        self::assertEquals($userIdentifier, $this->userEntity->getUserIdentifier());

        // Test setPassword() and getPassword()
        $expected = 'Password';
        $this->userEntity->setPassword($expected);
        self::assertSame($expected, $this->userEntity->getPassword());

        // Test setRoles() and getRoles()
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $this->userEntity->setRoles($roles);
        self::assertSame($roles, $this->userEntity->getRoles());

        // Test setPlainPassword() and getPlainPassword()
        $plainPassword = 'plainPassword';
        $this->userEntity->setPlainPassword($plainPassword);
        self::assertSame($plainPassword, $this->userEntity->getPlainPassword());
    }

    //    public function testAddUser(): void
    //    {
    //        $this->setName('AddUser');
    //        $user = new UserEntity();
    //
    //        $user->setUsername('TestUser');
    //        $user->setFirstname('TestFirstname');
    //        $user->setLastname('TestLastname');
    //        $user->setPassword('password');
    //
    //        self::assertEquals('TestUser', $user->getUsername());
    //        self::assertEquals('TestFirstname', $user->getFirstname());
    //        self::assertEquals('TestLastname', $user->getLastname());
    //        self::assertEquals('password', $user->getPassword());
    //    }

    public function testToStringReturnsUsername(): void
    {
        $userEntity = new UserEntity();
        $username = 'rirrgang';
        $userEntity->setUsername($username);

        $result = (string) $userEntity;

        self::assertSame($username, $result);
    }

    public function testIsEqualToReturnsFalseIfUserIsNotInstanceOfSelf(): void
    {
        $userEntity = new UserEntity();
        $otherUser = $this->createMock(UserInterface::class);

        self::assertFalse($userEntity->isEqualTo($otherUser));
    }

    public function testIsEqualToReturnsTrueForEqualUser(): void
    {
        $user = new UserEntity();
        $user->setPassword('password');
        $user->setUsername('username');

        $otherUser = new UserEntity();
        $otherUser->setPassword('password');
        $otherUser->setUsername('username');

        $isEqual = $user->isEqualTo($otherUser);

        self::assertTrue($isEqual);
    }

    public function testIsEqualToReturnsFalseForDifferentPassword(): void
    {
        $user = new UserEntity();
        $user->setPassword('password');
        $user->setUsername('username');

        $otherUser = new UserEntity();
        $otherUser->setPassword('different_password');
        $otherUser->setUsername('username');

        $isEqual = $user->isEqualTo($otherUser);

        self::assertFalse($isEqual);
    }

    public function testIsEqualToReturnsFalseForDifferentUsername(): void
    {
        $user = new UserEntity();
        $user->setPassword('password');
        $user->setUsername('username');

        $otherUser = new UserEntity();
        $otherUser->setPassword('password');
        $otherUser->setUsername('different_username');

        $isEqual = $user->isEqualTo($otherUser);

        self::assertFalse($isEqual);
    }

    public function testGetRolesReturnsDefaultRoleWhenNoRolesSet(): void
    {
        $userEntity = new UserEntity();

        $roles = $userEntity->getRoles();

        self::assertContains(UserEntity::ROLE_DEFAULT, $roles);
    }

    public function testGetRolesReturnsUniqueRoles(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $roles = $userEntity->getRoles();

        self::assertEquals(['ROLE_USER', 'ROLE_ADMIN'], $roles);
    }

    public function testGetRoles(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setRoles(['ROLE_USER']);

        $roles = $userEntity->getRoles();

        self::assertEquals(['ROLE_USER'], $roles);
    }

    public function testIsSuperAdminReturnsFalseByDefault(): void
    {
        $userEntity = new UserEntity();

        $isSuperAdmin = $userEntity->isSuperAdmin();

        self::assertFalse($isSuperAdmin);
    }

    public function testSetSuperAdminAddsSuperAdminRole(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setSuperAdmin(true);

        $roles = $userEntity->getRoles();

        self::assertContains(UserEntity::ROLE_SUPER_ADMIN, $roles);
    }

    public function testSetSuperAdminRemovesSuperAdminRole(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setRoles(['ROLE_USER', 'ROLE_SUPER_ADMIN']);
        $userEntity->setSuperAdmin(false);

        $roles = $userEntity->getRoles();

        self::assertNotContains(UserEntity::ROLE_SUPER_ADMIN, $roles);
    }

    public function testAddRole(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setRoles(['ROLE_USER']);
        $userEntity->addRole('ROLE_ADMIN');

        $roles = $userEntity->getRoles();

        self::assertContains('ROLE_USER', $roles);
        self::assertContains('ROLE_ADMIN', $roles);
    }

    public function testIsAccountNonLockedReturnsTrue(): void
    {
        $userEntity = new UserEntity();
        $result = $userEntity->isAccountNonLocked();

        self::assertTrue($result);
    }

    public function testGetGroupsReturnsNewArrayCollectionWhenNotSet(): void
    {
        $userEntity = new UserEntity();
        $groups = $userEntity->getGroups();

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

        $this->userEntity->setUserGroups($userGroups);
        self::assertEquals($userGroups, $this->userEntity->getUserGroups());
    }

    public function testGetGroupNames(): void
    {
        $userEntity = new UserEntity();
        $userGroups = [
            ['group' => 'Group 1'],
            ['group' => 'Group 2'],
        ];
        $userEntity->setUserGroups($userGroups);

        $groupNames = ['Group 1', 'Group 2'];

        self::assertEquals($groupNames, $userEntity->getGroupNames());
    }

    public function testHasGroup(): void
    {
        $userGroupEntity = new UserGroupEntity();
        $userGroupEntity->setGroup('Group 1');

        self::assertFalse($this->userEntity->hasGroup('Group 3'));
    }
}
