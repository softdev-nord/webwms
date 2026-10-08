<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Infrastructure\Screenshot;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;
use WebWMS\Documentation\Application\Screenshot\ScreenshotScenario;
use WebWMS\Documentation\Application\Screenshot\ScreenshotScenarioProvider;

final readonly class ScreenshotConfigurationLoader implements ScreenshotScenarioProvider
{
    public function __construct(private string $projectDir)
    {
    }

    /** @return list<ScreenshotScenario> */
    public function load(): array
    {
        $configuration = Yaml::parseFile($this->projectDir . '/config/handbook/screenshots.yaml');
        if (!is_array($configuration) || !is_array($configuration['scenarios'] ?? null)) {
            throw new RuntimeException('The handbook screenshot configuration is invalid.');
        }
        $defaults = is_array($configuration['defaults'] ?? null) ? $configuration['defaults'] : [];
        $scenarios = [];
        foreach ($configuration['scenarios'] as $key => $values) {
            if (!is_string($key) || !is_array($values)) {
                throw new RuntimeException('A handbook screenshot scenario is invalid.');
            }
            $scenario = array_replace($defaults, $values);
            $viewport = $scenario['viewport'] ?? null;
            if (!is_array($viewport) || !is_int($viewport['width'] ?? null) || !is_int($viewport['height'] ?? null)) {
                throw new RuntimeException(sprintf('Scenario "%s" has no valid viewport.', $key));
            }
            $scenarios[] = new ScreenshotScenario(
                $key,
                $this->string($scenario, 'documentation_view', $key),
                $this->string($scenario, 'category', $key),
                $this->string($scenario, 'route', $key),
                $this->map($scenario['route_parameters'] ?? []),
                $this->map($scenario['query'] ?? []),
                $this->string($scenario, 'output', $key),
                $this->string($scenario, 'wait_for', $key),
                $this->list($scenario['masks'] ?? []),
                ['width' => $viewport['width'], 'height' => $viewport['height']],
                (bool) ($scenario['full_page'] ?? true),
                (bool) ($scenario['authenticated'] ?? true),
            );
        }

        $this->validateHandbookCoverage($scenarios);

        return $scenarios;
    }

    /** @param list<ScreenshotScenario> $scenarios */
    private function validateHandbookCoverage(array $scenarios): void
    {
        $german = $this->documentationViews('de');
        $english = $this->documentationViews('en');
        if ($german !== $english) {
            throw new RuntimeException('The German and English handbook view identifiers are inconsistent.');
        }

        $configured = array_values(array_unique(array_map(
            static fn (ScreenshotScenario $scenario): string => $scenario->documentationView,
            $scenarios,
        )));
        sort($configured);
        $missing = array_values(array_diff($german, $configured));
        $unknown = array_values(array_diff($configured, $german));
        if ($missing !== [] || $unknown !== []) {
            throw new RuntimeException(sprintf(
                'Handbook screenshot coverage is inconsistent. Missing views: %s. Unknown views: %s.',
                $missing === [] ? 'none' : implode(', ', $missing),
                $unknown === [] ? 'none' : implode(', ', $unknown),
            ));
        }
    }

    /** @return list<string> */
    private function documentationViews(string $locale): array
    {
        $catalogue = Yaml::parseFile($this->projectDir . '/translations/handbook.' . $locale . '.yaml');
        if (!is_array($catalogue) || !is_array($catalogue['chapter'] ?? null)) {
            throw new RuntimeException(sprintf('The %s handbook catalogue is invalid.', $locale));
        }

        $views = [];
        foreach ($catalogue['chapter'] as $chapter) {
            if (!is_array($chapter) || !is_array($chapter['views'] ?? null)) {
                continue;
            }
            foreach (array_keys($chapter['views']) as $view) {
                if (!is_string($view) || $view === '') {
                    throw new RuntimeException(sprintf('The %s handbook contains an invalid view identifier.', $locale));
                }
                $views[] = $view;
            }
        }
        sort($views);

        return $views;
    }

    /** @param array<string, mixed> $values */
    private function string(array $values, string $name, string $fallback = ''): string
    {
        $value = $values[$name] ?? $fallback;
        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException(sprintf('Screenshot configuration value "%s" is missing.', $name));
        }

        return $value;
    }

    /** @return array<string, string> */
    private function map(mixed $values): array
    {
        if (!is_array($values)) {
            throw new RuntimeException('Screenshot route and query parameters must be maps.');
        }
        $result = [];
        foreach ($values as $key => $value) {
            if (!is_string($key) || (!is_string($value) && !is_int($value))) {
                throw new RuntimeException('Screenshot route and query parameter values must be scalar strings.');
            }
            $result[$key] = (string) $value;
        }

        return $result;
    }

    /** @return list<string> */
    private function list(mixed $values): array
    {
        if (!is_array($values) || array_filter($values, static fn (mixed $value): bool => !is_string($value)) !== []) {
            throw new RuntimeException('Screenshot masks must be a list of selectors.');
        }

        return array_values($values);
    }
}
