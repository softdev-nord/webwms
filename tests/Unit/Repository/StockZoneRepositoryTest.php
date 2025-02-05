<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\StockZoneRepository;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Repository',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockZoneRepositoryTest'
)]
#[CoversClass(StockZoneRepository::class)]
final class StockZoneRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $stockZoneRepository = new StockZoneRepository($registry);

        self::assertInstanceOf(StockZoneRepository::class, $stockZoneRepository);
    }
}
