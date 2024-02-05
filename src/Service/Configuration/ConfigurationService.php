<?php

declare(strict_types=1);

namespace WebWMS\Service\Configuration;

use Symfony\Component\HttpKernel\Kernel;
use WebWMS\Service\DataHandlers\Configuration\ConfigurationDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ConfigurationService
 */
class ConfigurationService
{
    public function __construct(
        private readonly ConfigurationDataHandler $configurationDataHandler,
        private readonly string $appVersion,
        private readonly string $appVersionNumber
    ) {
    }

    /**
     * @throws \Exception
     * @return array<string|int|mixed>
     */
    public function getAllConfigurations(): array
    {
        return $this->configurationDataHandler->getAllConfigurations();
    }

    /**
     * @return array<mixed>
     */
    public function prepareSystemInformation(): array
    {
        return [
            'php' => [
                'general' => $this->getPhpGeneralInformation(),
                'settings' => $this->getPhpImportantSetting(),
                'extensions' => $this->getPhpExtensions(),
            ],
            'sql' => [
                'driver' => $this->configurationDataHandler->getServerVersion()['driver'],
                'server_version' => $this->configurationDataHandler->getServerVersion()['serverVersion'],
                'database_name' => $this->configurationDataHandler->getServerVersion()['dbname'],
            ],
            'software' => $this->getSoftwareInformation(),
            'env' => $this->getEnvironmentInformation(),
            'server' => $this->getServerInformation(),
            'request' => $this->getRequestInformation(),
        ];
    }

    /**
     * @return array<string, object>
     */
    public function getEnvironmentInformation(): array
    {
        $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? __FILE__;

        try {
            $fileOwner = !function_exists('posix_getpwuid') ? null : @posix_getpwuid((int) @fileowner($scriptFile));
        } catch (\Throwable $e) {
            $fileOwner = null;
        }

        try {
            $fileGroup = !function_exists('posix_getgrgid') ? null : @posix_getgrgid((int) @filegroup($scriptFile));
        } catch (\Throwable $e) {
            $fileGroup = null;
        }

        return [
            'username' => getenv('USER_NAME'),
            'home_dir' => getenv('WORKING_DIRECTORY'),
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? null,
            'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? null,
            'script_owner' => $fileOwner['name'] ?? null,
            'script_group' => $fileGroup['name'] ?? null,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getServerInformation(): array
    {
        return [
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
            'signature' => isset($_SERVER['SERVER_SIGNATURE']) ? strip_tags(trim($_SERVER['SERVER_SIGNATURE'])) : null,
            'addr' => $_SERVER['SERVER_ADDR'] ?? null,
            'name' => $_SERVER['SERVER_NAME'] ?? null,
            'port' => $_SERVER['SERVER_PORT'] ?? null,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getRequestInformation(): array
    {
        return [
            'is_https' => $this->isHttpsRequest(),
            'is_ajax' => $this->isAjaxRequest(),
            'time' => $_SERVER['REQUEST_TIME'] ?? null,
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
            'scheme' => $_SERVER['REQUEST_SCHEME'] ?? null,
            'uri' => $_SERVER['REQUEST_URI'] ?? null,
            'referer' => $_SERVER['HTTP_REFERER'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];
    }

    /**
     * @return array<string, int<0, max>|string|false>
     */
    public function getPhpGeneralInformation(): array
    {
        return [
            'version' => PHP_VERSION,
            'version_id' => PHP_VERSION_ID,
            'version_major' => PHP_MAJOR_VERSION,
            'version_minor' => PHP_MINOR_VERSION,
            'version_release' => PHP_RELEASE_VERSION,
            'server_api' => PHP_SAPI,
            'binary_dir' => PHP_BINDIR,
            'php_ini_dir' => php_ini_loaded_file(),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public function getSoftwareInformation(): array
    {
        return [
            'webWms_version' => $this->appVersion,
            'webWms_version_number' => $this->appVersionNumber,
            'webWms_symfony_version' => Kernel::VERSION,
        ];
    }

    /**
     * @return array<int, array<string, int|string|false>>
     */
    public function getPhpImportantSetting(): array
    {
        return [
            [
                'setting' => 'max_execution_time',
                'raw_value' => $this->getPhpMaxExecutionTimeValue(),
                'int_value' => (int) $this->getPhpMaxExecutionTimeValue(),
            ],
            [
                'setting' => 'max_input_time',
                'raw_value' => ini_get('max_input_time'),
                'int_value' => (int) ini_get('max_input_time'),
            ],
            [
                'setting' => 'post_max_size',
                'raw_value' => $this->getPostMaxSizeValue(),
                'int_value' => $this->convertPhpValueToBytes($this->getPostMaxSizeValue()),
            ],
            [
                'setting' => 'upload_max_filesize',
                'raw_value' => ini_get('upload_max_filesize'),
                'int_value' => $this->convertPhpValueToBytes(ini_get('upload_max_filesize')),
            ],
            [
                'setting' => 'memory_limit',
                'raw_value' => ini_get('memory_limit'),
                'int_value' => $this->convertPhpValueToBytes(ini_get('memory_limit')),
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getPhpExtensions(): array
    {
        $extensionsLoaded = $this->getPhpExtensionsLoaded();
        $extensionsDefined = $this->getPhpExtensionsDefined();
        $extensionsOther = array_diff_key($extensionsLoaded, $extensionsDefined);
        ksort($extensionsOther);

        return [
            'other' => $extensionsOther,
            'loaded' => $extensionsLoaded,
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getPhpExtensionsLoaded(): array
    {
        $extensions = get_loaded_extensions();

        return array_combine($extensions, array_fill(0, count($extensions), true));
    }

    /**
     * @return array<mixed>
     */
    public function getPhpExtensionsDefined(): array
    {
        $extensionsResult = [];
        $extensionsCheck = $this->getPhpExtensionsDefinedCallbacks();
        foreach ($extensionsCheck as $extension => $callback) {
            $checkResult = $callback;
            $extensionsResult[$extension] = $checkResult;
        }

        return $extensionsResult;
    }

    /**
     * @return array<string, \Closure>
     */
    public function getPhpExtensionsDefinedCallbacks(): array
    {
        return [
            'mysqli' => function () {
                return function_exists('mysqli_connect');
            },
            'mysqlnd' => function () {
                return extension_loaded('mysqlnd');
            },
            'PDO' => function () {
                return class_exists('\PDO');
            },
            'curl' => function () {
                return function_exists('curl_init');
            },
            'xml' => function () {
                return function_exists('simplexml_load_string');
            },
            'stream_socket_enable_crypto' => function () {
                return function_exists('stream_socket_enable_crypto');
            },
            'fsocket' => function () {
                return function_exists('fsockopen');
            },
            'openssl' => function () {
                return function_exists('openssl_error_string');
            },
            'mbstring' => function () {
                return function_exists('mb_encode_numericentity');
            },
            'json' => function () {
                return function_exists('json_encode');
            },
            'iconv' => function () {
                return function_exists('iconv');
            },
            'soap' => function () {
                return class_exists('\SoapClient');
            },
            'imap' => function () {
                return function_exists('imap_open');
            },
            'zip' => function () {
                return class_exists('\ZipArchive');
            },
            'gd' => function () {
                return function_exists('imagejpeg');
            },
            'ldap' => function () {
                return function_exists('ldap_connect');
            },
            'ioncube' => function () {
                if (!function_exists('ioncube_loader_version')) {
                    return false;
                }

                $ioncubeMajorVersion = (int) @ioncube_loader_version();

                return $ioncubeMajorVersion >= 5;
            },
        ];
    }

    /**
     * @return bool
     */
    public function isHttpsRequest(): bool
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }

        return false;
    }

    public function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function getPostMaxSizeValue(): string
    {
        $postMaxSize = ini_get('client_max_body_size');
        if ($postMaxSize === false) {
            $postMaxSize = (string) ini_get('post_max_size');
        }

        return $postMaxSize;
    }

    /**
     * Converts PHP size value to byte value; e.g. 64K => 65536 Bytes
     *
     * @param false|string $phpValue
     *
     * @return int
     */
    public function convertPhpValueToBytes(false|string $phpValue): int
    {
        if (!$phpValue) {
            return 0;
        }

        $lastChar = strtoupper(substr(trim($phpValue), -1));

        return match ($lastChar) {
            'G' => (int) $phpValue * 1024 * 1024 * 1024,
            'M' => (int) $phpValue * 1024 * 1024,
            'K' => (int) $phpValue * 1024,
            default => (int) $phpValue,
        };
    }

    private function getPhpMaxExecutionTimeValue(): string
    {
        $maxExecutionTime = @ini_get('fastcgi_read_timeout'); // Nginx
        if (!$maxExecutionTime) {
            $maxExecutionTime = @ini_get('max_execution_time');
        }

        return $maxExecutionTime;
    }
}
