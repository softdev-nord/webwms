<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RequirementsController extends AbstractController
{
    const APP_NAME = " | webLVS Das webbasierte Lagerverwaltungssystem";
    const APP_VERSION ="Enterprise Version";
    const APP_VERSION_NUMBER ="1.1.0";
    const APP_COPYRIGHT ="© 2019 Softdev-Nord | Rene Irrgang";
    const APP_LIZENZ ="Demo Spedition<br>Demoweg 500<br>21698 Harsefeld";

    public function coreInfo()
    {
        $coreInfo =
            [
                'appName' => self::APP_NAME,
                'appVersion' => self::APP_VERSION,
                'appVersionNumber' => self::APP_VERSION_NUMBER,
                'appCopyright' => self::APP_COPYRIGHT,
                'appLizenz' => self::APP_LIZENZ
            ];

        return $coreInfo;
    }

    /**
     * Checks the disk free space
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
     * Encode byte size format
     * @param float $bytes
     * @return string
     */
    public function encodeSize($bytes)
    {
        $types = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++);

        return round($bytes, 2) . ' ' . $types[$i];
    }

    public function __toString()
    {
        return $this->checkDiskFreeSpace();
    }

    /**
     * Checks the php version
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
        $mySqlVersion = $this->getDoctrine()->getConnection()->getWrappedConnection()->getServerVersion();

        return $mySqlVersion;
    }
}
