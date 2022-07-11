<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WebWMS\Service\RequirementsService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Requirements
 */
class Requirements extends AbstractController
{
    const APP_NAME = ' | webLVS Das webbasierte Lagerverwaltungssystem';
    const APP_VERSION = 'Enterprise Version';
    const APP_VERSION_NUMBER = '1.1.0';
    const APP_COPYRIGHT = '© 2019 Softdev-Nord | Rene Irrgang';
    const APP_LIZENZ = 'Demo Spedition<br>Demoweg 500<br>21698 Harsefeld';

    /** @var RequirementsService */
    private $requirementsService;

    private $appName;
    private $appVersion;
    private $appVersionNumber;
    private $appCopyright;
    private $appLizenz;

    public function __construct(
        string $appName,
        string $appVersion,
        string $appVersionNumber,
        string $appCopyright,
        string $appLizenz,
        RequirementsService $requirementsService
    ) {
        $this->appName = $appName;
        $this->appVersion = $appVersion;
        $this->appVersionNumber = $appVersionNumber;
        $this->appLizenz = $appLizenz;
        $this->appCopyright = $appCopyright;
        $this->requirementsService = $requirementsService;
    }

    public function coreInfo(): array
    {
        return [
            'appName' => $this->getAppName(),
            'appVersion' => $this->getAppVersion(),
            'appVersionNumber' => $this->getAppVersionNumber(),
            'appCopyright' => $this->getAppCopyright(),
            'appLizenz' => $this->getAppLizenz(),
        ];
    }

    /**
     * Checks the disk free space.
     *
     * @return bool|string
     */
    public function checkDiskFreeSpace()
    {
        if (function_exists('disk_free_space')) {
            // Prevent Warning: disk_free_space() [function.disk-free-space]: Value too large for defined data type
            $freeSpace = @disk_free_space(__DIR__);

            return $this->encodeSize($freeSpace);
        }

        return false;
    }

    /**
     * Encode byte size format.
     */
    public function encodeSize(float $bytes): string
    {
        $types = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++);

        return round($bytes, 2).' '.$types[$i];
    }

    public function __toString()
    {
        return $this->checkDiskFreeSpace();
    }

    /**
     * Checks the php version.
     *
     * @return bool|string
     */
    public function checkPhp()
    {
        if (strpos(PHP_VERSION, '-')) {
            return substr(PHP_VERSION, 0, strpos(PHP_VERSION, '-'));
        }

        return PHP_VERSION;
    }

    public function getServerVersion()
    {
        return $this->requirementsService->getServerVersion();
    }

    public function getAppName(): string
    {
        return $this->appName;
    }

    public function getAppVersion(): string
    {
        return $this->appVersion;
    }

    public function getAppVersionNumber(): string
    {
        return $this->appVersionNumber;
    }

    public function getAppCopyright(): string
    {
        return $this->appCopyright;
    }

    public function getAppLizenz(): string
    {
        return $this->appLizenz;
    }
}
