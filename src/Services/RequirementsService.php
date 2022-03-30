<?php

declare(strict_types=1);

namespace WebWMS\Services;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @package:    WebWMS\Services
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        RequirementsService
 */
class RequirementsService
{
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getServerVersion()
    {
        return $this->doctrine->getConnection()->getWrappedConnection()->getServerVersion();
    }
}