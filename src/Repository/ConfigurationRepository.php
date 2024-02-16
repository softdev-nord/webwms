<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\ConfigurationEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ConfigurationRepository
 *
 * @method ConfigurationEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfigurationEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfigurationEntity[]    findAll()
 * @method ConfigurationEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        parent::__construct($managerRegistry, ConfigurationEntity::class);
    }
}
