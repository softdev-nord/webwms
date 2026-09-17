<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\TransportRequest;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use WebWMS\Dto\StockInFinalDto;
use WebWMS\Entity\TransportHistory;
use WebWMS\Entity\TransportRequest;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;
use WebWMS\Service\Stock\StockOccupancyService;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers\TransportRequest',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'TransportRequestDataHandler'
)]
readonly class TransportRequestDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockOccupancyService $stockOccupancyService,
        private DateTimeService $dateTimeService,
    ) {
    }

    public function save(TransportRequest $transportRequest): void
    {
        $this->entityManager->persist($transportRequest);
        $this->entityManager->flush();
    }

    public function delete(TransportRequest $transportRequest): void
    {
        $this->entityManager->remove($transportRequest);
        $this->entityManager->flush();
    }

    public function getTransportRequestById(int $id): ?TransportRequest
    {
        /** @var TransportRequest|null $transportRequest */
        $transportRequest = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findOneBy(['id' => $id]);

        return $transportRequest;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
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
#                tr.from_stock_coordinate,
#                CONCAT(tr.from_stock_nr,"-",tr.from_stock_level1,"-",tr.from_stock_level2,"-",tr.from_stock_level3) AS from_stock_location,
#                tr.to_stock_coordinate,
#                CONCAT(tr.to_stock_nr,"-",tr.to_stock_level1,"-",tr.to_stock_level2,"-",tr.to_stock_level3) AS to_stock_location,
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
                tr.tr_type'
            )
            ->from('transport_request', 'tr');

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();

        return new JsonResponse($results);
    }

    /**
     * Erstellt mehrere Transportaufträge transaktional.
     * Bei Fehler wird die gesamte Operation zurückgerollt.
     */
    public function createTransportRequest(Request $request, string $user, string $clientIp): ?Response
    {
        $requestData = (array) $request->request->all()['stock_in_final'];
        $entities = [];
        $actualDateTime = $this->dateTimeService->createDateTime();

        try {
            if (empty($requestData)) {
                throw new Exception('Keine Transportdaten vorhanden.');
            }

            foreach ($requestData as $key => $data) {
                $data = StockInFinalDto::hydrate((array) $data);

                $transportRequest = (new TransportRequest())
                    ->setSuId($data->getStockSuId())
                    ->setTrNr($this->getLastTransportRequestNr() + 1)
                    ->setTrPos($key + 1)
                    ->setTrPrio(0)
                    ->setArticleNr($data->getArticleNr())
                    ->setTrQuantity($data->getStockQuantity())
                    ->setStockCoordinate($data->getStockCoordinate())
                    ->setStockNr($data->getStockLn())
                    ->setStockLevel1($data->getStockFb())
                    ->setStockLevel2($data->getStockSp())
                    ->setStockLevel3($data->getStockTf())
                    ->setStockLevel4(1)
                    ->setTrAccess($actualDateTime)
                    ->setTrState('open')
                    ->setOrderUsername($user)
                    ->setBookingMethod($data->getBookingMethod())
                    ->setDocId(null)
                    ->setCharge($data->getCharge())
                    ->setTrComputerIp($clientIp)
                    ->setLoadingEquipment($data->getLoadingEquipment())
                    ->setTrType(1)
                    ->setCreatedAt($actualDateTime);

                $entities[] = $transportRequest;

                // TODO: T4.2 Bestandssynchronisierung bei vollständiger Inventur-Integration
                // $this->stockOccupancyService->updateStockOccupancy($data, $actualDateTime);
            }

            foreach ($entities as $entity) {
                $this->entityManager->persist($entity);
            }

            $this->entityManager->flush();
            $this->entityManager->clear();

            return new Response('success');
        } catch (Exception $e) {
            // Rollback bei Fehler
            $this->entityManager->rollback();
            // Fehler wird an Caller propagiert
            throw $e;
        }
    }

    /** @return TransportRequest[] */
    public function getLastStockUnit(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('tre')
            ->from(TransportRequest::class, 'tre')
            ->setMaxResults(1)
            ->addOrderBy('tre.suId', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getLastTransportRequestNr(): int
    {
        /** @var TransportRequest[] $lastTransportRequestNr */
        $lastTransportRequestNr = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findBy([], ['trNr' => 'DESC'], 1, 0);

        return $lastTransportRequestNr[0]->getTrNr();
    }

    public function addTransportRequest(TransportRequest $transportRequest): void
    {
        $transportRequest->setCreatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportRequest);
    }

    public function updateTransportRequest(TransportRequest $transportRequest): void
    {
        $transportRequest->setUpdatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportRequest);
    }

    public function deleteTransportRequest(TransportRequest $transportRequest): void
    {
        $this->delete($transportRequest);
    }

    public function moveTransportRequestToTransportHistory(int $id): void
    {
        /** @var TransportRequest|null $transportRequestEntry */
        $transportRequestEntry = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findOneBy(['id' => $id]);

        if ($transportRequestEntry !== null) {
            $transportHistoryEntry = $this->setDataToMove($transportRequestEntry);

            $this->entityManager->persist($transportHistoryEntry);
            $this->entityManager->flush();
        }
    }

    public function setDataToMove(TransportRequest $transportRequest): TransportHistory
    {
        return (new TransportHistory())
            ->setSuId($transportRequest->getSuId())
            ->setTrNr($transportRequest->getTrNr())
            ->setTrPos($transportRequest->getTrPos())
            ->setTrPrio($transportRequest->getTrPrio())
            ->setArticleNr($transportRequest->getArticleNr())
            ->setTrQuantity($transportRequest->getTrQuantity())
            ->setStockCoordinate($transportRequest->getStockCoordinate())
            ->setStockNr($transportRequest->getStockNr())
            ->setStockLevel1($transportRequest->getStockLevel1())
            ->setStockLevel2($transportRequest->getStockLevel2())
            ->setStockLevel3($transportRequest->getStockLevel3())
            ->setStockLevel4($transportRequest->getStockLevel4())
//            ->setFromStockCoordinate($transportRequest->getFromStockCoordinate())
//            ->setFromStockNr($transportRequest->getFromStockNr())
//            ->setFromStockLevel1($transportRequest->getFromStockLevel1())
//            ->setFromStockLevel2($transportRequest->getFromStockLevel2())
//            ->setFromStockLevel3($transportRequest->getFromStockLevel3())
//            ->setFromStockLevel4($transportRequest->getFromStockLevel4())
//            ->setToStockCoordinate($transportRequest->getToStockCoordinate())
//            ->setToStockNr($transportRequest->getToStockNr())
//            ->setToStockLevel1($transportRequest->getToStockLevel1())
//            ->setToStockLevel2($transportRequest->getToStockLevel2())
//            ->setToStockLevel3($transportRequest->getToStockLevel3())
//            ->setToStockLevel4($transportRequest->getToStockLevel4())
            ->setTrAccess($transportRequest->getTrAccess())
            ->setTrDispatch($transportRequest->getTrDispatch())
            ->setTrState($transportRequest->getTrState())
            ->setOrderUsername($transportRequest->getOrderUsername())
            ->setBookingMethod($transportRequest->getBookingMethod())
            ->setDocId($transportRequest->getDocId())
            ->setOrderNr($transportRequest->getOrderNr())
            ->setOrderPos($transportRequest->getOrderPos())
            ->setCharge($transportRequest->getCharge())
            ->setLoadingEquipment($transportRequest->getLoadingEquipment())
            ->setConfirmationState($transportRequest->getConfirmationState())
            ->setTrUsername($transportRequest->getTrUsername())
            ->setTrComputerIp($transportRequest->getTrComputerIp())
            ->setTrBlocked($transportRequest->getTrBlocked())
            ->setTrStartDate($transportRequest->getTrStartDate())
            ->setTrEdited($transportRequest->getTrEdited())
            ->setTrType($transportRequest->getTrType())
            ->setCreatedAt($transportRequest->getCreatedAt())
            ->setUpdatedAt($transportRequest->getUpdatedAt());
    }
}
