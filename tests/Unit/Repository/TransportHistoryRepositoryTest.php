<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\TransportHistoryRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportHistoryRepositoryTest
 *
 * @covers \WebWMS\Repository\TransportHistoryRepository
 */
final class TransportHistoryRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new TransportHistoryRepository($registry);

        self::assertInstanceOf(TransportHistoryRepository::class, $repository);
    }
}
