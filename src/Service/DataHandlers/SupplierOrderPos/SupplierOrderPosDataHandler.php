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
            ->find($supplierOrderId);
    }

    public function getAllSupplierOrderPos(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $sql = "SELECT pos.supplier_order_id, ord.supplier_order_nr, art.article_nr, art.article_name, pos.supplier_order_pos_quantity,
                (SELECT (SUM(IF(transport_history.tr_type = '1', transport_history.tr_quantity, 0.000))) FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
                FROM supplier_order_pos AS pos
                INNER JOIN supplier_orders AS ord
                    ON pos.supplier_order_id = ord.supplier_order_id
                INNER JOIN article AS art
                    ON pos.article_id = art.article_id
                LEFT OUTER JOIN transport_history AS lbw
                    ON ord.supplier_order_nr = lbw.order_nr
                GROUP BY pos.article_id ORDER BY pos.article_id";

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    public function addSupplierOrderPos(Request $request): void
    {
        $requestData = $request->request->all()['supplier_order_pos'];
        $supplierOrderPos = new SupplierOrderPos();

        $supplierOrderPos->setSupplierOrderId(intval($requestData['supplierOrderId']));
        $supplierOrderPos->setSupplierOrderPosQuantity(intval($requestData['supplierOrderPosQuantity']));
        $supplierOrderPos->setArticleId(intval($requestData['articleId']));
        $supplierOrderPos->setArticleNr(strval($requestData['articleNr']));
        $supplierOrderPos->setArticleName(strval($requestData['articleName']));
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
