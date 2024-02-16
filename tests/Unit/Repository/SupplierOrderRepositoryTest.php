<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\SupplierOrderRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderRepositoryTest
 */
#[CoversClass(SupplierOrderRepository::class)]
final class SupplierOrderRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $supplierOrderRepository = new SupplierOrderRepository($registry);

        self::assertInstanceOf(SupplierOrderRepository::class, $supplierOrderRepository);
    }
}
