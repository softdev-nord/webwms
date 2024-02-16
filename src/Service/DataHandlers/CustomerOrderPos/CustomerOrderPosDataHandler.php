<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\CustomerOrderPos;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderPosEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerOrderPosEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosDataHandler
 */
class CustomerOrderPosDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(CustomerOrderPosEntity $customerOrderPosEntity): void
    {
        $this->entityManager->persist($customerOrderPosEntity);
        $this->entityManager->flush();
    }

    public function delete(CustomerOrderPosEntity $customerOrderPosEntity): void
    {
        $this->entityManager->remove($customerOrderPosEntity);
        $this->entityManager->flush();
    }

    public function getCustomerOrderPosById(int $customerOrderPosId): ?CustomerOrderPosEntity
    {
        return $this->entityManager
            ->getRepository(CustomerOrderPosEntity::class)
            ->findOneBy(['id' => $customerOrderPosId]);
    }

    public function getCustomerOrderPosByCustomerOrderId(int $customerOrderId): ?CustomerOrderPosEntity
    {
        return $this->entityManager
            ->getRepository(CustomerOrderPosEntity::class)
            ->find($customerOrderId);
    }

    public function getAllCustomerOrderPos(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('customer_orders_pos');

        $result = $queryBuilder->executeQuery();

        $results = $result->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function addCustomerOrderPos(CustomerOrderPosEntity $customerOrderPosEntity): void
    {
        $customerOrderPosEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($customerOrderPosEntity);
    }

    public function updateCustomerOrderPos(CustomerOrderPosEntity $customerOrderPosEntity): void
    {
        $customerOrderPosEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($customerOrderPosEntity);
    }

    public function deleteCustomerOrderPos(?CustomerOrderPosEntity $customerOrderPosEntity): void
    {
        if ($customerOrderPosEntity instanceof CustomerOrderPosEntity) {
            $this->delete($customerOrderPosEntity);
        }
    }
}
