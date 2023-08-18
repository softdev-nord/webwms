<?php

declare(strict_types=1);

namespace WebWMS\Components\Environment;

/**
 * @package:    WebWMS\Components\Environment
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EnvironmentHelperTransformerInterface
 */
interface EnvironmentHelperTransformerInterface
{
    public static function transform(EnvironmentHelperTransformerData $data): void;
}
