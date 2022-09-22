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
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getAllOpenTransportRequests(): array
    {
        return $this->entityManager->getRepository(TransportRequest::class)->findAll();
    }

    public function getTransportRequestById($id): array
    {
        return $this->entityManager->getRepository(TransportRequest::class)->findBy(['id' => $id]);
    }

    public function putTransportRequest()
    {
        // TODO: Implement logic
    }

    public function createTransportRequest(Request $request)
    {
        $requestData = $request->request->all();

        $createTransportRequest = new TransportRequest();
        $createTransportRequestEntry = $this->setData($createTransportRequest, $requestData);

        $this->entityManager->persist($createTransportRequestEntry);
        $this->entityManager->flush();
    }

    public function moveTransportRequestToTransportHistory(Request $request)
    {
        $requestData = $request->request->all();
        $transportRequestEntry = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findOneBy(['tr_nr' => $requestData['tr_nr']]);

        $transportHistoryObject = new TransportHistory();
        $transportHistoryEntry = $this->setData($transportHistoryObject, $requestData);

        $this->entityManager->persist($transportHistoryEntry);
        $this->entityManager->remove($transportRequestEntry);
        $this->entityManager->flush();
    }

    public function setData(TransportRequest|TransportHistory $object, $requestData): TransportRequest|TransportHistory
    {
        $object->setSuId($requestData['su_id']);
        $object->setTrNr($requestData['tr_nr']);
        $object->setTrPos($requestData['tr_pos']);
        $object->setTrPrio($requestData['tr_prio']);
        $object->setArtNr($requestData['article_nr']);
        $object->setTrQuantity($requestData['tr_quantity']);
        $object->setStockCoordinate($requestData['stock_coordinate']);
        $object->setStockNr($requestData['stock_nr']);
        $object->setStockLevel1($requestData['stock_level1']);
        $object->setStockLevel2($requestData['stock_level2']);
        $object->setStockLevel3($requestData['stock_level3']);
        $object->setStockLevel4($requestData['stock_level3']);
        $object->setTrAccess($requestData['tr_access']);
        $object->setTrDispatch($requestData['tr_dispatch']);
        $object->setTrState($requestData['tr_state']);
        $object->setOrderUsername($requestData['order_username']);
        $object->setBookingMethod($requestData['booking_method']);
        $object->setOrderNr($requestData['order_nr']);
        $object->setOrderPos($requestData['order_pos']);
        $object->setCharge($requestData['charge']);
        $object->setLoadingEquipment($requestData['loading_equipment']);
        $object->setConfirmationState($requestData['confirmation_state']);
        $object->setTrUsername($requestData['tr_username']);
        $object->setTrComputerIp($requestData['tr_computer_ip']);
        $object->setTrBlocked($requestData['tr_blocked']);
        $object->setTrStartDate($requestData['tr_start_date']);
        $object->setTrEdited($requestData['tr_edited']);
        $object->setTrTyp($requestData['tr_typ']);

        return $object;
    }

    public function getLastStockUnit(): int
    {
        $result = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findBy([], ['suId' => 'DESC'], 1, 0);

        if (!$result) {
            $result = $this->entityManager
                ->getRepository(TransportHistory::class)
                ->findBy([], ['suId' => 'DESC'], 1, 0);
        }

        return $result[0]->getSuId();
    }

    public function getLastTransportRequestNr(): int
    {
        $result = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findBy([], ['tr_nr' => 'DESC'], 1, 0);

        if (!$result) {
            $result = $this->entityManager
                ->getRepository(TransportHistory::class)
                ->findBy([], ['tr_nr' => 'DESC'], 1, 0);
        }

        return $result[0]->getTrNr();
    }
}
