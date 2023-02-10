<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\User;

class UserTest extends TestCase
{
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
