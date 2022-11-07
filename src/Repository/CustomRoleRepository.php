<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use WebWMS\Entity\CustomRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomRole>
 *
 * @method CustomRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomRole[]    findAll()
 * @method CustomRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomRole::class);
    }

    /**
     * @param CustomRole $entity
     * @param bool $flush
     *
     * @return int
     */
    public function add(CustomRole $entity, bool $flush = true): int
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity->getId();
    }
}
