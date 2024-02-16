<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\StockLocationEntity;
use WebWMS\Helper\FormHelper\StockLocationFormHelper;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\Validation\StockLocationValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationController
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockLocationController extends AbstractController
{
    public function __construct(
        private readonly StockLocationService $stockLocationService,
        private readonly RequirementsService $requirementsService,
        private readonly StockLocationValidationService $stockLocationValidationService,
        private readonly LoggingService $loggingService,
        private readonly StockLocationFormHelper $stockLocationFormHelper
    ) {
    }

    #[Route('/lagerplatz', name: 'stock_location')]
    public function index(): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'stock/stock_location/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Lagerplätze',
            ]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/lagerplatz_anlegen', name: 'add_stock_location')]
    public function addStockLocation(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->stockLocationFormHelper->addStockLocationForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockLocationEntity $stockLocationRequestData */
            $stockLocationRequestData = $form->getData();
            $responseData = $this->stockLocationValidationService
                ->validateStockLocationData(
                    (array) $request->request->all()['add_stock_location']
                );

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Die Lagerplätze für das Lager ' . $stockLocationRequestData->getStockLocationLn() . 'wurden erfolgreich angelegt.';
                $logMessage = 'Die Lagerplätze für das Lager ' . $stockLocationRequestData->getStockLocationLn() . ' wurden angelegt.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->stockLocationService->addStockLocation($stockLocationRequestData);

                return new JsonResponse($responseData);
            }

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/stock_location/stock_location_add.html.twig',
            [
                'stockLocationForm' => $form->createView(),
                'editStockLocation' => false,
            ]
        );
    }

    #[Route('lagerplatz_bearbeiten/koordinate/{stockLocationCoordinate}', name: 'edit_stock_location')]
    public function editStockLocation(Request $request, string $stockLocationCoordinate): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $stockLocation = $this->stockLocationService->getStockLocationByCoordinate($stockLocationCoordinate);

        if (!$stockLocation instanceof StockLocationEntity) {
            return null;
        }

        $form = $this->stockLocationFormHelper->editStockLocationForm($stockLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockLocationEntity $stockLocationRequestData */
            $stockLocationRequestData = $form->getData();
            $responseData = $this->stockLocationValidationService
                ->validateStockLocationData(
                    (array) $request->request->all()['edit_stock_location']
                );
            $selectedStockLocation = $stockLocationRequestData->getStockLocationLn()
                . '-' . $stockLocationRequestData->getStockLocationFb()
                . '-' . $stockLocationRequestData->getStockLocationSp()
                . '-' . $stockLocationRequestData->getStockLocationTf();

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Der Lagerplatz ' . $selectedStockLocation . ' wurde erfolgreich geändert.';
                $logMessage = 'Der Lagerplatz ' . $selectedStockLocation . ' wurde geändert.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->stockLocationService->updateStockLocation($stockLocationRequestData);

                return new JsonResponse($responseData);
            }

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/stock_location/stock_location_edit.html.twig',
            [
                'stockLocationForm' => $form->createView(),
                'stockLocations' => json_decode((string) $this->getAllStockLocations()->getContent()),
                'editStockLocation' => true,
            ]
        );
    }

    #[Route('lagerplatz_löschen/koordinate/{stockLocationCoordinate}', name: 'delete_stock_location')]
    public function deleteStockLocation(Request $request, string $stockLocationCoordinate): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $stockLocation = $this->stockLocationService->getStockLocationByCoordinate($stockLocationCoordinate);

        if (!$stockLocation instanceof StockLocationEntity) {
            return null;
        }

        $form = $this->stockLocationFormHelper->deleteStockLocationForm($stockLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockLocationEntity $stockLocationRequestData */
            $stockLocationRequestData = $form->getData();
            $selectedStockLocation = $stockLocationRequestData->getStockLocationLn()
                . '-' . $stockLocationRequestData->getStockLocationFb()
                . '-' . $stockLocationRequestData->getStockLocationSp()
                . '-' . $stockLocationRequestData->getStockLocationTf();
            $responseData = [];

            $responseData['message'] = 'Der Lagerplatz ' . $selectedStockLocation . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Der Lagerplatz ' . $selectedStockLocation . ' wurde geändert.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->stockLocationService->deleteStockLocation($stockLocationRequestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/stock_location/stock_location_delete_ask.html.twig',
            [
                'stockLocationForm' => $form->createView(),
                'stockLocation' => $stockLocation->getStockLocationLn()
                    . '-' . $stockLocation->getStockLocationFb()
                    . '-' . $stockLocation->getStockLocationSp()
                    . '-' . $stockLocation->getStockLocationTf(),

            ]
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/stock_location_ajax', name: 'stock_location_ajax')]
    public function getAllStockLocations(): JsonResponse
    {
        return $this->stockLocationService->getAllStockLocations();
    }

    /**
     * @throws \Exception
     * @return array|object[]
     */
    #[Route('/lagerplatz_details/{stock_location_coordinate}', name: 'show_stock_location_details')]
    public function getStockLocationDetailsById(string $coordinate): array
    {
        return $this->stockLocationService->getStockLocationDetailsById($coordinate);
    }

    /**
     * @param array<string> $freeStockLocations
     */
    #[Route('/selected_stock_locations', name: 'selected_stock_locations')]
    public function getSelectedStocklocations(array $freeStockLocations): JsonResponse
    {
        return new JsonResponse($freeStockLocations);
    }
}
