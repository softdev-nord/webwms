<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WebWMS\Services\ConfigurationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Configuration
 */
class Configuration extends AbstractController
{
    /** @var ConfigurationService */
    private $configurationService;

    public function __construct(
        ConfigurationService $configurationService
    ) {
        $this->configurationService = $configurationService;
    }
}