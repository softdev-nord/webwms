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
            // Prevent Warning: disk_free_space() [function.disk-free-space]: Value too large for defined data type
            $freeSpace = disk_free_space(__DIR__);

            /*
             * @phpstan-ignore-next-line
             */
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
        for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++) {
        }

        return round($bytes, 2) . ' ' . $types[$i];
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
