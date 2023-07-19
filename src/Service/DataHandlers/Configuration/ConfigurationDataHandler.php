<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Configuration;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\Configuration;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ConfigurationDataHandler
 */
class ConfigurationDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function save(Configuration $configuration): void
    {
        $this->entityManager->persist($configuration);
        $this->entityManager->flush();
    }

    public function delete(Configuration $configuration): void
    {
        $this->entityManager->remove($configuration);
        $this->entityManager->flush();
    }

    /**
     * @return Configuration|null Returns an array of Configuration objects
     */
    public function getConfigurationById(int $configurationId): ?Configuration
    {
        return $this->entityManager
            ->getRepository(Configuration::class)
            ->find($configurationId);
    }

    /**
     * @throws \Exception
     * @return array<string|int|mixed>
     */
    public function getAllConfigurations(): array
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('configuration');

        $stmt = $queryBuilder->executeQuery();
        return $stmt->fetchAllAssociative();
    }

    /**
     * @return array<mixed>
     */
    public function getServerVersion(): array
    {
        return $this->entityManager->getConnection()->getParams();
    }
}
