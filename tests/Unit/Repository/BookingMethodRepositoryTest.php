<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\BookingMethodRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BookingMethodRepositoryTest
 *
 * @covers \WebWMS\Repository\BookingMethodRepository
 */
final class BookingMethodRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new BookingMethodRepository($registry);

        self::assertInstanceOf(BookingMethodRepository::class, $repository);
    }
}
