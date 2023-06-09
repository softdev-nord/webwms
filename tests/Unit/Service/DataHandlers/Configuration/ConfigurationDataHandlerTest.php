<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\DataHandlers\Configuration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Configuration;
use WebWMS\Service\DataHandlers\Configuration\ConfigurationDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\DataHandlers\Configuration
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ConfigurationDataHandlerTest
 *
 * @covers \WebWMS\Service\DataHandlers\Configuration\ConfigurationDataHandler
 */
final class ConfigurationDataHandlerTest extends TestCase
{
    private ConfigurationDataHandler $configurationDataHandler;

    /**
     * @var (EntityManagerInterface&MockObject)|MockObject
     */
    private MockObject|EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->configurationDataHandler = new ConfigurationDataHandler(
            $this->entityManager
        );
    }

    public function testSave(): void
    {
        $configuration = $this->createMock(Configuration::class);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($configuration);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->configurationDataHandler->save($configuration);
    }

    public function testDelete(): void
    {
        $configuration = $this->createMock(Configuration::class);

        $this->entityManager
            ->expects(self::once())
            ->method('remove')
            ->with($configuration);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->configurationDataHandler->delete($configuration);
    }

    public function testConfigurationById(): void
    {
        $configurationId = 123;
        $expectedConfiguration = $this->createMock(Configuration::class);
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('find')
            ->with($configurationId)
            ->willReturn($expectedConfiguration);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(Configuration::class)
            ->willReturn($repository);

        $configuration = $this->configurationDataHandler->getConfigurationById($configurationId);

        self::assertSame($expectedConfiguration, $configuration);
    }

    public function testGetAllArticles(): void
    {
        $expectedConfiguration = [$this->createMock(Configuration::class)];
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedConfiguration);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(Configuration::class)
            ->willReturn($repository);

        $configurations = $this->configurationDataHandler->getAllConfigurations();

        self::assertSame($expectedConfiguration, $configurations['configuration']);
    }
}
