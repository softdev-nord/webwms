<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\CustomerOrderRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderRepositoryTest
 */
#[CoversClass(CustomerOrderRepository::class)]
final class CustomerOrderRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $customerOrderRepository = new CustomerOrderRepository($registry);

        self::assertInstanceOf(CustomerOrderRepository::class, $customerOrderRepository);
    }
}
