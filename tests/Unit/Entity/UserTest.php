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

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->user);
        unset($this->dateTime);
    }

    public function testGetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('id');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getId());
    }

    public function testSetId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('id');
        $this->user->setId($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetUsername(): void
    {
        $expected = 'Username';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('username');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getUsername());
    }

    public function testSetUsername(): void
    {
        $expected = 'Username';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('username');
        $this->user->setUsername($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetFirstname(): void
    {
        $expected = 'Firstname';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('firstname');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getFirstname());
    }

    public function testSetFirstname(): void
    {
        $expected = 'Firstname';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('firstname');
        $this->user->setFirstname($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetLastname(): void
    {
        $expected = 'Lastname';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('lastname');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getLastname());
    }

    public function testSetLastname(): void
    {
        $expected = 'Lastname';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('lastname');
        $this->user->setLastname($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetEmail(): void
    {
        $expected = 'Email';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('email');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getEmail());
    }

    public function testSetEmail(): void
    {
        $expected = 'Email';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('email');
        $this->user->setEmail($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetLastLogin(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('lastLogin');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getLastLogin());
    }

    public function testSetLastLogin(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('lastLogin');
        $this->user->setLastLogin($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testIsEnabled(): void
    {
        $property = (new \ReflectionClass(User::class))
            ->getProperty('enabled');
        $property->setValue($this->user, true);
        $this->assertSame(true, $this->user->isEnabled());
    }

    public function testSetEnabled(): void
    {
        $expected = true;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('enabled');
        $this->user->setEnabled($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('createdAt');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('createdAt');
        $this->user->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('updatedAt');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(User::class))
            ->getProperty('updatedAt');
        $this->user->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetPassword(): void
    {
        $expected = 'Password';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('password');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getPassword());
    }

    public function testSetPassword(): void
    {
        $expected = 'Password';
        $property = (new \ReflectionClass(User::class))
            ->getProperty('password');
        $this->user->setPassword($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetRole(): void
    {
        $expected = $this->createMock(Role::class);
        $property = (new \ReflectionClass(User::class))
            ->getProperty('role');
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getRole());
    }

    public function testSetRole(): void
    {
        $expected = $this->createMock(Role::class);
        $property = (new \ReflectionClass(User::class))
            ->getProperty('role');
        $this->user->setRole($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testAddUser(): void
    {
        $this->setName('AddUser');
        $user = new User();

        $user->setUsername('TestUser');
        $user->setFirstname('TestFirstname');
        $user->setLastname('TestLastname');
        $user->setPassword('password');

        $this->assertEquals('TestUser', $user->getUsername());
        $this->assertEquals('TestFirstname', $user->getFirstname());
        $this->assertEquals('TestLastname', $user->getLastname());
        $this->assertEquals('password', $user->getPassword());
    }
}
