<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Service\DateTimeService;

class BaseDataHandler
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly DateTimeService $dateTimeService
    ) {
    }

    /**
     * @param class-string $className
     */
    protected function find(string $className, mixed $id): ?object
    {
        return $this->entityManager->getRepository($className)->find($id);
    }

    /**
     * @param class-string $className
     * @return object[]
     */
    protected function findAll(string $className): array
    {
        return $this->entityManager->getRepository($className)->findAll();
    }

    /**
     * @SuppressWarnings("unused")
     *
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     */
    protected function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        return $this;
    }

    /**
     * @SuppressWarnings("unused")
     *
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     */
    protected function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): object
    {
        return $this;
    }
}
