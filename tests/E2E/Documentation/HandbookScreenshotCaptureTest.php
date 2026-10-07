<?php

declare(strict_types=1);

namespace WebWMS\Tests\E2E\Documentation;

use Playwright\Symfony\Test\PlaywrightTestCase;

final class HandbookScreenshotCaptureTest extends PlaywrightTestCase
{
    public function testProductOverviewCanBeCapturedWithRealBrowser(): void
    {
        $email = (string) getenv('HANDBOOK_SCREENSHOT_EMAIL');
        $password = (string) getenv('HANDBOOK_SCREENSHOT_PASSWORD');
        if ($email === '' || $password === '') {
            self::markTestSkipped('Dedicated handbook screenshot credentials are not configured.');
        }

        $page = $this->visit('/v3/login');
        $page->fill('#email', $email);
        $page->fill('#password', $password);
        $page->click('button[type="submit"]');
        $this->visit('/v3/master-data/products');
        $this->waitForSelector('table');

        $target = sys_get_temp_dir() . '/webwms-handbook-product-overview.png';
        $this->screenshot($target);
        self::assertFileExists($target);
        self::assertGreaterThan(0, filesize($target));
        unlink($target);
    }
}
