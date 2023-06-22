<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserRight;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRightTest
 *
 * @covers \WebWMS\Entity\UserRight
 */
final class UserRightTest extends TestCase
{
    private UserRight $userRight;

    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRight = new UserRight();
        $this->dateTime = new \DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->userRight->setId($id);
        self::assertEquals($id, $this->userRight->getId());

        // Test setUserRight() and getUserRight()
        $right = 'create';
        $this->userRight->setUserRight($right);
        self::assertEquals($right, $this->userRight->getUserRight());

        // Test setDescription() and getUsername()
        $description = 'Erstellen';
        $this->userRight->setDescription($description);
        self::assertEquals($description, $this->userRight->getDescription());

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
