<?php

declare(strict_types=1);

namespace WebWMS\Helper;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Helper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    trait: 'HydrateStaticTrait'
)]
trait HydrateStaticTrait
{
    public static function hydrate(array $values): self
    {
        $dto = new self();
        foreach ($values as $key => $value) {
            // Falls der Key ein Integer ist, direkt als Property-Name verwenden
            if (is_int($key)) {
                $property = (string) $key;
            } else {
                // Falls der Key ein String ist, in CamelCase umwandeln
                $property = lcfirst(str_replace('_', '', ucwords($key, '_')));
            }

            if (property_exists($dto, $property)) {
                $dto->$property = $value;
            }
        }

        return $dto;
    }
}
