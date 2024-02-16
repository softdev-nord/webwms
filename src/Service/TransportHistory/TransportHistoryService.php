<?php

declare(strict_types=1);

namespace WebWMS\Service\TransportHistory;

use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportHistoryEntity;
use WebWMS\Service\DataHandlers\TransportHistory\TransportHistoryDataHandler;

/**
 * @package:    WebWMS\Service\TransportHistoryEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportHistoryService
 */
class TransportHistoryService
{
    public function __construct(
        private readonly TransportHistoryDataHandler $transportHistoryDataHandler
    ) {
    }

    /**
     * @return array<object>
     */
    public function getTransportHistoryById(int $id): array
    {
        return $this->transportHistoryDataHandler
            ->getTransportHistoryById($id);
    }

    /**
     * @return  array<object>
     */
    public function getAllTransportHistories(): array
    {
        return $this->transportHistoryDataHandler
            ->getAllTransportHistories();
    }

    public function createTransportHistory(Request $request, string $user, string $clientIp): void
    {
        $this->transportHistoryDataHandler
            ->createTransportHistory($request, $user, $clientIp);
    }

    public function addTransportHistory(TransportHistoryEntity $transportHistoryEntity): void
    {
        $this->transportHistoryDataHandler
            ->addTransportHistory($transportHistoryEntity);
    }

    public function updateTransportHistory(TransportHistoryEntity $transportHistoryEntity): void
    {
        $this->transportHistoryDataHandler
            ->updateTransportHistory($transportHistoryEntity);
    }

    public function deleteTransportHistory(TransportHistoryEntity $transportHistoryEntity): void
    {
        $this->transportHistoryDataHandler
            ->deleteTransportHistory($transportHistoryEntity);
    }

    public function getLastStockUnit(): int
    {
        return $this->transportHistoryDataHandler
            ->getLastStockUnit();
    }

    public function getLastTransportHistoryNr(): int
    {
        return $this->transportHistoryDataHandler
            ->getLastTransportHistoryNr();
    }
}
