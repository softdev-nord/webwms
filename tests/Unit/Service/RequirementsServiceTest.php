<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\RequirementsService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'RequirementsServiceTest'
)]
#[CoversClass(RequirementsService::class)]
final class RequirementsServiceTest extends TestCase
{
    private RequirementsService $requirementsService;

    private string $appName;

    private string $appVersion;

    private string $appVersionNumber;

    private string $appCopyright;

    private string $appLizenz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appName = 'WebWms';
        $this->appVersion = 'Enterprise';
        $this->appVersionNumber = '1.0';
        $this->appCopyright = 'Copyright';
        $this->appLizenz = 'Lizenz';
        $this->requirementsService = new RequirementsService(
            $this->appName,
            $this->appVersion,
            $this->appVersionNumber,
            $this->appCopyright,
            $this->appLizenz
        );
    }

    public function testCheckDiskFreeSpace(): void
    {
        $expectedResult = $this->requirementsService->formatBytes(disk_free_space(__DIR__));

        self::assertSame($expectedResult, $this->requirementsService->checkDiskFreeSpace());
    }

    public function testGetterMethods(): void
    {
        // Test getAppName()
        $appName = $this->appName;
        self::assertSame($appName, $this->requirementsService->getAppName());

        // Test getAppVersion()
        $appVersion = $this->appVersion;
        self::assertSame($appVersion, $this->requirementsService->getAppVersion());

        // Test getAppVersionNumber()
        $appVersionNumber = $this->appVersionNumber;
        self::assertSame($appVersionNumber, $this->requirementsService->getAppVersionNumber());

        // Test getAppCopyright()
        $appCopyright = $this->appCopyright;
        self::assertSame($appCopyright, $this->requirementsService->getAppCopyright());

        // Test getAppLizenz()
        $appLizenz = $this->appLizenz;
        self::assertSame($appLizenz, $this->requirementsService->getAppLizenz());
    }
}
