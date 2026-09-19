<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Demo;

final readonly class DemoBootstrapResult
{
    public function __construct(
        public string $tenantId,
        public string $email,
        public ?string $generatedPassword,
        public bool $created,
    ) {
    }
}
