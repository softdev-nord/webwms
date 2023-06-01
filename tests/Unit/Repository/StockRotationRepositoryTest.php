<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\StockRotationRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationRepositoryTest
 *
 * @covers \WebWMS\Repository\StockRotationRepository
 */
final class StockRotationRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new StockRotationRepository($registry);

        self::assertInstanceOf(StockRotationRepository::class, $repository);
    }
}
