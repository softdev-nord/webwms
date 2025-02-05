<?php

declare(strict_types=1);

namespace WebWMS\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Twig',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'VarsExtension'
)]
class VarsExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('json_decode', $this->jsonDecode(...)),
        ];
    }

    public function jsonDecode(string $str): mixed
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
