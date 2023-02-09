<?php

declare(strict_types=1);

namespace WebWMS\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AccessCheckExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('has_role', [AccessCheckRuntime::class, 'hasRole']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_role', [AccessCheckRuntime::class, 'hasRole']),
        ];
    }
}
