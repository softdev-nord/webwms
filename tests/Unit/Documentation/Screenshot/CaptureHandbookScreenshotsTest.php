<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Documentation\Screenshot;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WebWMS\Documentation\Application\Screenshot\CaptureHandbookScreenshots;
use WebWMS\Documentation\Application\Screenshot\DemoReferenceProvider;
use WebWMS\Documentation\Application\Screenshot\ScreenshotRunner;
use WebWMS\Documentation\Application\Screenshot\ScreenshotScenario;
use WebWMS\Documentation\Application\Screenshot\ScreenshotScenarioProvider;

final class CaptureHandbookScreenshotsTest extends TestCase
{
    public function testDryRunFiltersByDocumentationViewAndReportsMissingFile(): void
    {
        $scenario = $this->scenario('product_form', 'product_form', 'product/form.png');
        $configuration = $this->createStub(ScreenshotScenarioProvider::class);
        $configuration->method('load')->willReturn([$scenario, $this->scenario('other', 'other', 'other.png')]);
        $references = $this->createMock(DemoReferenceProvider::class);
        $references->expects(self::once())->method('resolve')->with([])->willReturn([]);
        $runner = $this->createMock(ScreenshotRunner::class);
        $runner->expects(self::never())->method('capture');

        $results = (new CaptureHandbookScreenshots($configuration, $references, $runner, sys_get_temp_dir()))
            ->capture(['product_form'], null, 'de', false, true, false);

        self::assertCount(1, $results);
        self::assertSame('validated', $results[0]->status);
        self::assertSame('Screenshot file is missing and will be created.', $results[0]->message);
        self::assertStringEndsWith('/product/form.de.png', $results[0]->target);
    }

    public function testEnglishTargetUsesExplicitLocaleSuffix(): void
    {
        $configuration = $this->createStub(ScreenshotScenarioProvider::class);
        $configuration->method('load')->willReturn([$this->scenario('product_form', 'product_form', 'product/form.png')]);

        $results = (new CaptureHandbookScreenshots(
            $configuration,
            $this->createStub(DemoReferenceProvider::class),
            $this->createStub(ScreenshotRunner::class),
            sys_get_temp_dir(),
        ))->capture([], null, 'en', false, true, false);

        self::assertStringEndsWith('/product/form.en.png', $results[0]->target);
    }

    public function testUnsafeOutputPathIsRejected(): void
    {
        $configuration = $this->createStub(ScreenshotScenarioProvider::class);
        $configuration->method('load')->willReturn([$this->scenario('unsafe', 'unsafe', '../outside.png')]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsafe handbook screenshot path');

        (new CaptureHandbookScreenshots(
            $configuration,
            $this->createStub(DemoReferenceProvider::class),
            $this->createStub(ScreenshotRunner::class),
            sys_get_temp_dir(),
        ))->capture([], null, 'de', false, true, false);
    }

    private function scenario(string $key, string $documentationView, string $output): ScreenshotScenario
    {
        return new ScreenshotScenario(
            $key,
            $documentationView,
            'warehouse',
            'route',
            [],
            [],
            $output,
            'main',
            [],
            ['width' => 1440, 'height' => 1000],
            true,
            true,
        );
    }
}
