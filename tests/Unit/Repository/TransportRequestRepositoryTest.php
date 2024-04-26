<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\TransportRequestRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        TransportRequestRepositoryTest
 */
#[CoversClass(TransportRequestRepository::class)]
final class TransportRequestRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $transportRequestRepository = new TransportRequestRepository($registry);

        self::assertInstanceOf(TransportRequestRepository::class, $transportRequestRepository);
    }
}
