<?php

declare(strict_types=1);

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use WebWMS\Entity\Template;

/**
 * @package:    WebWMS\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        TemplateRepository
 */
class TemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function loadByTypeName($names)
    {
        $query = $this
            ->createQueryBuilder('t')
            ->select('t', 'tt')
            ->join('t.templateType', 'tt');
        // ->where('tt.name LIKE :name ')
        // ->setParameter('name', $name);
        // ->addOrderBy('u.endDate', 'ASC')
        // ->getQuery();

        $len = count($names);
        for ($i = 0; $i < $len; ++$i) {
            if (0 == $i) {
                $query->where('tt.name LIKE :name'.$i.' ');
            } else {
                $query->orWhere('tt.name LIKE :name'.$i.' ');
            }
            $query->setParameter('name'.$i, $names[$i]);
        }
        $query = $query->getQuery();

        $templates = null;
        try {
            $templates = $query->getResult();
        } catch (NoResultException $e) {
        }

        return $templates;
    }
}
