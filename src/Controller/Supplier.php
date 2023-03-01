<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\Supplier\AddSupplierType;
use WebWMS\Form\Supplier\DeleteSupplierType;
use WebWMS\Form\Supplier\EditSupplierType;
use WebWMS\Service\LoggingService;
use WebWMS\Service\Supplier\SupplierService;
use WebWMS\Service\Validation\SupplierValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Supplier
 */
class Supplier extends AbstractController
{
    public function __construct(
        private SupplierService $supplierService,
        private Requirements $requirements,
        private LoggingService $loggingService,
        private SupplierValidationService $supplierValidationService,
    ) {
    }

    #[Route('/lieferanten', name: 'supplier')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'supplier/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lieferantenübersicht',
            ]
        );
    }

    #[Route('/lieferant_anlegen', name: 'add_supplier')]
    public function addSupplier(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AddSupplierType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $form->getData();
            $supplierNr = $requestData->getSupplierNr();
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
                'lastId' => $this->getLastSupplier()[0],
                'editSupplier' => false,
            ]
        );
    }

    #[Route('/lieferant_bearbeiten/supplierId/{supplierId}', name: 'edit_supplier')]
    public function editSupplier(Request $request, int $supplierId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $supplier = $this->supplierService->getSupplierById($supplierId);

        if (!$supplier) {
            return null;
        }

        $form = $this->createForm(EditSupplierType::class, $supplier);
        $form->handleRequest($request);
        $requestData = $form->getData();
        $supplierNr = $requestData->getSupplierNr();

        $responseData = $this->supplierValidationService->validateSupplierData($requestData);
        $responseData['message'] = '';

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
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
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $supplier = $this->supplierService->getSupplierById($supplierId);

        if (!$supplier) {
            return null;
        }

        $form = $this->createForm(DeleteSupplierType::class, $supplier);
        $form->handleRequest($request);
        $requestData = $form->getData();
        $supplierNr = $requestData->getSupplierNr();

        if ($form->isSubmitted() && $form->isValid()) {
            $responseData['message'] = 'Der Lieferant mit der Lieferanten-Nr. ' . $supplierNr . ' wurde erfolgreich angelegt.';
            $logMessage = 'Der Lieferant mit der Lieferanten-Nr. ' . $supplierNr . ' wurde angelegt.';
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierService->deleteSupplier($requestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'supplier/supplier_delete_ask.html.twig',
            [
                'supplierForm' => $form->createView(),
                'supplierNr' => $supplierNr,
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

    /**
     * @return object[]
     */
    public function getLastSupplier(): array
    {
        return $this->supplierService->getLastSupplier();
    }
}
