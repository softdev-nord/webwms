<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\TenantRepository;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Repository',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'SupplierRepositoryTest'
)]
#[CoversClass(TenantRepository::class)]
final class TentantRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $tenantRepository = new TenantRepository($registry);

        self::assertInstanceOf(TenantRepository::class, $tenantRepository);
    }
}
