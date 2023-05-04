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

    private \DateTimeImmutable $dateTime;

    private ArrayCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = new Role();
        $this->dateTime = new \DateTimeImmutable();
        $this->collection = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->role);
        unset($this->dateTime);
        unset($this->collection);
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('id');
        $property->setValue($this->role, $expected);
        self::assertSame($expected, $this->role->getId());
    }

    public function testSetId(): void
    {
        $expected = 42;
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('id');
        $this->role->setId($expected);
        self::assertSame($expected, $property->getValue($this->role));
    }

    public function testGetName(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('name');
        $property->setValue($this->role, $expected);
        self::assertSame($expected, $this->role->getName());
    }

    public function testSetName(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('name');
        $this->role->setName($expected);
        self::assertSame($expected, $property->getValue($this->role));
    }

    public function testGetRole(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('role');
        $property->setValue($this->role, $expected);
        self::assertSame($expected, $this->role->getRole());
    }

    public function testSetRole(): void
    {
        $expected = '42';
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('role');
        $this->role->setRole($expected);
        self::assertSame($expected, $property->getValue($this->role));
    }

    public function testGetUsers(): void
    {
        $expected = $this->collection;
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('user');
        $property->setValue($this->role, $expected);
        self::assertSame($expected, $this->role->getUsers());
    }

    public function testSetUsers(): void
    {
        $expected = $this->collection;
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('user');
        $this->role->setUsers($expected);
        self::assertSame($expected, $property->getValue($this->role));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('createdAt');
        $property->setValue($this->role, $expected);
        self::assertSame($expected, $this->role->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('createdAt');
        $this->role->setCreatedAt($expected);
        self::assertSame($expected, $property->getValue($this->role));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('updatedAt');
        $property->setValue($this->role, $expected);
        self::assertSame($expected, $this->role->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Role::class))
            ->getProperty('updatedAt');
        $this->role->setUpdatedAt($expected);
        self::assertSame($expected, $property->getValue($this->role));
    }
}
