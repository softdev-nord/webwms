<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\TransportRequest;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Dto\StockInFinalDto;
use WebWMS\Entity\TransportHistoryEntity;
use WebWMS\Entity\TransportRequestEntity;
use WebWMS\Service\DateTimeService;
use WebWMS\Service\Stock\StockOccupancyService;

/**
 * @package:    WebWMS\Service\DataHandlers\TransportRequestEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequestDataHandler
 */
class TransportRequestDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StockOccupancyService $stockOccupancyService,
        private readonly DateTimeService $dateTimeService,
    ) {
    }

    public function save(TransportRequestEntity $transportRequestEntity): void
    {
        $this->entityManager->persist($transportRequestEntity);
        $this->entityManager->flush();
    }

    public function delete(TransportRequestEntity $transportRequestEntity): void
    {
        $this->entityManager->remove($transportRequestEntity);
        $this->entityManager->flush();
    }

    public function getTransportRequestById(int $id): ?TransportRequestEntity
    {
        return $this->entityManager
            ->getRepository(TransportRequestEntity::class)
            ->findOneBy(['id' => $id]);
    }

    public function getAllOpenTransportRequests(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select(
                '
                tr.id,
                tr.su_id,
                tr.tr_nr,
                tr.tr_pos,
                tr.tr_prio,
                tr.article_nr,
                tr.tr_quantity,
                tr.stock_coordinate,
                CONCAT(tr.stock_nr,"-",tr.stock_level1,"-",tr.stock_level2,"-",tr.stock_level3) AS stock_location,
                tr.tr_state,
                tr.order_username,
                tr.booking_method,
                tr.order_nr,
                tr.loading_equipment,
                tr.tr_username,
                tr.tr_computer_ip,
                tr.tr_blocked,
                tr.tr_start_date,
                tr.tr_edited,
                tr.tr_type')
            ->from('transport_request', 'tr');

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function createTransportRequest(Request $request, string $user, string $clientIp): ?Response
    {
        $requestData = (array) $request->request->all()['stock_in_final'];
        $entities = [];
        $actualDateTime = $this->dateTimeService->createDateTime();

        try {
            foreach ($requestData as $key => $data) {
                $data = StockInFinalDto::hydrate($data);

                $entities[] = (new TransportRequestEntity())
                    ->setSuId((int) $data->getStockSuId())
                    ->setTrNr($this->getLastTransportRequestNr() + 1)
                    ->setTrPos($key + 1)
                    ->setTrPrio(0)
                    ->setArticleNr((string) $data->getArticleNr())
                    ->setTrQuantity((float) $data->getStockQuantity())
                    ->setStockCoordinate((string) $data->getStockCoordinate())
                    ->setStockNr((int) $data->getStockLn())
                    ->setStockLevel1((int) $data->getStockFb())
                    ->setStockLevel2((int) $data->getStockSp())
                    ->setStockLevel3((int) $data->getStockTf())
                    ->setStockLevel4(1)
                    ->setTrAccess($actualDateTime)
                    ->setTrState(0)
                    ->setOrderUsername($user)
                    ->setBookingMethod((string) $data->getBookingMethod())
                    ->setDocId(null)
                    ->setCharge((string) $data->getCharge())
                    ->setTrComputerIp($clientIp)
                    ->setLoadingEquipment((string) $data->getLoadingEquipment())
                    ->setTrType(1)
                    ->setCreatedAt($actualDateTime);

                $this->stockOccupancyService->updateStockOccupancy($data, $actualDateTime);
            }

            foreach ($entities as $entity) {
                $this->entityManager->persist($entity);
            }

            $this->entityManager->flush();
            $this->entityManager->clear();

            return new Response('success');
        } catch (Exception) {
            return null;
        }
    }

    /** @return TransportRequestEntity[] */
    public function getLastStockUnit(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('tre')
            ->from(TransportRequestEntity::class, 'tre')
            ->setMaxResults(1)
            ->addOrderBy('tre.suId', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getLastTransportRequestNr(): int
    {
        $lastTransportRequestNr = $this->entityManager
            ->getRepository(TransportRequestEntity::class)
            ->findBy([], ['trNr' => 'DESC'], 1, 0);

        return $lastTransportRequestNr[0]->getTrNr();
    }

    public function addTransportRequest(TransportRequestEntity $transportRequestEntity): void
    {
        $transportRequestEntity->setCreatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportRequestEntity);
    }

    public function updateTransportRequest(TransportRequestEntity $transportRequestEntity): void
    {
        $transportRequestEntity->setUpdatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportRequestEntity);
    }

    public function deleteTransportRequest(TransportRequestEntity $transportRequestEntity): void
    {
        $this->delete($transportRequestEntity);
    }

    public function moveTransportRequestToTransportHistory(int $id): void
    {
        $transportRequestEntry = $this->entityManager
            ->getRepository(TransportRequestEntity::class)
            ->findOneBy(['id' => $id]);

        if ($transportRequestEntry !== null) {
            $transportHistoryEntry = $this->setDataToMove($transportRequestEntry);

            $this->entityManager->persist($transportHistoryEntry);
            $this->entityManager->flush();
        }
    }

    public function setDataToMove(TransportRequestEntity $transportRequestEntity): TransportHistoryEntity
    {
        return (new TransportHistoryEntity())
            ->setSuId($transportRequestEntity->getSuId())
            ->setTrNr($transportRequestEntity->getTrNr())
            ->setTrPos($transportRequestEntity->getTrPos())
            ->setTrPrio($transportRequestEntity->getTrPrio())
            ->setArticleNr($transportRequestEntity->getArticleNr())
            ->setTrQuantity($transportRequestEntity->getTrQuantity())
            ->setStockCoordinate($transportRequestEntity->getStockCoordinate())
            ->setStockNr($transportRequestEntity->getStockNr())
            ->setStockLevel1($transportRequestEntity->getStockLevel1())
            ->setStockLevel2($transportRequestEntity->getStockLevel2())
            ->setStockLevel3($transportRequestEntity->getStockLevel3())
            ->setStockLevel4($transportRequestEntity->getStockLevel4())
            ->setTrAccess($transportRequestEntity->getTrAccess())
            ->setTrDispatch($transportRequestEntity->getTrDispatch())
            ->setTrState($transportRequestEntity->getTrState())
            ->setOrderUsername($transportRequestEntity->getOrderUsername())
            ->setBookingMethod($transportRequestEntity->getBookingMethod())
            ->setDocId($transportRequestEntity->getDocId())
            ->setOrderNr($transportRequestEntity->getOrderNr())
            ->setOrderPos($transportRequestEntity->getOrderPos())
            ->setCharge($transportRequestEntity->getCharge())
            ->setLoadingEquipment($transportRequestEntity->getLoadingEquipment())
            ->setConfirmationState($transportRequestEntity->getConfirmationState())
            ->setTrUsername($transportRequestEntity->getTrUsername())
            ->setTrComputerIp($transportRequestEntity->getTrComputerIp())
            ->setTrBlocked($transportRequestEntity->getTrBlocked())
            ->setTrStartDate($transportRequestEntity->getTrStartDate())
            ->setTrEdited($transportRequestEntity->getTrEdited())
            ->setTrType($transportRequestEntity->getTrType())
            ->setCreatedAt($transportRequestEntity->getCreatedAt())
            ->setUpdatedAt($transportRequestEntity->getUpdatedAt());
    }
}
