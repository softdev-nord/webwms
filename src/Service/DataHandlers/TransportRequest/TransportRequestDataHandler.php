<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\TransportRequest;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportRequestEntity;
use WebWMS\Service\DateTimeService;

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

    /**
     * @return array<object>
     */
    public function getTransportRequestById(int $id): array
    {
        return $this->entityManager
            ->getRepository(TransportRequestEntity::class)
            ->findBy(['id' => $id]);
    }

    /**
     * @return array<object>
     */
    public function getAllOpenTransportRequests(): array
    {
        return $this->entityManager
            ->getRepository(TransportRequestEntity::class)
            ->findAll();
    }

    public function createTransportRequest(Request $request, string $user, string $clientIp): void
    {
        $requestData = (array) $request->request->all()['stock_in_final'];
        $articleNr = $requestData['article_nr'];
        $bookingMethod = $requestData['booking_method'];
        $charge = $requestData['charge'];
        $loadingEquipment = $requestData['loading_equipment'];
        $transportRequestEntity = new TransportRequestEntity();

        foreach ($requestData as $key => $data) {
            $transportRequestEntity->setSuId($this->getLastStockUnit());
            $transportRequestEntity->setTrNr($this->getLastTransportRequestNr());
            $transportRequestEntity->setTrPos($key + 1);
            $transportRequestEntity->setTrPrio(0);
            $transportRequestEntity->setArticleNr((string) $articleNr);
            $transportRequestEntity->setTrQuantity((float) $data['stock_quantity']);
            $transportRequestEntity->setStockCoordinate((string) $data['stock_coordinate']);
            $transportRequestEntity->setStockNr((int) $data['stock_ln']);
            $transportRequestEntity->setStockLevel1((int) $data['stock_fb']);
            $transportRequestEntity->setStockLevel2((int) $data['stock_sp']);
            $transportRequestEntity->setStockLevel3((int) $data['stock_tf']);
            $transportRequestEntity->setStockLevel4(1);
            $transportRequestEntity->setTrAccess($this->dateTimeService->createDateTime());
            $transportRequestEntity->setTrState(0);
            $transportRequestEntity->setOrderUsername($user);
            $transportRequestEntity->setBookingMethod((string) $bookingMethod);
            $transportRequestEntity->setDocId(null);
            $transportRequestEntity->setCharge((string) $charge);
            $transportRequestEntity->setTrComputerIp($clientIp);
            $transportRequestEntity->setLoadingEquipment((string) $loadingEquipment);
            $transportRequestEntity->setTrType(1);
        }

        $this->save($transportRequestEntity);
    }

    public function getLastStockUnit(): int
    {
        $lastStockUnitTr = $this->entityManager
            ->getRepository(TransportRequestEntity::class)
            ->findBy([], ['suId' => 'DESC'], 1, 0);

        return $lastStockUnitTr[0]->getSuId();
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

    //    public function moveTransportRequestToTransportHistory(Request $request): void
    //    {
    //        $requestData = $request->request->all();
    //        $trNr = (string) $requestData['tr_nr'];
    //        $transportRequestEntry = $this->entityManager
    //            ->getRepository(TransportRequestEntity::class)
    //            ->findOneBy(['tr_nr' => $trNr]);
    //
    //        $transportHistoryObject = new TransportHistoryEntity();
    //        $transportHistoryEntry = $this->setData($transportHistoryObject, $request);
    //
    //        $this->entityManager->persist($transportHistoryEntry);
    //        $this->entityManager->remove($transportRequestEntry);
    //        $this->entityManager->flush();
    //    }

    //    /**
    //     * @param TransportRequestEntity|TransportHistoryEntity $object
    //     * @param Request $request
    //     * @return TransportRequestEntity|TransportHistoryEntity
    //     */
    //    public function setData(TransportRequestEntity|TransportHistoryEntity $object, Request $request): TransportRequestEntity|TransportHistoryEntity
    //    {
    //        $requestData = $request->request->all();
    //
    //        $object->setSuId((int) $requestData['suId']);
    //        $object->setTrNr((int) $requestData['trNr']);
    //        $object->setTrPos((int) $requestData['trPos']);
    //        $object->setTrPrio((int) $requestData['trPrio']);
    //        $object->setArtNr((string) $requestData['articleNr']);
    //        $object->setTrQuantity((int) $requestData['trQuantity']);
    //        $object->setStockCoordinate((string) $requestData['stockCoordinate']);
    //        $object->setStockNr((int) $requestData['stockCoordinate']);
    //        $object->setStockLevel1((int) $requestData['stockLevel1']);
    //        $object->setStockLevel2((int) $requestData['stockLevel2']);
    //        $object->setStockLevel3((int) $requestData['stockLevel3']);
    //        $object->setStockLevel4((int) $requestData['stockLevel4']);
    //        $object->setTrAccess($requestData['trAccess']);
    //        $object->setTrState((int) $requestData['trState']);
    //        $object->setOrderUsername((string) $requestData['orderUsername']);
    //        $object->setBookingMethod($requestData['bookingMethod']);
    //        $object->setCharge($requestData['charge']);
    //        $object->setLoadingEquipment($requestData['loadingEquipment']);
    //        $object->setTrType((int) $requestData['trType']);
    //
    //        return $object;
    //    }
}
