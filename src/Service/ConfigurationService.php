<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ConfigurationService
 */
class ConfigurationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }
}
