<?php

declare(strict_types=1);

namespace WebWMS\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @package:    WebWMS\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ArrayExtension
 */
class ArrayExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('cast_to_array', [$this, 'castToArray']),
        ];
    }

    public function castToArray($stdClassObject): array
    {
        $response = [];
        foreach ($stdClassObject as $key => $value) {
            $response[] = [$key, $value];
        }

        return $response;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'array_extension';
    }
}
