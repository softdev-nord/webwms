<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\StockOccupancyRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyRepositoryTest
 *
 * @covers \WebWMS\Repository\StockOccupancyRepository
 */
final class StockOccupancyRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new StockOccupancyRepository($registry);

        self::assertInstanceOf(StockOccupancyRepository::class, $repository);
    }
}
