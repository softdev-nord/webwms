<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\StockLocationType;
use WebWMS\Services\StockService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Stock
 */
class Stock extends AbstractController
{
    /** @var StockService */
    private $stockService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        StockService $stockService,
        Requirements $requirements
    ) {
        $this->stockService = $stockService;
        $this->requirements = $requirements;
    }

    /**
     * @return StockLocation[]
     */
    public function getAllStockLocations(): array
    {
        return $this->stockService->getAllStockLocations();
    }

    /**
     * @Route("/stock_rotation_ajax", name="stock_rotation")
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getAllStockRotations(): JsonResponse
    {
        return $this->stockService->getAllStockRotationsWithJoin();
    }

    /**
     * @Route("/stock_occupancy_ajax", name="stock_occupancy_ajax")
     */
    public function getAllStockOccupancy(): JsonResponse
    {
        return $this->stockService->getAllStockOccupancy();
    }

    public function addNewStockRotation(Request $request)
    {
        $form = $this->createForm(StockLocationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->stockService->setStockRotation($data);
            $this->addFlash('success', 'Der Auftrag und die Position(en) wurden erfolgreich angelegt.');

            return $this->redirectToRoute('new_customer_order');
        }

        return $this->render('customer_order/add_customer_order.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerplatz anlegen',
                'setStockRotationForm' => $form->createView(),
            ]
        );
    }

    public function generateStockCoordinate(array $stockLocation): string
    {
        return $stockLocation['stock_location_ln'] .
               $stockLocation['stock_location_fb'] .
               $stockLocation['stock_location_sp'] .
               $stockLocation['stock_location_tf'];

    }

    public function getFreeStockLocation()
    {
        // TODO: Implement logic
    }

    public function getFiFo()
    {
        // TODO: Implement logic
    }

    /**
     * @Route("/lagerplatz", name="stock_location")
     */
    public function stockLocations(): Response
    {
        return $this->render('stock/stock_location.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerplätze',
                'stockLocation' => $this->getAllStockLocations(),
            ]
        );
    }

    /**
     * @Route("/lagerbewegung", name="stock_rotation")
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function stockRotations(): Response
    {
        return $this->render('stock/stock_rotation.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerbewegungen',
                'stockRotation' => $this->getAllStockRotations(),
            ]
        );
    }

    /**
     * @Route("/lagerbelegung", name="stock_occupancy")
     */
    public function stockOccupancy(): Response
    {
        return $this->render('stock/stock_occupancy.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'page' => 'Lagerbelegungen',
                'stockOccupancy' => $this->getAllStockOccupancy(),
            ]
        );
    }
}
