<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application\Screenshot;

final readonly class ScreenshotScenario
{
    /**
     * @param array<string, string> $routeParameters
     * @param array<string, string> $query
     * @param list<string> $masks
     * @param array{width: int, height: int} $viewport
     */
    public function __construct(
        public string $key,
        public string $documentationView,
        public string $category,
        public string $route,
        public array $routeParameters,
        public array $query,
        public string $output,
        public string $waitFor,
        public array $masks,
        public array $viewport,
        public bool $fullPage,
    ) {
    }
}
