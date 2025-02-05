<?php

declare(strict_types=1);

namespace WebWMS\Exception;

/**
 * @package:    WebWMS\Exception
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ExceptionCollection
 */
class ExceptionCollection
{
    protected function getExpectedClass(): ?string
    {
        return WebWmsHttpException::class;
    }
}
