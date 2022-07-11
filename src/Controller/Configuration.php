<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WebWMS\Service\ConfigurationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Configuration
 */
class Configuration extends AbstractController
{
    public function __construct(
        private ConfigurationService $configurationService
    ) {
    }
}
