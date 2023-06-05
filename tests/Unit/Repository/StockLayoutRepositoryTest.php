<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\StockLayoutRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutRepositoryTest
 *
 * @covers \WebWMS\Repository\StockLayoutRepository
 */
final class StockLayoutRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new StockLayoutRepository($registry);

        self::assertInstanceOf(StockLayoutRepository::class, $repository);
    }
}
