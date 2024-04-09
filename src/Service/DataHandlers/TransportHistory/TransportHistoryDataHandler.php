<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\TransportHistory;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportHistoryEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\TransportHistoryEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportHistoryDataHandler
 */
class TransportHistoryDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService,
    ) {
    }

    public function save(TransportHistoryEntity $transportHistoryEntity): void
    {
        $this->entityManager->persist($transportHistoryEntity);
        $this->entityManager->flush();
    }

    public function delete(TransportHistoryEntity $transportHistoryEntity): void
    {
        $this->entityManager->remove($transportHistoryEntity);
        $this->entityManager->flush();
    }

    /**
     * @return array<object>
     */
    public function getTransportHistoryById(int $id): array
    {
        return $this->entityManager
            ->getRepository(TransportHistoryEntity::class)
            ->findBy(['id' => $id]);
    }

    /**
     * @return array<object>
     */
    public function getAllTransportHistories(): array
    {
        return $this->entityManager
            ->getRepository(TransportHistoryEntity::class)
            ->findAll();
    }

    public function createTransportHistory(Request $request, string $user, string $clientIp): void
    {
        $requestData = (array) $request->request->all()['stock_in_final'];
        $articleNr = $requestData['article_nr'];
        $bookingMethod = $requestData['booking_method'];
        $charge = $requestData['charge'];
        $loadingEquipment = $requestData['loading_equipment'];
        $transportHistoryEntity = new TransportHistoryEntity();

        foreach ($requestData as $key => $data) {
            $transportHistoryEntity->setSuId($this->getLastStockUnit()[0]->getSuId());
            $transportHistoryEntity->setTrNr($this->getLastTransportHistoryNr());
            $transportHistoryEntity->setTrPos($key + 1);
            $transportHistoryEntity->setTrPrio(0);
            $transportHistoryEntity->setArticleNr((string) $articleNr);
            $transportHistoryEntity->setTrQuantity((float) $data['stock_quantity']);
            $transportHistoryEntity->setStockCoordinate((string) $data['stock_coordinate']);
            $transportHistoryEntity->setStockNr((int) $data['stock_ln']);
            $transportHistoryEntity->setStockLevel1((int) $data['stock_fb']);
            $transportHistoryEntity->setStockLevel2((int) $data['stock_sp']);
            $transportHistoryEntity->setStockLevel3((int) $data['stock_tf']);
            $transportHistoryEntity->setStockLevel4(1);
            $transportHistoryEntity->setTrAccess($this->dateTimeService->createDateTime());
            $transportHistoryEntity->setTrState(0);
            $transportHistoryEntity->setOrderUsername($user);
            $transportHistoryEntity->setBookingMethod((string) $bookingMethod);
            $transportHistoryEntity->setDocId(0);
            $transportHistoryEntity->setCharge((string) $charge);
            $transportHistoryEntity->setTrComputerIp($clientIp);
            $transportHistoryEntity->setLoadingEquipment((string) $loadingEquipment);
            $transportHistoryEntity->setTrType(1);
        }

        $this->save($transportHistoryEntity);
    }

    /** @return TransportHistoryEntity[] */
    public function getLastStockUnit(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('the')
            ->from(TransportHistoryEntity::class, 'the')
            ->setMaxResults(1)
            ->addOrderBy('the.suId', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getLastTransportHistoryNr(): int
    {
        $lastTransportHistoryNr = $this->entityManager
            ->getRepository(TransportHistoryEntity::class)
            ->findBy([], ['trNr' => 'DESC'], 1, 0);

        return $lastTransportHistoryNr[0]->getTrNr();
    }

    public function addTransportHistory(TransportHistoryEntity $transportHistoryEntity): void
    {
        $transportHistoryEntity->setCreatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportHistoryEntity);
    }

    public function updateTransportHistory(TransportHistoryEntity $transportHistoryEntity): void
    {
        $transportHistoryEntity->setUpdatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportHistoryEntity);
    }

    public function deleteTransportHistory(TransportHistoryEntity $transportHistoryEntity): void
    {
        $this->delete($transportHistoryEntity);
    }
}
