<?php

declare(strict_types=1);

namespace WebWMS\Service\Inventory;

use DateTimeInterface;
use WebWMS\Entity\Inventory;
use WebWMS\Entity\InventoryCount;
use WebWMS\Entity\StockOccupancy;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\InventoryCountRepository;
use WebWMS\Repository\InventoryRepository;
use WebWMS\Repository\StockOccupancyRepository;
use WebWMS\Service\DateTimeService;

#[ClassInformation(
    package: 'WebWMS\Service\Inventory',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'InventoryService'
)]
readonly class InventoryService
{
    public function __construct(
        private InventoryRepository $inventoryRepository,
        private InventoryCountRepository $inventoryCountRepository,
        private StockOccupancyRepository $stockOccupancyRepository,
        private DateTimeService $dateTimeService,
    ) {
    }

    /**
     * Startet eine neue Inventur und erstellt Snapshot von aktuellen Beständen
     */
    public function startInventory(string $scope, string $username): Inventory
    {
        $inventory = (new Inventory())
            ->setInventoryNr($this->inventoryRepository->getNextInventoryNr())
            ->setStatus('open')
            ->setScope($scope)
            ->setStartDate($this->dateTimeService->createDateTime())
            ->setStartedBy($username)
            ->setCreatedAt($this->dateTimeService->createDateTime());

        $this->inventoryRepository->save($inventory);

        // Erstelle Snapshot von allen Beständen
        $this->createSnapshotForInventory($inventory);

        return $inventory;
    }

    /**
     * Erstellt Zählpositionen aus aktuellen Beständen
     */
    private function createSnapshotForInventory(Inventory $inventory): void
    {
        $occupancies = $this->stockOccupancyRepository->findAll();

        foreach ($occupancies as $occupancy) {
            $count = (new InventoryCount())
                ->setInventory($inventory)
                ->setStockLocationId($occupancy->getStockLocationId())
                ->setArticleId($occupancy->getArticleId())
                ->setArticleNr($occupancy->getArticleNr())
                ->setExpectedQuantity($occupancy->getInStock() + $occupancy->getIncomingStock())
                ->setCreatedAt($this->dateTimeService->createDateTime());

            $this->inventoryCountRepository->save($count);
        }
    }

    /**
     * Speichert gezählte Menge und berechnet Differenz
     */
    public function recordCount(InventoryCount $count, float $countedQuantity, string $username): void
    {
        $count->setCountedQuantity($countedQuantity)
            ->setCountedBy($username)
            ->setCountedAt($this->dateTimeService->createDateTime())
            ->setDifference($countedQuantity - $count->getExpectedQuantity());

        $this->inventoryCountRepository->save($count);
    }

    /**
     * Prüft ob Inventur komplett gezählt ist
     */
    public function isInventoryComplete(Inventory $inventory): bool
    {
        $uncounted = $this->inventoryCountRepository->findUncountedByInventory($inventory);
        return count($uncounted) === 0;
    }

    /**
     * Beendet Inventur und erzeugt Buchungen für Differenzen
     */
    public function completeInventory(Inventory $inventory, string $username): void
    {
        if (!$this->isInventoryComplete($inventory)) {
            throw new \InvalidArgumentException('Inventory is not complete yet');
        }

        $inventory->setStatus('completed')
            ->setEndDate($this->dateTimeService->createDateTime())
            ->setCompletedBy($username);

        $this->inventoryRepository->save($inventory);

        // TODO: Buche Differenzen als Korrekturbuchungen (T5.1 Bestandskorrektur)
    }
}

