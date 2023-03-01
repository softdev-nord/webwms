<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\CustomerOrder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Customer;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrderDataHandler
 */
class CustomerOrderDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        // private CustomerDataHandler $customerDataHandler
    ) {
    }

    public function save(CustomerOrder $customerOrder): void
    {
        $this->entityManager->persist($customerOrder);
        $this->entityManager->flush();
    }

    public function update(CustomerOrder $customerOrder): void
    {
        $this->entityManager->persist($customerOrder);
        $this->entityManager->flush();
    }

    public function delete(CustomerOrder $customerOrder): void
    {
        $this->entityManager->remove($customerOrder);
        $this->entityManager->flush();
    }

    /**
     * @return CustomerOrder|null Returns an array of Customer order objects
     */
    public function getCustomerOrderById(int $id): ?CustomerOrder
    {
        return $this->entityManager
            ->getRepository(CustomerOrder::class)
            ->findOneBy(['id' => $id]);
    }

    public function getAllCustomerOrders(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->select(
                'co.customer_order_id',
                'co.customer_order_nr',
                'co.customer_order_reference',
                'cu.customer_nr',
                'cu.customer_name',
                'co.customer_order_date',
                'co.customer_order_creation_date',
                'usr.username'
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

        $stmt = $queryBuilder->executeQuery();
        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function getCustomerOrderPosByOrderId(int $id): JsonResponse
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

        $stmt = $queryBuilder->executeQuery();
        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    /**
     * @return object[]
     */
    public function getLastCustomerOrderId(): array
    {
        $customerOrderRepository = $this->entityManager
            ->getRepository(CustomerOrder::class);

        return $customerOrderRepository->findBy([], ['customerOrderId' => 'DESC'], 1, 0);
    }

    public function addNewCustomerOrderAndRelatedPositions(Request $request): void
    {
        // TODO: Implement logic

        $params = $request->request->all()['customer'];
        // $lastCustomer = $this->customerDataHandler->getLastCustomer();

        $customerOrder = new Customer();
        // $customerOrder->setCustomerId($lastCustomer['customer_id'] + 1);
        // $customerOrder->setCustomerNr($lastCustomer['customer_nr'] + 1);
        $customerOrder->setCustomerName($params['customer_name']);
        $customerOrder->setCustomerAddressAddition($params['customer_address_addition']);
        $customerOrder->setCustomerAddressStreet($params['customer_address_street']);
        $customerOrder->setCustomerAddressStreetNr($params['customer_address_street_nr']);
        $customerOrder->setCustomerCountryCode($params['customer_country_code']);
        $customerOrder->setCustomerZipCode($params['customer_zip_code']);
        $customerOrder->setCustomerCity($params['customer_city']);
        $this->entityManager->persist($customerOrder);
        $this->entityManager->flush();
    }
}
