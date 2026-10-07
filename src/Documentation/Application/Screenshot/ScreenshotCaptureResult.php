<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application\Screenshot;

final readonly class ScreenshotCaptureResult
{
    public function __construct(
        public string $scenario,
        public string $status,
        public string $target,
        public ?string $message = null,
    ) {
    }
}
