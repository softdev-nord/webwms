<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Module;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\Module;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ModuleDataHandler
 */
class ModuleDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(Module $module): void
    {
        $this->entityManager->persist($module);
        $this->entityManager->flush();
    }

    public function delete(Module $module): void
    {
        $this->entityManager->remove($module);
        $this->entityManager->flush();
    }

    public function getModuleById(int $moduleId): ?Module
    {
        return $this->entityManager
            ->getRepository(Module::class)
            ->findOneBy(['id' => $moduleId]);
    }

    public function getModuleByName(string $moduleName): Module
    {
        return $this->entityManager
            ->getRepository(Module::class)
            ->findOneBy(['name' => $moduleName]);
    }

    /**
     * @return Module[]
     */
    public function getAllModules(): array
    {
        return $this->entityManager
            ->getRepository(Module::class)
            ->findAll();
    }

    public function addModule(Module $module): void
    {
        $module->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($module);
    }

    public function updateModule(Module $module): void
    {
        $module->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($module);
    }

    public function deleteModule(Module $module): void
    {
        $this->delete($module);
    }
}
