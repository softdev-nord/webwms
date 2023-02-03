<?php

declare(strict_types=1);

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
     * @throws Exception
     */
    public function getAllStockLocations(): JsonResponse
    {
        return $this->stockLocationDataHandler->getAllStockLocation();
    }

    /**
     * @return array<int, StockLocation|null>
     */
    public function getSockLocationDetailsByCoordinate(string $coordinate): array
    {
        return $this->stockLocationDataHandler->getSockLocationDetailsByCoordinate($coordinate);
    }

    /**
     * @return object[]
     * @throws NotFoundException
     */
    public function getSockLocationDetailsById(string $stockLocationId): array
    {
        return $this->stockLocationDataHandler->getSockLocationDetailsById($stockLocationId);
    }

    /**
     * @return array<string|int|mixed>
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

    /**
     * @return object[]
     * @throws NotFoundException
     */
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

    /**
     * @return array<string|int|mixed>
     * @throws Exception
     */
    public function getAllFreeStockLocations(string $stockSystem): array
    {
        return $this->stockLocationDataHandler->getAllFreeStockLocations($stockSystem);
    }

    /**
     * @return array<string|int|mixed>
     * @throws Exception
     */
    public function getAllFreeStockLocationsWithLimit(string $stockSystem, int $limit): array
    {
        return $this->stockLocationDataHandler->getAllFreeStockLocationsWithLimit($stockSystem, $limit);
    }

    /**
     * @return object[]
     * @throws Exception
     */
    public function getFirstFreeStockLocation(string $stockSystem, int $limit): array
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

    public function updateStockLocation(Request $request): ?StockLocation
    {
        return $this->stockLocationDataHandler->updateStockLocation($request);
    }

    public function getStockLocationByCoordinate(int $stockLocationCoordinate): ?StockLocation
    {
        return $this->stockLocationDataHandler
            ->getStockLocationByCoordinate(
                $stockLocationCoordinate
            );
    }

    /**
     * @param  array<string>                         $stockLocation
     * @return array<int, array<string, int|string>>
     */
    public function getRemainder(array $stockLocation, int|null $remainder): array
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

    /**
     * @param array<string> $options
     */
    protected function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        /*
         * @phpstan-ignore-next-line
         */
        return $this->container->get('form.factory')->create($type, $data, $options);
    }
}
