<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use WebWMS\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Role>
 *
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param Role $entity
     * @param bool $flush
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return int
     */
    public function add(Role $entity, bool $flush = true): int
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity->getId();
    }

    public function getAllRoles(): array
    {
        $queryBuilder = $this->_em->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('role', 'r');

        $stmt = $queryBuilder->executeQuery();
        return $stmt->fetchAllAssociative();
    }
}
