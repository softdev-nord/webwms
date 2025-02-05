<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserRight;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserRightTest'
)]
#[CoversClass(UserRight::class)]
final class UserRightTest extends TestCase
{
    private UserRight $userRight;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRight = new UserRight();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->userRight->setId($id);
        self::assertSame($id, $this->userRight->getId());

        // Test setUserRight() and getUserRight()
        $right = 'create';
        $this->userRight->setUserRight($right);
        self::assertSame($right, $this->userRight->getUserRight());

        // Test setDescription() and getUsername()
        $description = 'Erstellen';
        $this->userRight->setDescription($description);
        self::assertSame($description, $this->userRight->getDescription());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->userRight->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->userRight->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->userRight->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->userRight->getUpdatedAt());
    }
}
