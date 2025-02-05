<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Configuration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\Configuration\ConfigurationService;
use WebWMS\Service\DataHandlers\Configuration\ConfigurationDataHandler;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Configuration',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'ConfigurationServiceTest'
)]
#[CoversClass(ConfigurationService::class)]
final class ConfigurationServiceTest extends TestCase
{
    private ConfigurationService $configurationService;

    private MockObject $mockObject;

    private string $appVersion = 'Enterprise Version';

    private string $appVersionNumber = '2.0.0';

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(ConfigurationDataHandler::class);
        $this->configurationService = new ConfigurationService(
            $this->mockObject,
            $this->appVersion,
            $this->appVersionNumber
        );
    }

    public function testGetAllConfigurations(): void
    {
        $configurations = [
            'config1' => 'configValue1',
            'config2' => 123,
            456 => 'configValue3',
        ];

        $this->mockObject
            ->expects($this->once())
            ->method('getAllConfigurations')
            ->willReturn($configurations);

        $result = $this->configurationService->getAllConfigurations();

        self::assertSame($configurations, $result);
    }
}
