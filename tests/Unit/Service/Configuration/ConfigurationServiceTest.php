<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Configuration;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebWMS\Service\Configuration\ConfigurationService;
use WebWMS\Service\DataHandlers\Configuration\ConfigurationDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\ConfigurationController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ConfigurationServiceTest
 */
#[CoversClass(ConfigurationService::class)]
final class ConfigurationServiceTest extends TestCase
{
    private ConfigurationService $configurationService;

    private MockObject $mockObject;

    private string $appVersion = 'Enterprise Version';

    private string $appVersionNumber = '2.0.0';

    #[Override]
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
            ->expects(self::once())
            ->method('getAllConfigurations')
            ->willReturn($configurations);

        $result = $this->configurationService->getAllConfigurations();

        self::assertSame($configurations, $result);
    }

    //    public function testPrepareSystemInformation(): void
    //    {
    //        $systemInformation = $this->configurationService->prepareSystemInformation();
    //        //dd($systemInformation);
    //
    //        // Assert structure and content of the returned array
    //        self::assertArrayHasKey('php', $systemInformation);
    // #        self::assertArrayHasKey('sql', $systemInformation);
    //        self::assertArrayHasKey('software', $systemInformation);
    //        self::assertArrayHasKey('env', $systemInformation);
    //        self::assertArrayHasKey('server', $systemInformation);
    //        self::assertArrayHasKey('request', $systemInformation);
    //
    //        // Assert expected values within the array
    //        self::assertSame('Enterprise Version', $systemInformation['software']['webWms_version']);
    //        self::assertSame('2.0.0', $systemInformation['software']['webWms_version_number']);
    //        self::assertSame(Kernel::VERSION, $systemInformation['software']['webWms_symfony_version']);
    //    }

    public function testItGetsEnvironmentInformation(): void
    {
        // Set up expected environment variables
        $expectedUsername = 'test_user';
        $expectedHomeDir = '/home/test_user';
        $expectedDocumentRoot = '/var/www/html';
        $expectedScriptFilename = '/var/www/html/index.php';
        $expectedScriptOwner = 'root';
        $expectedScriptGroup = 'root';

        // Mock environment variables and server variables
        putenv('USER_NAME=' . $expectedUsername);
        putenv('WORKING_DIRECTORY=' . $expectedHomeDir);
        $_SERVER['DOCUMENT_ROOT'] = $expectedDocumentRoot;
        $_SERVER['SCRIPT_FILENAME'] = $expectedScriptFilename;

        // Call the method
        $result = $this->configurationService->getEnvironmentInformation();

        // Assert the results
        self::assertEquals($expectedUsername, $result['username']);
        self::assertEquals($expectedHomeDir, $result['home_dir']);
        self::assertEquals($expectedDocumentRoot, $result['document_root']);
        self::assertEquals($expectedScriptFilename, $result['script_filename']);
        self::assertEquals($expectedScriptOwner, $result['script_owner']);
        self::assertEquals($expectedScriptGroup, $result['script_group']);
    }

    //    public function testItHandlesThrowableInFileOwner(): void
    //    {
    //        // Mock posix_getpwuid to throw an exception
    //        $this->expectException(\Throwable::class);
    //        // Call the method
    //        $result = $this->configurationService->getEnvironmentInformation();
    //
    //        // Assert that fileOwner is null
    //        $this->assertNull($result['script_owner']);
    //    }
    //
    //    public function testItHandlesThrowableInFileGroup(): void
    //    {
    //        // Mock posix_getgrgid to throw an exception
    //        $this->expectException(\Throwable::class);
    //
    //        // Call the method
    //        $result = $this->configurationService->getEnvironmentInformation();
    //
    //        // Assert that fileGroup is null
    //        $this->assertNull($result['script_group']);
    //    }

    public function testItGetsServerInformation(): void
    {
        // Set up expected server variables
        $expectedSoftware = 'Apache/2.4.56';
        $expectedSignature = 'Server signature content';
        $expectedAddr = '127.0.0.1';
        $expectedName = 'localhost';
        $expectedPort = '80';

        // Mock server variables
        $_SERVER['SERVER_SOFTWARE'] = $expectedSoftware;
        $_SERVER['SERVER_SIGNATURE'] = '<p>Server signature content</p>'; // Test HTML stripping
        $_SERVER['SERVER_ADDR'] = $expectedAddr;
        $_SERVER['SERVER_NAME'] = $expectedName;
        $_SERVER['SERVER_PORT'] = $expectedPort;

        // Call the method
        $result = $this->configurationService->getServerInformation();

        // Assert the results
        self::assertEquals($expectedSoftware, $result['software']);
        self::assertEquals($expectedSignature, $result['signature']);
        self::assertEquals($expectedAddr, $result['addr']);
        self::assertEquals($expectedName, $result['name']);
        self::assertEquals($expectedPort, $result['port']);
    }

    //    /**
    //     * @covers \WebWMS\Service\ConfigurationController\ConfigurationService::getRequestInformation
    //     */
    //    public function testItGetsRequestInformation(): void
    //    {
    //        // Create a mock request object
    //        $request = new Request();
    //
    //        // Set up expected request information
    //        $request->headers->set('X-Requested-With', 'XMLHttpRequest'); // Simulate AJAX request
    //        $request->setMethod('POST');
    //        $request->headers->set('Referer', 'https://example.com');
    //        $request->headers->set('UserController-Agent', 'Mozilla/5.0 ...');
    //
    //        $request = $this->configurationService->getRequestInformation();
    //
    //        // Assert the results
    //        self::assertFalse($request['is_https']);
    //        self::assertFalse($request['is_ajax']);
    //        self::assertSame('POST', $request['method']);
    //        self::assertSame('https', $request['scheme']);
    //        self::assertSame('/some/path', $request['uri']);
    //        self::assertSame('https://example.com', $request['referer']);
    //        self::assertSame('Mozilla/5.0 ...', $request['user_agent']);
    //    }

    public function testItGetsPhpGeneralInformation(): void
    {
        // Define expected values based on your current PHP environment
        $expectedVersion = '8.3.4';
        $expectedVersionId = 80304;
        $expectedMajorVersion = 8;
        $expectedMinorVersion = 3;
        $expectedReleaseVersion = 4;
        $expectedServerApi = 'cli';
        $expectedBinaryDir = '/usr/local/bin';

        // Call the method
        $result = $this->configurationService->getPhpGeneralInformation();

        // Assert the results
        self::assertEquals($expectedVersion, $result['version']);
        self::assertEquals($expectedVersionId, $result['version_id']);
        self::assertEquals($expectedMajorVersion, $result['version_major']);
        self::assertEquals($expectedMinorVersion, $result['version_minor']);
        self::assertEquals($expectedReleaseVersion, $result['version_release']);
        self::assertEquals($expectedServerApi, $result['server_api']);
        self::assertEquals($expectedBinaryDir, $result['binary_dir']);
    }

    public function testItGetsSoftwareInformation(): void
    {
        // Call the method
        $result = $this->configurationService->getSoftwareInformation();

        // Assert the results
        self::assertEquals('Enterprise Version', $result['webWms_version']);
        self::assertEquals('2.0.0', $result['webWms_version_number']);
        self::assertEquals('7.0.6', $result['webWms_symfony_version']);
    }

    public function testItGetsPhpImportantSettings(): void
    {
        // Mock ini_get for specific settings
        ini_set('max_execution_time', 30);
        ini_set('max_input_time', -1);
        ini_set('post_max_size', '8M');
        ini_set('upload_max_filesize', '2M');
        ini_set('memory_limit', '128M');

        // Call the method
        $result = $this->configurationService->getPhpImportantSetting();

        // Assert the results
        self::assertEquals(
            [
                ['setting' => 'max_execution_time', 'raw_value' => '30', 'int_value' => 30],
                ['setting' => 'max_input_time', 'raw_value' => '-1', 'int_value' => -1],
                ['setting' => 'post_max_size', 'raw_value' => '8M', 'int_value' => 8 * 1024 * 1024],
                ['setting' => 'upload_max_filesize', 'raw_value' => '2M', 'int_value' => 2 * 1024 * 1024],
                ['setting' => 'memory_limit', 'raw_value' => '128M', 'int_value' => 128 * 1024 * 1024],
            ],
            $result
        );
    }

    //    public function testItGetsPhpExtensions(): void
    //    {
    //        $extensions = [
    //            'other' => [
    //                'Core' => true,
    //                'Phar' => true,
    //                'Reflection' => true,
    //                'SPL' => true,
    //                'SimpleXML' => true,
    //                'Zend OPcache' => true,
    //                'apcu' => true,
    //                'ctype' => true,
    //                'date' => true,
    //                'dom' => true,
    //                'exif' => true,
    //                'fileinfo' => true,
    //                'filter' => true,
    //                'gettext' => true,
    //                'hash' => true,
    //                'intl' => true,
    //                'libxml' => true,
    //                'pcov' => true,
    //                'pcre' => true,
    //                'pdo_mysql' => true,
    //                'pdo_sqlite' => true,
    //                'posix' => true,
    //                'random' => true,
    //                'readline' => true,
    //                'redis' => true,
    //                'session' => true,
    //                'sodium' => true,
    //                'sqlite3' => true,
    //                'standard' => true,
    //                'tokenizer' => true,
    //                'xdebug' => true,
    //                'xmlreader' => true,
    //                'xmlwriter' => true,
    //                'zlib' => true,
    //            ],
    //            'loaded' => [
    //                'Core' => true,
    //                'date' => true,
    //                'libxml' => true,
    //                'openssl' => true,
    //                'pcre' => true,
    //                'sqlite3' => true,
    //                'zlib' => true,
    //                'ctype' => true,
    //                'curl' => true,
    //                'dom' => true,
    //                'fileinfo' => true,
    //                'filter' => true,
    //                'hash' => true,
    //                'iconv' => true,
    //                'json' => true,
    //                'mbstring' => true,
    //                'SPL' => true,
    //                'session' => true,
    //                'PDO' => true,
    //                'pdo_sqlite' => true,
    //                'standard' => true,
    //                'posix' => true,
    //                'random' => true,
    //                'readline' => true,
    //                'Reflection' => true,
    //                'Phar' => true,
    //                'SimpleXML' => true,
    //                'tokenizer' => true,
    //                'xml' => true,
    //                'xmlreader' => true,
    //                'xmlwriter' => true,
    //                'mysqlnd' => true,
    //                'apcu' => true,
    //                'exif' => true,
    //                'gd' => true,
    //                'gettext' => true,
    //                'intl' => true,
    //                'mysqli' => true,
    //                'pcov' => true,
    //                'pdo_mysql' => true,
    //                'redis' => true,
    //                'sodium' => true,
    //                'zip' => true,
    //                'Zend OPcache' => true,
    //                'xdebug' => true,
    //            ],
    //        ];
    //
    //        // Call the method
    //        $result = $this->configurationService->getPhpExtensions();
    //
    //        // Assert the results
    //        self::assertEquals(
    //            [
    //                'other' => [
    //                    'Core' => true,
    //                    'Phar' => true,
    //                    'Reflection' => true,
    //                    'SPL' => true,
    //                    'SimpleXML' => true,
    //                    'Zend OPcache' => true,
    //                    'apcu' => true,
    //                    'ctype' => true,
    //                    'date' => true,
    //                    'dom' => true,
    //                    'exif' => true,
    //                    'fileinfo' => true,
    //                    'filter' => true,
    //                    'gettext' => true,
    //                    'hash' => true,
    //                    'intl' => true,
    //                    'libxml' => true,
    //                    'pcov' => true,
    //                    'pcre' => true,
    //                    'pdo_mysql' => true,
    //                    'pdo_sqlite' => true,
    //                    'posix' => true,
    //                    'random' => true,
    //                    'readline' => true,
    //                    'redis' => true,
    //                    'session' => true,
    //                    'sodium' => true,
    //                    'sqlite3' => true,
    //                    'standard' => true,
    //                    'tokenizer' => true,
    //                    'xdebug' => true,
    //                    'xmlreader' => true,
    //                    'xmlwriter' => true,
    //                    'zlib' => true,
    //                ],
    //                'loaded' => [
    //                    'Core' => true,
    //                    'date' => true,
    //                    'libxml' => true,
    //                    'openssl' => true,
    //                    'pcre' => true,
    //                    'sqlite3' => true,
    //                    'zlib' => true,
    //                    'ctype' => true,
    //                    'curl' => true,
    //                    'dom' => true,
    //                    'fileinfo' => true,
    //                    'filter' => true,
    //                    'hash' => true,
    //                    'iconv' => true,
    //                    'json' => true,
    //                    'mbstring' => true,
    //                    'SPL' => true,
    //                    'session' => true,
    //                    'PDO' => true,
    //                    'pdo_sqlite' => true,
    //                    'standard' => true,
    //                    'posix' => true,
    //                    'random' => true,
    //                    'readline' => true,
    //                    'Reflection' => true,
    //                    'Phar' => true,
    //                    'SimpleXML' => true,
    //                    'tokenizer' => true,
    //                    'xml' => true,
    //                    'xmlreader' => true,
    //                    'xmlwriter' => true,
    //                    'mysqlnd' => true,
    //                    'apcu' => true,
    //                    'exif' => true,
    //                    'gd' => true,
    //                    'gettext' => true,
    //                    'intl' => true,
    //                    'mysqli' => true,
    //                    'pcov' => true,
    //                    'pdo_mysql' => true,
    //                    'redis' => true,
    //                    'sodium' => true,
    //                    'zip' => true,
    //                    'Zend OPcache' => true,
    //                    'xdebug' => true,
    //                ],
    //            ],
    //            $result
    //        );
    //    }

    public function testItDetectsHttpsRequest(): void
    {
        $_SERVER['HTTPS'] = 'on';
        self::assertTrue($this->configurationService->isHttpsRequest());

        $_SERVER['HTTPS'] = 'off';
        $_SERVER['HTTP_X_FORWARDED_SSL'] = 'on';
        self::assertTrue($this->configurationService->isHttpsRequest());

        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        self::assertTrue($this->configurationService->isHttpsRequest());

        unset($_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_SSL'], $_SERVER['HTTP_X_FORWARDED_PROTO']);
        self::assertFalse($this->configurationService->isHttpsRequest());
    }

    public function testItDetectsAjaxRequest(): void
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
        self::assertTrue($this->configurationService->isAjaxRequest());

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        self::assertFalse($this->configurationService->isAjaxRequest());
    }

    public function testItConvertsPhpValueToBytes(): void
    {
        self::assertSame(65536, $this->configurationService->convertPhpValueToBytes('64K'));
        self::assertSame(1048576, $this->configurationService->convertPhpValueToBytes('1M'));
        self::assertSame(1073741824, $this->configurationService->convertPhpValueToBytes('1G'));
        self::assertSame(12345, $this->configurationService->convertPhpValueToBytes('12345'));
        self::assertSame(0, $this->configurationService->convertPhpValueToBytes(false));
    }
}
