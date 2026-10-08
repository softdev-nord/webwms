<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application\Screenshot;

use RuntimeException;

final readonly class CaptureHandbookScreenshots
{
    public function __construct(
        private ScreenshotScenarioProvider $configuration,
        private DemoReferenceProvider $references,
        private ScreenshotRunner $runner,
        private string $projectDir,
    ) {
    }

    /**
     * @param list<string> $views
     * @return list<ScreenshotCaptureResult>
     */
    public function capture(array $views, ?string $category, string $locale, bool $force, bool $dryRun, bool $headed): array
    {
        $scenarios = $this->configuration->load();
        $results = [];
        foreach ($scenarios as $scenario) {
            if ($views !== [] && !in_array($scenario->key, $views, true) && !in_array($scenario->documentationView, $views, true)) {
                continue;
            }
            if ($category !== null && $scenario->category !== $category) {
                continue;
            }

            $target = $this->target($scenario->output, $locale);
            if (is_file($target) && !$force) {
                $results[] = new ScreenshotCaptureResult($scenario->key, 'skipped', $target, 'Target already exists.');
                continue;
            }

            try {
                $parameters = $this->references->resolve([...$scenario->routeParameters, ...$scenario->query]);
                if (!$dryRun) {
                    $directory = dirname($target);
                    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                        throw new RuntimeException(sprintf('Screenshot directory "%s" cannot be created.', $directory));
                    }
                    $temporary = $target . '.tmp.png';
                    $this->runner->capture($scenario, $parameters, $locale, $temporary, $headed);
                    if (!is_file($temporary) || filesize($temporary) === 0) {
                        throw new RuntimeException('Playwright did not create a screenshot.');
                    }
                    if (!rename($temporary, $target)) {
                        throw new RuntimeException('The screenshot cannot be moved to its final target.');
                    }
                }
                $message = $dryRun && !is_file($target) ? 'Screenshot file is missing and will be created.' : null;
                $results[] = new ScreenshotCaptureResult($scenario->key, $dryRun ? 'validated' : 'created', $target, $message);
            } catch (ScreenshotRunnerUnavailable $exception) {
                throw $exception;
            } catch (\Throwable $exception) {
                $results[] = new ScreenshotCaptureResult($scenario->key, 'failed', $target, $exception->getMessage());
            }
        }

        if ($results === []) {
            throw new RuntimeException('No handbook screenshot scenario matches the selected filters.');
        }

        return $results;
    }

    private function target(string $relativeOutput, string $locale): string
    {
        if (preg_match('#^[a-z0-9][a-z0-9._/-]*\.png$#i', $relativeOutput) !== 1 || str_contains($relativeOutput, '..')) {
            throw new RuntimeException(sprintf('Unsafe handbook screenshot path "%s".', $relativeOutput));
        }
        $suffix = $locale === 'en' ? '.en.png' : '.png';
        $relativeOutput = substr($relativeOutput, 0, -4) . $suffix;
        $base = $this->projectDir . '/public/assets/images/handbook/screenshots';
        $target = $base . '/' . $relativeOutput;
        if (!str_starts_with($target, $base . '/')) {
            throw new RuntimeException('The screenshot target leaves the handbook asset directory.');
        }

        return $target;
    }
}
