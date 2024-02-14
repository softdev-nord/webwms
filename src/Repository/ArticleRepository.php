<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\ArticleEntity;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleRepository
 *
 * @method ArticleEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleEntity[]    findAll()
 * @method ArticleEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        parent::__construct($managerRegistry, ArticleEntity::class);
    }
}
