<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\StockZoneEntity;
use WebWMS\Helper\FormHelper\StockZoneFormHelper;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockZoneService;
use WebWMS\Service\Validation\StockZoneValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneController
 */
class StockZoneController extends AbstractController
{
    public function __construct(
        private readonly StockZoneService $stockZoneService,
        private readonly RequirementsService $requirementsService,
        private readonly LoggingService $loggingService,
        private readonly StockZoneFormHelper $stockZoneFormHelper,
        private readonly StockZoneValidationService $stockZoneValidationService
    ) {
    }

    #[Route('/lagerzonen', name: 'stock_zone')]
    public function index(): Response
    {
        return $this->render(
            'stock/stock_zone/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Lagerzonen',
            ]
        );
    }

    #[Route('/lagerzone_anlegen', name: 'add_stock_zone')]
    public function addStockZone(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->stockZoneFormHelper->addStockZoneForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockZoneEntity $stockZoneRequestData */
            $stockZoneRequestData = $form->getData();
            $responseData = [];

            $responseData['message'] = 'Die Lagerzone ' . $stockZoneRequestData->getStockZoneShortDesc() . ' wurde erfolgreich angelegt.';
            $logMessage = 'Die Lagerzone ' . $stockZoneRequestData->getStockZoneShortDesc() . ' wurde angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->stockZoneService->addStockZone($stockZoneRequestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/stock_zone/stock_zone_add.html.twig',
            [
                'stockZoneForm' => $form->createView(),
                'editStockZone' => false,
            ]
        );
    }

    #[Route('/lagerzone_bearbeiten/stockZoneId/{stockZoneId}', name: 'edit_stock_layout')]
    public function editStockZone(Request $request, int $stockZoneId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $stockZone = $this->stockZoneService->getStockZoneById($stockZoneId);

        if (!$stockZone instanceof StockZoneEntity) {
            return null;
        }

        $form = $this->stockZoneFormHelper->editStockZoneForm($stockZone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockZoneEntity $stockZoneRequestData */
            $stockZoneRequestData = $form->getData();
            $responseData = $this->stockZoneValidationService->validateStockZoneData($stockZoneRequestData);

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Die Lagerzone ' . $stockZoneRequestData->getStockZoneShortDesc() . ' wurde erfolgreich geändert.';
                $logMessage = 'Die Lagerzone ' . $stockZoneRequestData->getStockZoneShortDesc() . ' wurde geändert.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->stockZoneService->updateStockZone($stockZoneRequestData);

                return new JsonResponse($responseData);
            }
        }

        return $this->render(
            'stock/stock_zone/stock_zone_edit.html.twig',
            [
                'stockZoneForm' => $form->createView(),
                'editStockZone' => true,
            ]
        );
    }

    #[Route('/lagerzone_löschen/stockZoneId/{stockZoneId}', name: 'delete_stock_zone')]
    public function deleteStockZone(Request $request, int $stockZoneId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $stockZone = $this->stockZoneService->getStockZoneById($stockZoneId);

        if (!$stockZone instanceof StockZoneEntity) {
            return null;
        }

        $form = $this->stockZoneFormHelper->deleteStockZoneForm($stockZone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockZoneEntity $stockZoneRequestData */
            $stockZoneRequestData = $form->getData();
            $responseData = [];

            $responseData['message'] = 'Die Lagerzone ' . $stockZoneRequestData->getStockZoneShortDesc() . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Die Lagerzone ' . $stockZoneRequestData->getStockZoneShortDesc() . ' wurde gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->stockZoneService->deleteStockZone($stockZoneRequestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/stock_zone/stock_layout_delete_ask.html.twig',
            [
                'stockZoneForm' => $form->createView(),
                'stockZoneShortDesc' => $stockZone->getStockZoneShortDesc(),
            ]
        );
    }

    #[Route('/stock_zone_ajax', name: 'stock_zone_ajax')]
    public function getAllStockZones(): JsonResponse
    {
        return $this->stockZoneService->getAllStockZones();
    }
}
