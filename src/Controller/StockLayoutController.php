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
use WebWMS\Entity\StockLayoutEntity;
use WebWMS\Helper\FormHelper\StockLayoutFormHelper;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLayoutService;
use WebWMS\Service\Validation\StockLayoutValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutController
 */
class StockLayoutController extends AbstractController
{
    public function __construct(
        private readonly StockLayoutService $stockLayoutService,
        private readonly RequirementsService $requirementsService,
        private readonly LoggingService $loggingService,
        private readonly StockLayoutFormHelper $stockLayoutFormHelper,
        private readonly StockLayoutValidationService $stockLayoutValidationService
    ) {
    }

    #[Route('/lagerlayout', name: 'stock_layout')]
    public function index(): Response
    {
        return $this->render(
            'stock/stock_layout/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Lagerlayout',
            ]
        );
    }

    #[Route('/lagerlayout_anlegen', name: 'add_stock_layout')]
    public function addStockLayout(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->stockLayoutFormHelper->addStockLayoutForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockLayoutEntity $stockLayoutRequestData */
            $stockLayoutRequestData = $form->getData();
            $stockNr = $stockLayoutRequestData->getStockNr();
            $responseData = $this->stockLayoutValidationService
                ->validateStockLayoutData(
                    (array) $request->request->all()['add_stock_layout']
                );

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Das Lagerlayout für das Lager ' . $stockNr . ' wurde erfolgreich angelegt.';
                $logMessage = 'Das Lagerlayout für das Lager ' . $stockNr . ' wurde angelegt.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->stockLayoutService->addStockLayout($stockLayoutRequestData);

                return new JsonResponse($responseData);
            }

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/stock_layout/stock_layout_add.html.twig',
            [
                'stockLayoutForm' => $form->createView(),
                'editStockLayout' => false,
            ]
        );
    }

    #[Route('/lagerlayout_bearbeiten/stockLayoutId/{stockLayoutId}', name: 'edit_stock_layout')]
    public function editStockLayout(Request $request, int $stockLayoutId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $stockLayout = $this->stockLayoutService->getStockLayoutById($stockLayoutId);

        if (!$stockLayout instanceof StockLayoutEntity) {
            return null;
        }

        $form = $this->stockLayoutFormHelper->editStockLayoutForm($stockLayout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockLayoutEntity $stockLayoutRequestData */
            $stockLayoutRequestData = $form->getData();
            $stockNr = $stockLayoutRequestData->getStockNr();
            $responseData = $this->stockLayoutValidationService
                ->validateStockLayoutData(
                    (array) $request->request->all()['edit_stock_layout']
                );

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Das Lagerlayout für das Lager ' . $stockNr . ' wurde erfolgreich geändert.';
                $logMessage = 'Der Lagerplatz ' . $stockNr . ' wurde geändert.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->stockLayoutService->updateStockLayout($stockLayoutRequestData);

                return new JsonResponse($responseData);
            }

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/stock_layout/stock_layout_edit.html.twig',
            [
                'stockLayoutForm' => $form->createView(),
                'editStockLayout' => true,
            ]
        );
    }

    #[Route('/lagerlayout_löschen/stockLayoutId/{stockLayoutId}', name: 'delete_stock_layout')]
    public function deleteStockLayout(Request $request, int $stockLayoutId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $stockLayout = $this->stockLayoutService->getStockLayoutById($stockLayoutId);

        if (!$stockLayout instanceof StockLayoutEntity) {
            return null;
        }

        $form = $this->stockLayoutFormHelper->deleteStockLayoutForm($stockLayout);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var StockLayoutEntity $stockLayoutRequestData */
            $stockLayoutRequestData = $form->getData();
            $stockNr = $stockLayout->getStockNr();
            $responseData = [];

            $responseData['message'] = 'Das Lagerlayout für das Lager ' . $stockNr . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Das Lagerlayout für das Lager ' . $stockNr . ' wurde gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->stockLayoutService->deleteStockLayout($stockLayoutRequestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'stock/stock_layout/stock_layout_delete_ask.html.twig',
            [
                'stockLayoutForm' => $form->createView(),
                'stockNr' => $stockLayout->getStockNr(),
            ]
        );
    }

    #[Route('/stock_layout_ajax', name: 'stock_layout_ajax')]
    public function getAllStockLayouts(): JsonResponse
    {
        return $this->stockLayoutService->getAllStockLayouts();
    }
}
