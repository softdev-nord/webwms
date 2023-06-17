<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\TransportRequest;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportRequest;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\TransportRequest
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequestDataHandler
 */
class TransportRequestDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
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

    /**
     * @return array<object>
     */
    public function getTransportRequestById(int $id): array
    {
        return $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findBy(['id' => $id]);
    }

    /**
     * @return array<object>
     */
    public function getAllOpenTransportRequests(): array
    {
        return $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findAll();
    }

    public function createTransportRequest(Request $request, string $user, string $clientIp): void
    {
        $requestData = (array) $request->request->all()['stock_in_final'];
        $articleNr = $requestData['article_nr'];
        $bookingMethod = $requestData['booking_method'];
        $charge = $requestData['charge'];
        $loadingEquipment = $requestData['loading_equipment'];
        $transportRequest = new TransportRequest();

        foreach ($requestData as $key => $data) {
            $transportRequest->setSuId($this->getLastStockUnit());
            $transportRequest->setTrNr($this->getLastTransportRequestNr());
            $transportRequest->setTrPos($key + 1);
            $transportRequest->setTrPrio(0);
            $transportRequest->setArticleNr(strval($articleNr));
            $transportRequest->setTrQuantity(floatval($data['stock_quantity']));
            $transportRequest->setStockCoordinate(strval($data['stock_coordinate']));
            $transportRequest->setStockNr(intval($data['stock_ln']));
            $transportRequest->setStockLevel1(intval($data['stock_fb']));
            $transportRequest->setStockLevel2(intval($data['stock_sp']));
            $transportRequest->setStockLevel3(intval($data['stock_tf']));
            $transportRequest->setStockLevel4(1);
            $transportRequest->setTrAccess($this->dateTimeService->createDateTime());
            $transportRequest->setTrState(0);
            $transportRequest->setOrderUsername($user);
            $transportRequest->setBookingMethod(strval($bookingMethod));
            $transportRequest->setDocId(null);
            $transportRequest->setCharge(strval($charge));
            $transportRequest->setTrComputerIp(strval($clientIp));
            $transportRequest->setLoadingEquipment(strval($loadingEquipment));
            $transportRequest->setTrType(1);
        }

        $this->save($transportRequest);
    }

    public function getLastStockUnit(): int
    {
        $lastStockUnitTr = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findBy([], ['suId' => 'DESC'], 1, 0);

        return $lastStockUnitTr[0]->getSuId();
    }

    public function getLastTransportRequestNr(): int
    {
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

    //    public function moveTransportRequestToTransportHistory(Request $request): void
//    {
//        $requestData = $request->request->all();
//        $trNr = (string) $requestData['tr_nr'];
//        $transportRequestEntry = $this->entityManager
//            ->getRepository(TransportRequest::class)
//            ->findOneBy(['tr_nr' => $trNr]);
//
//        $transportHistoryObject = new TransportHistory();
//        $transportHistoryEntry = $this->setData($transportHistoryObject, $request);
//
//        $this->entityManager->persist($transportHistoryEntry);
//        $this->entityManager->remove($transportRequestEntry);
//        $this->entityManager->flush();
//    }

//    /**
//     * @param TransportRequest|TransportHistory $object
//     * @param Request $request
//     * @return TransportRequest|TransportHistory
//     */
//    public function setData(TransportRequest|TransportHistory $object, Request $request): TransportRequest|TransportHistory
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
