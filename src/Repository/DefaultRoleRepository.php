<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use WebWMS\Entity\DefaultRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefaultRole>
 *
 * @method DefaultRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefaultRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefaultRole[]    findAll()
 * @method DefaultRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefaultRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefaultRole::class);
    }

    /**
     * @param DefaultRole $entity
     * @param bool $flush
     *
     * @return int
     */
    public function add(DefaultRole $entity, bool $flush = true): int
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity->getId();
    }
}
