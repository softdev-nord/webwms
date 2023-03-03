<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\SupplierOrderPos;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\SupplierOrderPos;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\SupplierOrderPos
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrderPosDataHandler
 */
class SupplierOrderPosDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function save(SupplierOrderPos $supplierOrderPos): void
    {
        $this->entityManager->persist($supplierOrderPos);
        $this->entityManager->flush();
    }

    public function delete(SupplierOrderPos $supplierOrderPos): void
    {
        $this->entityManager->remove($supplierOrderPos);
        $this->entityManager->flush();
    }

    public function getSupplierOrderPosById(int $supplierOrderPosId): ?SupplierOrderPos
    {
        return $this->entityManager
            ->getRepository(SupplierOrderPos::class)
            ->findOneBy(['id' => $supplierOrderPosId]);
    }

    public function getSupplierOrderPosBySupplierOrderId(int $supplierOrderId): ?SupplierOrderPos
    {
        return $this->entityManager
            ->getRepository(SupplierOrderPos::class)
            ->find(['supplierOrderId' => $supplierOrderId]);
    }

    public function getAllSupplierOrderPos(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('supplier_order_pos');

        $stmt = $queryBuilder->executeQuery();

        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function addSupplierOrderPos(Request $request): void
    {
        $requestData = $request->request->all()['supplier_order_pos'];
        $supplierOrderPos = new SupplierOrderPos();

        $supplierOrderPos->setSupplierOrderId((int) $requestData['supplierOrderId']);
        $supplierOrderPos->setSupplierOrderPosQuantity((int) $requestData['supplierOrderPosQuantity']);
        $supplierOrderPos->setArticleId((int) $requestData['articleId']);
        $supplierOrderPos->setArticleNr((string) $requestData['articleNr']);
        $supplierOrderPos->setArticleName((string) $requestData['articleName']);
        $supplierOrderPos->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($supplierOrderPos);
    }

    public function updateSupplierOrderPos(SupplierOrderPos $supplierOrder): void
    {
        $supplierOrder->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($supplierOrder);
    }

    public function deleteSupplierOrderPos(?SupplierOrderPos $supplierOrderPos): void
    {
        if ($supplierOrderPos !== null) {
            $this->delete($supplierOrderPos);
        }
    }
}
