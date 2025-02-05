<?php

declare(strict_types=1);

namespace WebWMS\Service\TransportRequest;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportRequest;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\TransportRequest\TransportRequestDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service\TransportRequest',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'TransportRequestService'
)]
readonly class TransportRequestService
{
    public function __construct(
        private TransportRequestDataHandler $transportRequestDataHandler,
    ) {
    }

    public function getTransportRequestById(int $id): ?TransportRequest
    {
        return $this->transportRequestDataHandler
            ->getTransportRequestById($id);
    }

    /**
     * @throws Exception
     */
    public function getAllOpenTransportRequests(): JsonResponse
    {
        return $this->transportRequestDataHandler
            ->getAllOpenTransportRequests();
    }

    public function createTransportRequest(Request $request, string $user, string $clientIp): void
    {
        $this->transportRequestDataHandler
            ->createTransportRequest($request, $user, $clientIp);
    }

    public function addTransportRequest(TransportRequest $transportRequest): void
    {
        $this->transportRequestDataHandler
            ->addTransportRequest($transportRequest);
    }

    public function updateTransportRequest(TransportRequest $transportRequest): void
    {
        $this->transportRequestDataHandler
            ->updateTransportRequest($transportRequest);
    }

    public function deleteTransportRequest(TransportRequest $transportRequest): void
    {
        $this->transportRequestDataHandler
            ->deleteTransportRequest($transportRequest);
    }

    /**
     * @return TransportRequest[]
     */
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
