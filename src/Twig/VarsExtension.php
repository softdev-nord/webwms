<?php

declare(strict_types=1);

namespace WebWMS\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @package:    WebWMS\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        VarsExtension
 */
class VarsExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
        ];
    }

    public function jsonDecode($str)
    {
        return json_decode($str);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'vars_extension';
    }
}
