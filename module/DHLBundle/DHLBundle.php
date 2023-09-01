<?php

namespace WebWMS\Bundles\DHLBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DHLBundle extends Bundle
{
    public function getNiceName(): string
    {
        return 'WebWMS DHL Modul';
    }

    public function getDescription(): string
    {
        return 'Modul für DHL';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
