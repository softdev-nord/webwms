<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\CustomerOrderPosRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosRepositoryTest
 */
#[CoversClass(CustomerOrderPosRepository::class)]
final class CustomerOrderPosRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $customerOrderPosRepository = new CustomerOrderPosRepository($registry);

        self::assertInstanceOf(CustomerOrderPosRepository::class, $customerOrderPosRepository);
    }
}
