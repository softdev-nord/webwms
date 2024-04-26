<?php

declare(strict_types=1);

namespace WebWMS\Helper;

/**
 * @package:    WebWMS\Helper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 */
trait HydrateStaticTrait
{
    /**
     * @param array<mixed> $values
     */
    public static function hydrate(array $values): self
    {
        $dto = new self();

        foreach ($values as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value; /* @@phpstan-ignore-line */
            }
        }

        return $dto;
    }
}
