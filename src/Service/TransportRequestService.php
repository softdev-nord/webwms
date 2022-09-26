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
        $transportRequest = new TransportRequest();

        $requestData = $request->request->all();
        dd($requestData['stock_in_final']);
        foreach ($requestData['stock_in_final'] as $key => $data) {
            $transportRequest->setSuId((int) $data[$key]['stock_su_id']);
            $transportRequest->setTrNr($this->getLastTransportRequestNr() + 1);
            $transportRequest->setTrPos((int) $key + 1);
            $transportRequest->setTrPrio(0);
            $transportRequest->setArtNr($requestData['article_nr']);
            $transportRequest->setTrQuantity($data['stock_quantity']);
            $transportRequest->setStockCoordinate($data['stock_coordinate']);
            $transportRequest->setStockNr($data['stock_ln']);
            $transportRequest->setStockLevel1($data['stock_fb']);
            $transportRequest->setStockLevel2($data['stock_sp']);
            $transportRequest->setStockLevel3($data['stock_tf']);
            $transportRequest->setStockLevel4(1);
            $transportRequest->setTrAccess(new \DateTime('NOW', new \DateTimeZone('Europe/Berlin')));
            $transportRequest->setTrState(0);
            $transportRequest->setOrderUsername($request->getSession()->all()['_security.last_username']);
            $transportRequest->setBookingMethod($data['booking_method']);
            $transportRequest->setCharge($data['charge']);
            $transportRequest->setLoadingEquipment($data['loading_equipment']);
            $transportRequest->setTrTyp('1');
        }

        $createTransportRequestEntry = $this->setData($transportRequest, $request);

        dd($createTransportRequestEntry);

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
        $transportHistoryEntry = $this->setData($transportHistoryObject, $request);

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
        } else {
            return $lastStockUnitTh[0]->getSuId();
        }
    }

    public function getLastTransportRequestNr(): int
    {
        $result = $this->entityManager
            ->getRepository(TransportRequest::class)
            ->findBy([], ['trNr' => 'DESC'], 1, 0);

        if (!$result) {
            $result = $this->entityManager
                ->getRepository(TransportHistory::class)
                ->findBy([], ['trNr' => 'DESC'], 1, 0);
        }

        return $result[0]->getTrNr();
    }
}
