<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\OrderPos;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        OrderPosRepository
 *
 * @method OrderPos|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderPos|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderPos[]    findAll()
 * @method OrderPos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderPosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderPos::class);
    }
}
