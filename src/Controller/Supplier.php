<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\Supplier as SupplierEntity;
use WebWMS\Helper\FormHelper\SupplierFormHelper;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Supplier\SupplierService;
use WebWMS\Service\Validation\SupplierValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        Supplier
 */
class Supplier extends AbstractController
{
    public function __construct(
        private SupplierService $supplierService,
        private RequirementsService $requirementsService,
        private LoggingService $loggingService,
        private SupplierValidationService $supplierValidationService,
        private SupplierFormHelper $supplierFormHelper
    ) {
    }

    #[Route('/lieferanten', name: 'supplier')]
    public function index(): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'supplier/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Lieferantenübersicht',
            ]
        );
    }

    #[Route('/lieferant_anlegen', name: 'add_supplier')]
    public function addSupplier(Request $request): RedirectResponse|Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->supplierFormHelper->addSupplierForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SupplierEntity $requestData */
            $requestData = $form->getData();
            $supplierNr = $requestData->getSupplierNr();
            $responseData = [];

            $responseData['message'] = 'Der Lieferant mit der Lieferanten-Nr. ' . $supplierNr . ' wurde erfolgreich angelegt.';
            $logMessage = 'Der Lieferant mit der Lieferanten-Nr. ' . $supplierNr . ' wurde angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierService->addSupplier($requestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'supplier/supplier_add.html.twig',
            [
                'supplierForm' => $form->createView(),
                'lastId' => $this->getLastSupplier(),
                'editSupplier' => false,
            ]
        );
    }

    #[Route('/lieferant_bearbeiten/supplierId/{supplierId}', name: 'edit_supplier')]
    public function editSupplier(Request $request, int $supplierId): RedirectResponse|JsonResponse|Response|null
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $supplier = $this->supplierService->getSupplierById($supplierId);

        if ($supplier === null) {
            return null;
        }

        $form = $this->supplierFormHelper->editSupplierForm($supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SupplierEntity $requestData */
            $requestData = $form->getData();
            $supplierNr = $requestData->getSupplierNr();
            $responseData = $this->supplierValidationService
                ->validateSupplierData((array) $request->request->all()['edit_customer']);

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Die Änderungen am Lieferanten ' . $supplierNr . ' wurden erfolgreich gespeichert.';
                $logMessage = 'Lieferant mit der Lieferanten-Nr. ' . $supplierNr . ' wurde geändert.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->supplierService->updateSupplier($requestData);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Die Änderungen der Lieferantendaten konnten nicht gespeichert werden.';

            return new JsonResponse($responseData);
        }

        return $this->render(
            'supplier/supplier_edit.html.twig',
            [
                'supplierForm' => $form->createView(),
                'supplier' => $supplier,
                'editSupplier' => true,
            ]
        );
    }

    #[Route('/lieferant_löschen/supplierId/{supplierId}', name: 'delete_supplier')]
    public function deleteSupplier(Request $request, int $supplierId): RedirectResponse|JsonResponse|Response|null
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $supplier = $this->supplierService->getSupplierById($supplierId);

        if ($supplier === null) {
            return null;
        }

        $form = $this->supplierFormHelper->deleteSupplierForm($supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SupplierEntity $requestData */
            $requestData = $form->getData();
            $supplierNr = $requestData->getSupplierNr();
            $responseData = [];

            $responseData['message'] = 'Der Lieferant mit der Lieferanten-Nr. ' . $supplierNr . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Der Lieferant mit der Lieferanten-Nr. ' . $supplierNr . ' wurde gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierService->deleteSupplier($requestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'supplier/supplier_delete_ask.html.twig',
            [
                'supplierForm' => $form->createView(),
                'supplierNr' => $supplier->getSupplierNr(),
            ]
        );
    }

    #[Route('/supplier_ajax', name: 'supplier_ajax')]
    public function getAllSuppliers(): JsonResponse
    {
        return $this->supplierService->getAllSuppliers();
    }

    #[Route('/order_supplier_ajax', name: 'order_supplier_ajax')]
    public function getAllSuppliersAjax(): JsonResponse
    {
        return $this->supplierService->getAllSuppliersAjax();
    }

    public function getLastSupplier(): int
    {
        return $this->supplierService->getLastSupplier();
    }
}
