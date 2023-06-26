<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\TransportHistory;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportHistory;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\TransportHistory
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportHistoryDataHandler
 */
class TransportHistoryDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService,
    ) {
    }

    public function save(TransportHistory $transportHistory): void
    {
        $this->entityManager->persist($transportHistory);
        $this->entityManager->flush();
    }

    public function delete(TransportHistory $transportHistory): void
    {
        $this->entityManager->remove($transportHistory);
        $this->entityManager->flush();
    }

    /**
     * @return array<object>
     */
    public function getTransportHistoryById(int $id): array
    {
        return $this->entityManager
            ->getRepository(TransportHistory::class)
            ->findBy(['id' => $id]);
    }

    /**
     * @return array<object>
     */
    public function getAllTransportHistories(): array
    {
        return $this->entityManager
            ->getRepository(TransportHistory::class)
            ->findAll();
    }

    public function createTransportHistory(Request $request, string $user, string $clientIp): void
    {
        $requestData = (array) $request->request->all()['stock_in_final'];
        $articleNr = $requestData['article_nr'];
        $bookingMethod = $requestData['booking_method'];
        $charge = $requestData['charge'];
        $loadingEquipment = $requestData['loading_equipment'];
        $transportHistory = new TransportHistory();

        foreach ($requestData as $key => $data) {
            $transportHistory->setSuId($this->getLastStockUnit());
            $transportHistory->setTrNr($this->getLastTransportHistoryNr());
            $transportHistory->setTrPos($key + 1);
            $transportHistory->setTrPrio(0);
            $transportHistory->setArticleNr(strval($articleNr));
            $transportHistory->setTrQuantity(floatval($data['stock_quantity']));
            $transportHistory->setStockCoordinate(strval($data['stock_coordinate']));
            $transportHistory->setStockNr(intval($data['stock_ln']));
            $transportHistory->setStockLevel1(intval($data['stock_fb']));
            $transportHistory->setStockLevel2(intval($data['stock_sp']));
            $transportHistory->setStockLevel3(intval($data['stock_tf']));
            $transportHistory->setStockLevel4(1);
            $transportHistory->setTrAccess($this->dateTimeService->createDateTime());
            $transportHistory->setTrState(0);
            $transportHistory->setOrderUsername($user);
            $transportHistory->setBookingMethod(strval($bookingMethod));
            $transportHistory->setDocId(0);
            $transportHistory->setCharge(strval($charge));
            $transportHistory->setTrComputerIp($clientIp);
            $transportHistory->setLoadingEquipment(strval($loadingEquipment));
            $transportHistory->setTrType(1);
        }

        $this->save($transportHistory);
    }

    public function getLastStockUnit(): int
    {
        $lastStockUnitTr = $this->entityManager
            ->getRepository(TransportHistory::class)
            ->findBy([], ['suId' => 'DESC'], 1, 0);

        return $lastStockUnitTr[0]->getSuId();
    }

    public function getLastTransportHistoryNr(): int
    {
        $lastTransportHistoryNr = $this->entityManager
            ->getRepository(TransportHistory::class)
            ->findBy([], ['trNr' => 'DESC'], 1, 0);

        return $lastTransportHistoryNr[0]->getTrNr();
    }

    public function addTransportHistory(TransportHistory $transportHistory): void
    {
        $transportHistory->setCreatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportHistory);
    }

    public function updateTransportHistory(TransportHistory $transportHistory): void
    {
        $transportHistory->setUpdatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportHistory);
    }

    public function deleteTransportHistory(TransportHistory $transportHistory): void
    {
        $this->delete($transportHistory);
    }
}
