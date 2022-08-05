<?php

declare(strict_types=1);

namespace WebWMS\Bundles\DashboardBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @package:    WebWMS\Bundles\DashboardBundle
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        DashboardBundle
 */
class DashboardBundle extends Bundle
{
    public function getNiceName(): string
    {
        return 'webWMS Dashboard';
    }

    public function getDescription(): string
    {
        return 'Bundle für die webWMS Dashboards';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
