<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\ConfigurationRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        ConfigurationRepositoryTest
 */
#[CoversClass(ConfigurationRepository::class)]
final class ConfigurationRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $configurationRepository = new ConfigurationRepository($registry);

        self::assertInstanceOf(ConfigurationRepository::class, $configurationRepository);
    }
}
