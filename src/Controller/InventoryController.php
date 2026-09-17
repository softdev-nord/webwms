<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\Inventory;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\InventoryCountRepository;
use WebWMS\Repository\InventoryRepository;
use WebWMS\Service\Inventory\InventoryService;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'InventoryController'
)]
#[Route('/inventory')]
class InventoryController extends AbstractController
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly InventoryRepository $inventoryRepository,
        private readonly InventoryCountRepository $inventoryCountRepository,
    ) {
    }

    /**
     * Startet eine neue Inventur
     */
    #[Route('/start', name: 'inventory_start', methods: ['POST'])]
    public function startInventory(Request $request): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $scope = $request->request->getString('scope', 'full');
            $username = $this->getUser()->getUserIdentifier();

            $inventory = $this->inventoryService->startInventory($scope, $username);

            return new JsonResponse([
                'success' => true,
                'message' => 'Inventory started successfully',
                'inventory_id' => $inventory->getId(),
                'inventory_nr' => $inventory->getInventoryNr(),
                'status' => $inventory->getStatus(),
            ]);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Speichert gezählte Menge für eine Position
     */
    #[Route('/count/{countId}', name: 'inventory_record_count', methods: ['POST'])]
    public function recordCount(int $countId, Request $request): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $count = $this->inventoryCountRepository->find($countId);
            if (!$count) {
                return new JsonResponse(['error' => 'Count position not found'], 404);
            }

            $countedQuantity = (float) $request->request->getString('counted_quantity', '0');
            $username = $this->getUser()->getUserIdentifier();

            $this->inventoryService->recordCount($count, $countedQuantity, $username);

            return new JsonResponse([
                'success' => true,
                'message' => 'Count recorded',
                'counted_quantity' => $count->getCountedQuantity(),
                'difference' => $count->getDifference(),
            ]);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Prüft Status der Inventur
     */
    #[Route('/{inventoryId}/status', name: 'inventory_status', methods: ['GET'])]
    public function getInventoryStatus(int $inventoryId): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $inventory = $this->inventoryRepository->find($inventoryId);
            if (!$inventory) {
                return new JsonResponse(['error' => 'Inventory not found'], 404);
            }

            $counts = $this->inventoryCountRepository->findByInventory($inventory);
            $countedCount = count(array_filter($counts, fn($c) => $c->getCountedQuantity() !== null));
            $totalCount = count($counts);

            $hasDifferences = count(array_filter($counts, fn($c) => $c->getDifference() !== null && $c->getDifference() !== 0.0)) > 0;

            return new JsonResponse([
                'inventory_id' => $inventory->getId(),
                'inventory_nr' => $inventory->getInventoryNr(),
                'status' => $inventory->getStatus(),
                'is_complete' => $this->inventoryService->isInventoryComplete($inventory),
                'counted' => $countedCount,
                'total' => $totalCount,
                'has_differences' => $hasDifferences,
            ]);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Beendet Inventur und bucht Differenzen
     */
    #[Route('/{inventoryId}/complete', name: 'inventory_complete', methods: ['POST'])]
    public function completeInventory(int $inventoryId, Request $request): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $inventory = $this->inventoryRepository->find($inventoryId);
            if (!$inventory) {
                return new JsonResponse(['error' => 'Inventory not found'], 404);
            }

            $username = $this->getUser()->getUserIdentifier();
            $this->inventoryService->completeInventory($inventory, $username);

            return new JsonResponse([
                'success' => true,
                'message' => 'Inventory completed successfully',
                'inventory_nr' => $inventory->getInventoryNr(),
                'status' => $inventory->getStatus(),
            ]);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Gibt alle Zählpositionen für eine Inventur zurück
     */
    #[Route('/{inventoryId}/counts', name: 'inventory_get_counts', methods: ['GET'])]
    public function getInventoryCounts(int $inventoryId): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $inventory = $this->inventoryRepository->find($inventoryId);
            if (!$inventory) {
                return new JsonResponse(['error' => 'Inventory not found'], 404);
            }

            $counts = $this->inventoryCountRepository->findByInventory($inventory);
            $data = array_map(fn($c) => [
                'id' => $c->getId(),
                'article_nr' => $c->getArticleNr(),
                'expected_quantity' => $c->getExpectedQuantity(),
                'counted_quantity' => $c->getCountedQuantity(),
                'difference' => $c->getDifference(),
                'counted_by' => $c->getCountedBy(),
                'counted_at' => $c->getCountedAt()?->format('Y-m-d H:i:s'),
            ], $counts);

            return new JsonResponse(['counts' => $data]);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}

