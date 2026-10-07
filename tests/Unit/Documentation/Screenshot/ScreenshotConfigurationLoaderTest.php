<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Documentation\Screenshot;

use PHPUnit\Framework\TestCase;
use WebWMS\Documentation\Infrastructure\Screenshot\ScreenshotConfigurationLoader;

final class ScreenshotConfigurationLoaderTest extends TestCase
{
    public function testConfigurationContainsRequiredProductAndTopologyScenarios(): void
    {
        $scenarios = (new ScreenshotConfigurationLoader(dirname(__DIR__, 4)))->load();
        $keys = array_map(static fn ($scenario): string => $scenario->key, $scenarios);

        self::assertContains('product_overview', $keys);
        self::assertContains('product_form', $keys);
        self::assertContains('product_detail', $keys);
        self::assertContains('topology_overview', $keys);
        self::assertContains('topology_form', $keys);
        self::assertContains('topology_generator', $keys);
        self::assertContains('topology_import', $keys);
        self::assertContains('warehouse_occupancy_block', $keys);
        self::assertContains('warehouse_occupancy_rack', $keys);
        self::assertContains('warehouse_occupancy_flow', $keys);

        foreach ($scenarios as $scenario) {
            self::assertMatchesRegularExpression('#^[a-z0-9][a-z0-9._/-]*\.png$#i', $scenario->output);
            self::assertStringNotContainsString('..', $scenario->output);
            self::assertGreaterThanOrEqual(1024, $scenario->viewport['width']);
            self::assertNotSame('', $scenario->waitFor);
        }
    }
}
