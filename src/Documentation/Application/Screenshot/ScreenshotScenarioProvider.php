<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application\Screenshot;

interface ScreenshotScenarioProvider
{
    /** @return list<ScreenshotScenario> */
    public function load(): array;
}
