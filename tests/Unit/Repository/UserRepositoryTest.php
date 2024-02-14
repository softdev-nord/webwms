<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\UserRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRepositoryTest
 */
#[CoversClass(UserRepository::class)]
final class UserRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $userRepository = new UserRepository($registry);

        self::assertInstanceOf(UserRepository::class, $userRepository);
    }
}
