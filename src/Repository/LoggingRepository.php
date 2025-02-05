<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\Logging;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Repository',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'LoggingRepository'
)]
class LoggingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Logging::class);
    }
}
