<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\SupplierOrderPosRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosRepositoryTest
 *
 * @covers \WebWMS\Repository\SupplierOrderPosRepository
 */
final class SupplierOrderPosRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SupplierOrderPosRepository($registry);

        self::assertInstanceOf(SupplierOrderPosRepository::class, $repository);
    }
}
