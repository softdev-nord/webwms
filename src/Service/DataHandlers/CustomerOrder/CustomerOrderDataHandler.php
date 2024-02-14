<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\CustomerOrder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrderEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderDataHandler
 */
class CustomerOrderDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(CustomerOrderEntity $customerOrderEntity): void
    {
        $this->entityManager->persist($customerOrderEntity);
        $this->entityManager->flush();
    }

    public function delete(CustomerOrderEntity $customerOrderEntity): void
    {
        $this->entityManager->remove($customerOrderEntity);
        $this->entityManager->flush();
    }

    /**
     * @return CustomerOrderEntity|null Returns an array of CustomerEntity order objects
     */
    public function getCustomerOrderById(int $id): ?CustomerOrderEntity
    {
        return $this->entityManager
            ->getRepository(CustomerOrderEntity::class)
            ->findOneBy(['id' => $id]);
    }

    public function getAllCustomerOrders(): JsonResponse
    {
        $connection = $this->entityManager->getConnection();

        $queryBuilder = $connection->createQueryBuilder();

        $queryBuilder
            ->select(
                'co.customer_order_id',
                'co.customer_order_nr',
                'co.customer_order_reference',
                'cu.customer_nr',
                'cu.customer_name',
                'co.customer_order_date',
                'co.customer_order_creation_date',
                'usr.username',
                'co.created_at',
                'co.updated_at'
            )
            ->from('customer_orders', 'co')
            ->innerJoin('co', 'customer_orders_pos', 'cop', 'cop.customer_order_id = co.customer_order_id')
            ->innerJoin('co', 'customer', 'cu', 'cu.customer_id = co.customer_id')
            ->innerJoin('co', 'user', 'usr', 'usr.id = co.usr_id')
            ->groupBy('cop.customer_order_id');

        $stmt = $queryBuilder->executeQuery();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
    }

    public function getAllCustomerOrderPos(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select('cop.customer_order_id,
                    tph.order_nr as customer_order_nr,
                    cop.article_nr,
                    cop.article_name,
                    cop.quantity,
                    tph.tr_quantity AS lbw_menge')
            ->from('customer_orders_pos', 'cop')
            ->innerJoin(
                'cop',
                'transport_history',
                'tph',
                'cop.customer_order_id = tph.doc_id'
            )
            ->andWhere('tph.article_nr = cop.article_nr');

        $result = $queryBuilder->executeQuery();
        $results = $result->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function getCustomerOrderPosByCustomerOrderId(int $id): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select('cop.customer_order_id,
                    tph.order_nr as customer_order_nr,
                    cop.article_nr,
                    cop.article_name,
                    cop.quantity,
                    tph.tr_quantity AS lbw_menge')
            ->from('customer_orders_pos', 'cop')
            ->innerJoin(
                'cop',
                'transport_history',
                'tph',
                'cop.customer_order_id = tph.doc_id'
            )
            ->andWhere('tph.doc_id = :customer_order_id')
            ->andWhere('tph.article_nr = cop.article_nr')
            ->setParameter('customer_order_id', $id);

        $result = $queryBuilder->executeQuery();
        $results = $result->fetchAllAssociative();

        return new JsonResponse($results);
    }

    /**
     * @return object[]
     */
    public function getLastCustomerOrderId(): array
    {
        $customerOrderRepository = $this->entityManager
            ->getRepository(CustomerOrderEntity::class);

        return $customerOrderRepository->findBy([], ['customerOrderId' => 'DESC'], 1, 0);
    }

    public function addCustomerOrder(CustomerOrderEntity $customerOrderEntity): void
    {
        $customerOrderEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($customerOrderEntity);
    }

    public function updateCustomerOrder(CustomerOrderEntity $customerOrderEntity): void
    {
        $customerOrderEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($customerOrderEntity);
    }

    public function deleteCustomerOrder(CustomerOrderEntity $customerOrderEntity): void
    {
        $this->delete($customerOrderEntity);
    }
}
