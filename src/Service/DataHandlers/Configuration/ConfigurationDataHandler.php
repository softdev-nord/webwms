<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Configuration;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use WebWMS\Entity\ConfigurationEntity;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerOrderEntity
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

    public function save(ConfigurationEntity $configurationEntity): void
    {
        $this->entityManager->persist($configurationEntity);
        $this->entityManager->flush();
    }

    public function delete(ConfigurationEntity $configurationEntity): void
    {
        $this->entityManager->remove($configurationEntity);
        $this->entityManager->flush();
    }

    /**
     * @return ConfigurationEntity|null Returns an array of ConfigurationController objects
     */
    public function getConfigurationById(int $configurationId): ?ConfigurationEntity
    {
        return $this->entityManager
            ->getRepository(ConfigurationEntity::class)
            ->find($configurationId);
    }

    /**
     * @throws Exception
     * @return array<string|int|mixed>
     */
    public function getAllConfigurations(): array
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('configuration');

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return array<mixed>
     */
    public function getServerVersion(): array
    {
        return $this->entityManager->getConnection()->getParams();
    }
}
