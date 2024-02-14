<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\StockLocationRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationRepositoryTest
 */
#[CoversClass(StockLocationRepository::class)]
final class StockLocationRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $stockLocationRepository = new StockLocationRepository($registry);

        self::assertInstanceOf(StockLocationRepository::class, $stockLocationRepository);
    }
}
