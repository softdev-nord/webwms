<?php

declare(strict_types=1);

namespace WebWMS\Service\TransportHistory;

use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\TransportHistory;
use WebWMS\Service\DataHandlers\TransportHistory\TransportHistoryDataHandler;

/**
 * @package:    WebWMS\Service\TransportHistory
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportHistoryService
 */
class TransportHistoryService
{
    public function __construct(
        private TransportHistoryDataHandler $transportHistoryDataHandler
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

    public function addTransportHistory(TransportHistory $transportHistory): void
    {
        $this->transportHistoryDataHandler
            ->addTransportHistory($transportHistory);
    }

    public function updateTransportHistory(TransportHistory $transportHistory): void
    {
        $this->transportHistoryDataHandler
            ->updateTransportHistory($transportHistory);
    }

    public function deleteTransportHistory(TransportHistory $transportHistory): void
    {
        $this->transportHistoryDataHandler
            ->deleteTransportHistory($transportHistory);
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
