<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserRightEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        UserRightTest
 */
#[CoversClass(UserRightEntity::class)]
final class UserRightTest extends TestCase
{
    private UserRightEntity $userRightEntity;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRightEntity = new UserRightEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->userRightEntity->setId($id);
        self::assertEquals($id, $this->userRightEntity->getId());

        // Test setUserRight() and getUserRight()
        $right = 'create';
        $this->userRightEntity->setUserRight($right);
        self::assertEquals($right, $this->userRightEntity->getUserRight());

        // Test setDescription() and getUsername()
        $description = 'Erstellen';
        $this->userRightEntity->setDescription($description);
        self::assertEquals($description, $this->userRightEntity->getDescription());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->userRightEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->userRightEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->userRightEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->userRightEntity->getUpdatedAt());
    }
}
