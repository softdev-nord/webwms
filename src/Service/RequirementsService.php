<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\ORM\EntityManagerInterface;

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
        private string $appLizenz,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __toString()
    {
        /*
         * @phpstan-ignore-next-line
         */
        return $this->checkDiskFreeSpace();
    }

    /**
     * Checks the disk free space.
     */
    public function checkDiskFreeSpace(): bool|string
    {
        if (function_exists('disk_free_space')) {
            $freeSpace = disk_free_space(__DIR__);

            return $this->formatBytes($freeSpace);
        }

        return false;
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

    public function getServerVersion(): string
    {
        /*
         * @phpstan-ignore-next-line
         */
        return $this->entityManager->getConnection()->getParams()['serverVersion'];
    }
}
