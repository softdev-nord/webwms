<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportHistory;
use WebWMS\Entity\TransportRequest;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        TransportRequestService
 */
class TransportRequestService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    /**
     * @return object[]
     */
    public function getAllOpenTransportRequests(): array
    {
        return $this->entityManager->getRepository(
            TransportRequest::class
        )->findAll();
    }

    /**
     * @return object[]
     */
    public function getTransportRequestById(int $id): array
    {
        return $this->entityManager->getRepository(TransportRequest::class)->findBy(['id' => $id]);
    }

    public function putTransportRequest(): void
    {
        // TODO: Implement logic
    }

    public function createTransportRequest(Request $request, string $user): void
    {
        $requestData = (array) $request->request->all()['stock_in_final'];
        $articleNr = $requestData['article_nr'];
        $bookingMethod = $requestData['booking_method'];
        $charge = $requestData['charge'];
        $loadingEquipment = $requestData['loading_equipment'];
        $clientIp = $request->getClientIp();
        $transportRequest = new TransportRequest();

        foreach ($requestData['stock_in_final'] as $key => $data) {
            $transportRequest->setSuId(intval($data['stock_su_id']));
            $transportRequest->setTrNr($this->getLastTransportRequestNr() + 1);
            $transportRequest->setTrPos($key + 1);
            $transportRequest->setTrPrio(0);
            $transportRequest->setArtNr(strval($articleNr));
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

        $this->entityManager->persist($transportRequest);
        $this->entityManager->flush();
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

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getLastStockUnit(): int
    {
        $lastStockUnitTr = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findBy([], ['suId' => 'DESC'], 1, 0);

        $lastStockUnitTh = $this->entityManager
            ->getRepository(TransportHistory::class)
            ->findBy([], ['suId' => 'DESC'], 1, 0);

        if ($lastStockUnitTr > $lastStockUnitTh) {
            return $lastStockUnitTr[0]->getSuId();
        }

        return $lastStockUnitTh[0]->getSuId();
    }

    public function getLastTransportRequestNr(): int
    {
        $result = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findBy([], ['trNr' => 'DESC'], 1, 0);

        if ($result == null) {
            $result = $this->entityManager
                ->getRepository(TransportHistory::class)
                ->findBy([], ['trNr' => 'DESC'], 1, 0);
        }

        return $result[0]->getTrNr();
    }
}
