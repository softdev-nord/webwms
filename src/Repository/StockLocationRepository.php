<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationRepository
 *
 * @method StockLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLocation[]    findAll()
 * @method StockLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLocationRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($registry, StockLocation::class);
    }

    public function findAllAjax(): JsonResponse
    {
        return new JsonResponse(self::findAll());
    }
}
