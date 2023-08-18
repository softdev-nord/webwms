<?php

declare(strict_types=1);

namespace WebWMS\Components\Environment;

/**
 * @package:    WebWMS\Components\Environment
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EnvironmentHelperTransformerData
 */
class EnvironmentHelperTransformerData
{
    /**
     * @param bool|float|int|string|null $value
     * @param bool|float|int|string|null $default
     */
    public function __construct(
//        private readonly string $key,
        private bool|float|int|string|null $value = null,
        private bool|float|int|string|null $default = null,
    ) {
    }

/*    public function getKey(): string
    {
        return $this->key;
    }*/

    /**
     * @return bool|float|int|string|null
     */
    public function getValue(): float|bool|int|string|null
    {
        return $this->value;
    }

    /**
     * @param float|bool|int|string|null $value
     */
    public function setValue(float|bool|int|string|null $value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool|float|int|string|null
     */
    public function getDefault(): float|bool|int|string|null
    {
        return $this->default;
    }

    /**
     * @param float|bool|int|string|null $default
     */
    public function setDefault(float|bool|int|string|null $default): void
    {
        $this->default = $default;
    }
}
