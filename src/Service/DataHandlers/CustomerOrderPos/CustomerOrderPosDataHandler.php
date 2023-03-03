<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\CustomerOrderPos;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\CustomerOrderPos
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
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
            ->find(['customerOrderId' => $customerOrderId]);
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

    public function addCustomerOrderPos(Request $request): void
    {
        $requestData = $request->request->all()['customer_orders_pos'];
        $customerOrderPos = new CustomerOrderPos();

        $customerOrderPos->setCustomerOrderId((int) $requestData['customerOrderId']);
        $customerOrderPos->setQuantity((int) $requestData['quantity']);
        $customerOrderPos->setArticleId((int) $requestData['articleId']);
        $customerOrderPos->setArticleNr((string) $requestData['articleNr']);
        $customerOrderPos->setArticleName((string) $requestData['articleName']);
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
