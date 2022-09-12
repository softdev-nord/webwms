<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Exception\NotFoundException;
use WebWMS\Form\EditStockLocationType;
use WebWMS\Form\StockLocationType;
use WebWMS\Service\DataHandlers\Stock\StockLocationDataHandler;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\Validation\StockLocationValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocation
 */
class StockLocation extends AbstractController
{
    public function __construct(
        private StockLocationService $stockLocationService,
        private Requirements $requirements,
        private StockLocationValidationService $stockLocationValidationService,
        private StockLocationDataHandler $stockLocationDataHandler
    ) {
    }

    #[Route('/lagerplatz', name: 'stock_location')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'stock/stock_location.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerplätze',
            ]
        );
    }

    #[Route('/lagerplatz_anlegen', name: 'add_stock_location')]
    public function addNewStockLocation(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(StockLocationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->stockLocationService->generateStockLocation($request);
            $this->addFlash('success', 'Die Lagerplätze wurden erfolgreich angelegt.');

            return $this->redirectToRoute('add_stock_location');
        }

        return $this->render(
            'stock/add_new_stock_location.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerplatz anlegen',
                'stockLocationForm' => $form->createView(),
            ]
        );
    }

    #[Route('lagerplatz_bearbeiten/koordinate/{stockLocationCoordinate}', name: 'edit_stock_location')]
    public function editStockLocation(Request $request, $stockLocationCoordinate): RedirectResponse|JsonResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestData = $request->request->all();

        if (!empty($requestData)) {
            $requestData = $requestData['edit_stock_location'];
        }

        $responseData = $this->stockLocationValidationService->validateStockLocationData($requestData);
        $responseData['message'] = '';

        $stockLocation = $this->stockLocationDataHandler->getStockLocationByCoordinate((int) $stockLocationCoordinate);
        $form = $this->createForm(EditStockLocationType::class, $stockLocation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Die Änderungen am Lagerplatz wurden erfolgreich gespeichert.';
                $this->stockLocationDataHandler->updateStockLocation($requestData);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Lagerplatz konnte nicht gespeichert werden.';

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/edit_stock_location.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lagerplatz bearbeiten',
                'editStockLocationForm' => $form->createView(),
                'stockLocations' => json_decode($this->getAllStockLocations()->getContent()),
            ]
        );
    }

    #[Route('/stock_location_ajax', name: 'stock_location_ajax')]
    public function getAllStockLocations(): JsonResponse
    {
        return $this->stockLocationService->getAllStockLocations();
    }

    /**
     * @throws NotFoundException
     */
    #[Route('/lagerplatz_details/{stock_location_coordinate}', name: 'show_stock_location_details')]
    public function getSockLocationDetailsById($coordinate): array
    {
        return $this->stockLocationService->getSockLocationDetailsById($coordinate);
    }

    #[Route('/selected_stock_locations', name: 'selected_stock_locations')]
    public function getSelectedStocklocations($freeStockLocations): JsonResponse
    {
        return new JsonResponse($freeStockLocations);
    }
}
