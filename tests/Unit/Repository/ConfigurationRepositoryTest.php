<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\ConfigurationRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ConfigurationRepositoryTest
 *
 * @covers \WebWMS\Repository\ConfigurationRepository
 */
final class ConfigurationRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ConfigurationRepository($registry);

        self::assertInstanceOf(ConfigurationRepository::class, $repository);
    }
}
