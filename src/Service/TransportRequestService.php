<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
    )
    {
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
        $createTransportRequest = new TransportRequest();

        $createTransportRequest->setSuId();
        $createTransportRequest->setTrNr();
        $createTransportRequest->setTrPos();
        $createTransportRequest->setTrPrio();
        $createTransportRequest->setArtNr();
        $createTransportRequest->setTrQuantity();
        $createTransportRequest->setStockCoordinate();
        $createTransportRequest->setStockNr();
        $createTransportRequest->setStockLevel1();
        $createTransportRequest->setStockLevel2();
        $createTransportRequest->setStockLevel3();
        $createTransportRequest->setStockLevel4();
        $createTransportRequest->setTrAccess();
        $createTransportRequest->setTrDispatch();
        $createTransportRequest->setTrState();
        $createTransportRequest->setOrderUsername();
        $createTransportRequest->setOrderNr();
        $createTransportRequest->setOrderPos();
        $createTransportRequest->setCharge();
        $createTransportRequest->setLoadingEquipment();
        $createTransportRequest->setConfirmationState();
        $createTransportRequest->setTrUsername();
        $createTransportRequest->setTrComputerIp();
        $createTransportRequest->setTrBlocked();
        $createTransportRequest->setTrStartDate();
        $createTransportRequest->setTrEdited();
        $createTransportRequest->setTrTyp();

        $this->entityManager->persist($createTransportRequest);
        $this->entityManager->flush();
    }

    public function moveTransportRequestToTransportHistory()
    {
        // TODO: Implement logic
    }
}
