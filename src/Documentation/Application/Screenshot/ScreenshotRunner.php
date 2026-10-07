<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application\Screenshot;

interface ScreenshotRunner
{
    /** @param array<string, string> $resolvedParameters */
    public function capture(ScreenshotScenario $scenario, array $resolvedParameters, string $locale, string $target, bool $headed): void;
}
