<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Role;
use WebWMS\Entity\User;

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

    private Role $role;

    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->role = new Role();
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

        // Test setRole() and getRole()
        $role = $this->role;
        $this->user->setRole($role);
        self::assertEquals($role, $this->user->getRole());

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
}
