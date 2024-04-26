<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Service\RequirementsService;

/**
 * @package:    WebWMS\Tests\Unit\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        RequirementsServiceTest
 */
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

        self::assertEquals($expectedResult, $this->requirementsService->checkDiskFreeSpace());
    }

    public function testGetterMethods(): void
    {
        // Test getAppName()
        $appName = $this->appName;
        self::assertEquals($appName, $this->requirementsService->getAppName());

        // Test getAppVersion()
        $appVersion = $this->appVersion;
        self::assertEquals($appVersion, $this->requirementsService->getAppVersion());

        // Test getAppVersionNumber()
        $appVersionNumber = $this->appVersionNumber;
        self::assertEquals($appVersionNumber, $this->requirementsService->getAppVersionNumber());

        // Test getAppCopyright()
        $appCopyright = $this->appCopyright;
        self::assertEquals($appCopyright, $this->requirementsService->getAppCopyright());

        // Test getAppLizenz()
        $appLizenz = $this->appLizenz;
        self::assertEquals($appLizenz, $this->requirementsService->getAppLizenz());
    }
}
