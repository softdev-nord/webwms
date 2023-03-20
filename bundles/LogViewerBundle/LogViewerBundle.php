<?php

declare(strict_types=1);

namespace WebWMS\Bundles\LogViewerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @package:    WebWMS\Bundles\LogViewerBundle
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LogViewerBundle
 */
class LogViewerBundle extends Bundle
{
    public function getNiceName(): string
    {
        return 'WebWMS Protokoll-Betrachter';
    }

    public function getDescription(): string
    {
        return 'Bundle für die WebWMS Protokoll Übersicht';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
