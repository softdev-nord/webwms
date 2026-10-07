<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application\Screenshot;

interface DemoReferenceProvider
{
    /** @param array<string, string> $values @return array<string, string> */
    public function resolve(array $values): array;
}
