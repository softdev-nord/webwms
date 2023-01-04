<?php

declare(strict_types=1);

namespace WebWMS\Service\Configuration;

use WebWMS\Service\DataHandlers\Configuration\ConfigurationDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ConfigurationService
 */
class ConfigurationService
{
    public function __construct(
        private ConfigurationDataHandler $configurationDataHandler
    ) {
    }

    public function getAllConfigurations(): array
    {
        return $this->configurationDataHandler->getAllConfigurations();
    }
}
