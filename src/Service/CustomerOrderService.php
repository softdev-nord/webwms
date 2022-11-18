<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Entity\CustomerOrder as CustomerOrders;
use WebWMS\Repository\CustomerOrderRepository;
use WebWMS\Service\DataHandlers\Customer\CustomerDataHandler;
use WebWMS\Service\DataHandlers\CustomerOrder\CustomerOrderDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrderService
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerOrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CustomerOrderRepository $customerOrderRepository,
        private CustomerDataHandler $customerDataHandler,
        private CustomerOrderDataHandler $customerOrderDataHandler
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getCustomerOrderApi(int $customerOrderId): ?CustomerOrders
    {
        $customerOrder = $this->customerOrderDataHandler->getCustomerOrderById($customerOrderId);

        if (!$customerOrder) {
            throw new EntityNotFoundException('Customer order with id '.$customerOrderId.' does not exist!');
        }

        return $customerOrder;
    }

    /**
     * @throws \Exception
     */
    public function getAllCustomerOrdersApi(): array
    {
        return $this->customerOrderRepository->findBy([], ['customerOrderId' => 'ASC']);
    }

    public function addCustomerOrderApi(
        int $customerOrderId,
        int $usrId,
        int $customerId,
        string $customerOrderNr,
        string $customerOrderReference,
        $customerOrderDate,
        $customerOrderOrderDate
    ): CustomerOrders {
        $createdAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $customerOrder = new CustomerOrders();
        $customerOrder->setCustomerOrderId($customerOrderId);
        $customerOrder->setUsrId($usrId);
        $customerOrder->setCustomerId($customerId);
        $customerOrder->setCustomerOrderNr($customerOrderNr);
        $customerOrder->setCustomerOrderReference($customerOrderReference);
        $customerOrder->setCustomerOrderDate($customerOrderDate);
        $customerOrder->setCustomerOrderCreationDate($customerOrderOrderDate);
        $customerOrder->setCreatedAt($createdAt);
        $this->customerOrderDataHandler->save($customerOrder);

        return $customerOrder;
    }

    public function updateCustomerOrderApi(
        int $customerOrderMainId,
        int $customerOrderId,
        int $usrId,
        int $customerId,
        string $customerOrderNr,
        string $customerOrderReference,
        $customerOrderDate,
        $customerOrderCreationDate
    ): ?CustomerOrders {
        $updatedAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $customerOrder = $this->customerOrderDataHandler->getCustomerOrderById($customerOrderMainId);

        $customerOrder->setCustomerOrderId($customerOrderId);
        $customerOrder->setUsrId($usrId);
        $customerOrder->setCustomerId($customerId);
        $customerOrder->setCustomerOrderNr($customerOrderNr);
        $customerOrder->setCustomerOrderReference($customerOrderReference);
        $customerOrder->setCustomerOrderDate($customerOrderDate);
        $customerOrder->setCustomerOrderCreationDate($customerOrderCreationDate);
        $customerOrder->setUpdatedAt($updatedAt);
        $this->customerOrderDataHandler->save($customerOrder);

        return $customerOrder;
    }

    /**
     * @throws EntityNotFoundException
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function deleteCustomerOrderApi(int $customerOrderId): void
    {
        $customerOrder = $this->customerOrderDataHandler->getCustomerOrderById($customerOrderId);

        if (!$customerOrder) {
            throw new EntityNotFoundException('Supplier with id '.$customerOrderId.' does not exist!');
        } else {
            $this->customerOrderDataHandler->delete($customerOrder);
        }
    }

    /**
     * Get all Customer Orders for Ajax-Request.
     *
     * @throws Exception
     */
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
            ->innerJoin('co', 'customer_order_pos', 'cop', 'cop.customer_order_id = co.customer_order_id')
            ->innerJoin('co', 'customer', 'cu', 'cu.customer_id = co.customer_id')
            ->innerJoin('co', 'user', 'usr', 'usr.id = co.usr_id')
            ->groupBy('cop.customer_order_id');

        $stmt = $queryBuilder->executeQuery();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
    }

    /**
     * Get all Customer Order Pos for Ajax-Request.
     *
     * @throws Exception
     */
    public function getAllCustomerOrderPos(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $sql = "SELECT DISTINCT cop.customer_order_id, th.order_nr, art.article_nr, art.article_name, cop.customer_order_pos_quantity, th.tr_quantity AS lbw_menge
            FROM transport_history AS th
            INNER JOIN article AS art
                ON th.article_nr = art.article_nr
            INNER JOIN customer_orders AS co
                ON th.doc_id = co.customer_order_id
            LEFT OUTER JOIN customer_order_pos AS cop
                ON th.order_nr LIKE CONCAT('%', cop.customer_order_id ,'%')";

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    /**
     * Get last customer order id.
     *
     * @return object[]
     */
    public function getLastCustomerOrderId(): array
    {
        $customerOrderRepository = $this->entityManager
            ->getRepository(CustomerOrders::class);

        return $customerOrderRepository->findBy([], ['customerOrderId' => 'DESC'], 1, 0);
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }

    public function addNewCustomerOrderAndRelatedPositions(Request $request)
    {
        // TODO: Implement logic

        $params = $request->request->all()['customer'];
        $lastCustomer = $this->customerDataHandler->getLastCustomer()[0]->toArray();

        $customerOrder = new CustomerOrders();
        $customerOrder->setCustomerId($lastCustomer['customer_id'] + 1);
        $customerOrder->setCustomerNr($lastCustomer['customer_nr'] + 1);
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
