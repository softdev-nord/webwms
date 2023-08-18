<?php

declare(strict_types=1);

namespace WebWMS\Exception;

/**
 * @package:    WebWMS\Exception
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        WebWmsException
 */
interface WebWmsException extends \Throwable
{
    public function getErrorCode(): string;

    /**
     * @return array<string|int, mixed|null>
     */
    public function getParameters(): array;
}
