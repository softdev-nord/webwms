<?php

declare(strict_types=1);

namespace WebWMS\Service;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        RequirementsService
 */
class RequirementsService
{
    public function __construct(
        private string $appName,
        private string $appVersion,
        private string $appVersionNumber,
        private string $appCopyright,
        private string $appLizenz
    ) {
    }

    /**
     * Checks the disk free space.
     */
    public function checkDiskFreeSpace(): string
    {
        $freeSpace = 0.000;
        if (function_exists('disk_free_space')) {
            $freeSpace = disk_free_space(__DIR__);
        }

        return $this->formatBytes($freeSpace);
    }

    public function formatBytes(false|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
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
