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

    public function getSockLocationDetailsById($coordinate): array
    {
        $stockLocation = $this->entityManager
            ->getRepository(StockLocation::class)
            ->findOneBy(['stock_location_coordinate' => $coordinate]);

        if (!$stockLocation) {
            throw new NotFoundException('Keine Details für den gewählten Lagerort gefunden.');
        }

        return (array) $stockLocation;
    }

    /**
     * @throws Exception
     */
    public function getAllStockLocationsForSelect(): array
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('stock_location_ln')
            ->from('stock_location')
            ->groupBy('stock_location_ln');

        $stmt = $queryBuilder->executeQuery();

        return $stmt->fetchAllAssociative();
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function generateStockLocation(Request $request)
    {
        $createdAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $stockLocations = $this->generateStockLocationValues($request);

        if (isset($request->request->all()['stock_location']['stock_location_check'])) {
            foreach ($stockLocations as $stockLocation) {
                $setStockLocations = new StockLocation();
                $setStockLocations->setStockLocationLn((int) $stockLocation['stock_location_ln']);
                $setStockLocations->setStockLocationFb((int) $stockLocation['stock_location_fb']);
                $setStockLocations->setStockLocationSp((int) $stockLocation['stock_location_sp']);
                $setStockLocations->setStockLocationTf((int) $stockLocation['stock_location_tf']);
                $setStockLocations->setStockLocationCoordinate((int) $stockLocation['stock_location_coordinate']);
                $setStockLocations->setStockLocationDesc($stockLocation['stock_location_desc']);
                $setStockLocations->setStockLocationWidth($stockLocation['stock_location_width']);
                $setStockLocations->setStockLocationDepth($stockLocation['stock_location_depth']);
                $setStockLocations->setStockLocationHeight($stockLocation['stock_location_height']);
                $setStockLocations->setStockLocationZone($stockLocations['stock_location_zone']);
                $setStockLocations->setStockLocationCreatedAt($createdAt);

                $this->entityManager->persist($setStockLocations);
                $this->entityManager->flush();
            }
        } else {
            $setStockLocations = new StockLocation();
            $setStockLocations->setStockLocationLn((int) $stockLocations['stock_location_ln']);
            $setStockLocations->setStockLocationFb((int) $stockLocations['stock_location_fb']);
            $setStockLocations->setStockLocationSp((int) $stockLocations['stock_location_sp']);
            $setStockLocations->setStockLocationTf((int) $stockLocations['stock_location_tf']);
            $setStockLocations->setStockLocationCoordinate((int) $stockLocations['stock_location_coordinate']);
            $setStockLocations->setStockLocationDesc($stockLocations['stock_location_desc']);
            $setStockLocations->setStockLocationWidth($stockLocations['stock_location_width']);
            $setStockLocations->setStockLocationDepth($stockLocations['stock_location_depth']);
            $setStockLocations->setStockLocationHeight($stockLocations['stock_location_height']);
            $setStockLocations->setStockLocationZone($stockLocations['stock_location_zone']);
            $setStockLocations->setStockLocationCreatedAt($createdAt);

            $this->entityManager->persist($setStockLocations);
            $this->entityManager->flush();
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function generateStockLocationValues($request): array
    {
        $stockLocations = [];
        $stockLocation = $request->request->all()['stock_location'];

        if (!isset($request->request->all()['stock_location']['stock_location_check'])) {
            $stockLocations['stock_location_ln'] = $stockLocation['stock_location_ln'];
            $stockLocations['stock_location_fb'] = (string) $stockLocation['stock_location_fb'];
            $stockLocations['stock_location_sp'] = (string) $stockLocation['stock_location_sp'];
            $stockLocations['stock_location_tf'] = (string) $stockLocation['stock_location_tf'];
            $stockLocations['stock_location_coordinate'] =
                $stockLocation['stock_location_ln']
                .$this->generateStockCoordinateLevel((string) $stockLocation['stock_location_fb'])
                .$this->generateStockCoordinateLevel((string) $stockLocation['stock_location_sp'])
                .$this->generateStockCoordinateLevel((string) $stockLocation['stock_location_tf'])
            ;
            $stockLocations['stock_location_desc'] = $stockLocation['stock_location_desc'];
            $stockLocations['stock_location_width'] = $stockLocation['stock_location_width'];
            $stockLocations['stock_location_depth'] = $stockLocation['stock_location_depth'];
            $stockLocations['stock_location_height'] = $stockLocation['stock_location_height'];
        } else {
            for ($fbn = 1; $fbn <= $stockLocation['stock_location_fb']; ++$fbn) {
                for ($spn = 1; $spn <= $stockLocation['stock_location_sp']; ++$spn) {
                    for ($tfn = 1; $tfn <= $stockLocation['stock_location_tf']; ++$tfn) {
                        $generatedStockLocation['stock_location_ln'] = $stockLocation['stock_location_ln'];
                        $generatedStockLocation['stock_location_fb'] = (string) $fbn;
                        $generatedStockLocation['stock_location_sp'] = (string) $spn;
                        $generatedStockLocation['stock_location_tf'] = (string) $tfn;
                        $generatedStockLocation['stock_location_coordinate'] =
                            $stockLocation['stock_location_ln']
                            .$this->generateStockCoordinateLevel((string) $fbn)
                            .$this->generateStockCoordinateLevel((string) $spn)
                            .$this->generateStockCoordinateLevel((string) $tfn)
                        ;
                        $generatedStockLocation['stock_location_desc'] = $stockLocation['stock_location_desc'];
                        $generatedStockLocation['stock_location_width'] = $stockLocation['stock_location_width'];
                        $generatedStockLocation['stock_location_depth'] = $stockLocation['stock_location_depth'];
                        $generatedStockLocation['stock_location_height'] = $stockLocation['stock_location_height'];
                        $stockLocations[] = $generatedStockLocation;
                    }
                }
            }
        }

        return $stockLocations;
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

    public function getFreeStockLocations($stockSystem, $limit): array
    {
        return $this->stockLocationDataHandler->getAllStockLocations($stockSystem, $limit);
    }

    public function getFirstFreeStockLocation($stockSystem, $limit): array
    {
        $freeStockLocation = [];
        $stockLocations = $this->getFreeStockLocations($stockSystem, $limit);

        foreach ($stockLocations as $stockLocation) {
            if (true !== $stockLocation['belegt']) {
                $freeStockLocation[] = $stockLocation;
            }
        }

        return $freeStockLocation;
    }

    public function generateStockCoordinateLevel($string): string
    {
        return str_pad($string, 4, '0', STR_PAD_LEFT);
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
