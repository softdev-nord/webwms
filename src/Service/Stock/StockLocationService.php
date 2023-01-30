<?php

namespace WebWMS\Service\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\StockLocation;
use WebWMS\Exception\NotFoundException;
use WebWMS\Service\DataHandlers\Stock\StockLocationDataHandler;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationService
 */
class StockLocationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockLocationDataHandler $stockLocationDataHandler,
        private ContainerInterface $container
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function getAllStockLocations(): JsonResponse
    {
        $stockLocation = $this->stockLocationDataHandler->getAllStockLocation();

        if (!$stockLocation) {
            throw new NotFoundException('Keine Lagerorte gefunden');
        }

        return $stockLocation;
    }

    /**
     * @throws NotFoundException
     */
    public function getSockLocationDetailsByCoordinate($coordinate): array
    {
        return $this->stockLocationDataHandler->getSockLocationDetailsByCoordinate($coordinate);
    }

    /**
     * @throws NotFoundException
     */
    public function getSockLocationDetailsById($stockLocationId): array
    {
        return $this->stockLocationDataHandler->getSockLocationDetailsById($stockLocationId);
    }

    /**
     * @throws Exception
     */
    public function getAllStockLocationsForSelect(): array
    {
        return $this->stockLocationDataHandler->getAllStockLocationsForSelect();
    }

    public function generateStockLocation(Request $request): void
    {
        $this->stockLocationDataHandler->generateStockLocation($request);
    }

    public function getAllStockLocationsAjax(): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findAll();

        if (!$stockLocation) {
            throw new NotFoundException('Keine Lagerorte gefunden');
        }

        return $stockLocation;
    }

    public function getAllFreeStockLocations($stockSystem): array
    {
        return $this->stockLocationDataHandler->getAllFreeStockLocations($stockSystem);
    }

    public function getAllFreeStockLocationsWithLimit($stockSystem, $limit): array
    {
        return $this->stockLocationDataHandler->getAllFreeStockLocationsWithLimit($stockSystem, $limit);
    }

    public function getFirstFreeStockLocation($stockSystem, $limit): array
    {
        $freeStockLocation = [];
        $stockLocations = $this->getAllFreeStockLocationsWithLimit($stockSystem, $limit);

        foreach ($stockLocations as $stockLocation) {
            if (true !== $stockLocation['belegt']) {
                $freeStockLocation[] = $stockLocation;
            }
        }

        return $freeStockLocation;
    }

    public function getRemainder($stockLocation, $remainder): array
    {
        $freeStockLocations = [];

        if (isset($remainder)) {
            $freeStockLocations[] = [
                'ln' => $stockLocation['ln'],
                'fb' => $stockLocation['fb'],
                'sp' => $stockLocation['sp'],
                'tf' => $stockLocation['tf'],
                'lnKomplett' => $stockLocation['ln'].'-'.$stockLocation['fb'].'-'.$stockLocation['sp'].'-'.$stockLocation['tf'],
                'koordinate' => $stockLocation['koordinate'],
                'system' => $stockLocation['system'],
                'quantity' => $remainder,
            ];
        }

        return $freeStockLocations;
    }

    protected function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }
}
