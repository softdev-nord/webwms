<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use WebWMS\Entity\Feature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feature>
 *
 * @method Feature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feature[]    findAll()
 * @method Feature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feature::class);
    }

  /**
   * @param Feature $entity
   * @param bool $flush
   *
   * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
   *
   * @return int
   */
  public function add(Feature $entity, bool $flush = true): int
  {
      $this->_em->persist($entity);
      if ($flush) {
          $this->_em->flush();
      }

      return $entity->getId();
  }
}
