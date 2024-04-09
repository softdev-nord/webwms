<?php

declare(strict_types=1);

namespace WebWMS\Service\TransportRequest;

use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportRequestEntity;
use WebWMS\Service\DataHandlers\TransportRequest\TransportRequestDataHandler;

/**
 * @package:    WebWMS\Service\TransportRequestEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequestService
 */
class TransportRequestService
{
    public function __construct(
        private readonly TransportRequestDataHandler $transportRequestDataHandler
    ) {
    }

    /**
     * @return array<object>
     */
    public function getTransportRequestById(int $id): array
    {
        return $this->transportRequestDataHandler
            ->getTransportRequestById($id);
    }

    /**
     * @return  array<object>
     */
    public function getAllOpenTransportRequests(): array
    {
        return $this->transportRequestDataHandler
            ->getAllOpenTransportRequests();
    }

    public function createTransportRequest(Request $request, string $user, string $clientIp): void
    {
        $this->transportRequestDataHandler
            ->createTransportRequest($request, $user, $clientIp);
    }

    public function addTransportRequest(TransportRequestEntity $transportRequestEntity): void
    {
        $this->transportRequestDataHandler
            ->addTransportRequest($transportRequestEntity);
    }

    public function updateTransportRequest(TransportRequestEntity $transportRequestEntity): void
    {
        $this->transportRequestDataHandler
            ->updateTransportRequest($transportRequestEntity);
    }

    public function deleteTransportRequest(TransportRequestEntity $transportRequestEntity): void
    {
        $this->transportRequestDataHandler
            ->deleteTransportRequest($transportRequestEntity);
    }

    /** @return TransportRequestEntity[] */
    public function getLastStockUnit(): array
    {
        return $this->transportRequestDataHandler
            ->getLastStockUnit();
    }

    public function getLastTransportRequestNr(): int
    {
        return $this->transportRequestDataHandler
            ->getLastTransportRequestNr();
    }
}
