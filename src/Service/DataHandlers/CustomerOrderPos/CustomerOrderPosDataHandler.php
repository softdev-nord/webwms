<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\CustomerOrderPos;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerOrderPos
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosDataHandler
 */
class CustomerOrderPosDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function save(CustomerOrderPos $customerOrderPos): void
    {
        $this->entityManager->persist($customerOrderPos);
        $this->entityManager->flush();
    }

    public function delete(CustomerOrderPos $customerOrderPos): void
    {
        $this->entityManager->remove($customerOrderPos);
        $this->entityManager->flush();
    }

    public function getCustomerOrderPosById(int $customerOrderPosId): ?CustomerOrderPos
    {
        return $this->entityManager
            ->getRepository(CustomerOrderPos::class)
            ->findOneBy(['id' => $customerOrderPosId]);
    }

    public function getCustomerOrderPosByCustomerOrderId(int $customerOrderId): ?CustomerOrderPos
    {
        return $this->entityManager
            ->getRepository(CustomerOrderPos::class)
            ->find($customerOrderId);
    }

    public function getAllCustomerOrderPos(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('customer_orders_pos');

        $stmt = $queryBuilder->executeQuery();

        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function addCustomerOrderPos(CustomerOrderPos $customerOrderPos): void
    {
        $customerOrderPos->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($customerOrderPos);
    }

    public function updateCustomerOrderPos(CustomerOrderPos $customerOrderPos): void
    {
        $customerOrderPos->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($customerOrderPos);
    }

    public function deleteCustomerOrderPos(?CustomerOrderPos $supplierOrderPos): void
    {
        if ($supplierOrderPos !== null) {
            $this->delete($supplierOrderPos);
        }
    }
}
