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

    /**
     * @throws Exception
     */
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

        $requestData = $request->request->all();

        if (!empty($requestData)) {
            $requestData = $requestData['add_supplier'];
        }

        $form = $this->createForm(AddSupplierType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $logMessage = sprintf('Der Lieferant mit der Lieferanten-Nr. %s wurde angelegt.', $requestData['supplierNr']);
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            /*
             * @phpstan-ignore-next-line
             */
            $this->supplierService->addSupplier($requestData);

            return new JsonResponse($requestData);
        }

        return $this->render(
            'supplier/supplier_add.html.twig',
            [
                'lastId' => $this->getLastSupplier()[0],
                'supplierForm' => $form->createView(),
                'editSupplier' => false,
            ]
        );
    }

    #[Route('/lieferant_bearbeiten/lieferantenNr/{supplierNr}', name: 'edit_supplier')]
    public function editSupplier(Request $request, int $supplierNr): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $supplier = $this->supplierService->getSupplierByNr($supplierNr);

        if (!$supplier) {
            return null;
        }

        $form = $this->createForm(EditSupplierType::class, $supplier);
        $form->handleRequest($request);
        $supplierNr = $supplier->getSupplierNr();
        $requestData = $form->getData();

        $responseData = $this->supplierValidationService->validateSupplierData($requestData);
        $responseData['message'] = '';

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Die Änderungen am Lieferanten '.$supplierNr.' wurden erfolgreich gespeichert.';
                $logMessage = 'Lieferant mit der Lieferanten-Nr. '.$supplierNr.' wurde geändert.';
                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->supplierService->updateSupplier($request);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Die Änderungen der Lieferantendaten konnte nicht gespeichert werden.';

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

    #[Route('/lieferant_löschen/lieferantenNr/{supplierNr}', name: 'delete_supplier')]
    public function deleteSupplier(Request $request, int $supplierNr): RedirectResponse|JsonResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestData = $request->request->all();

        if (!empty($requestData)) {
            $requestData = $requestData['delete_supplier'];
        }

        $supplier = $this->supplierService->getSupplierByNr($supplierNr);
        $form = $this->createForm(DeleteSupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $responseData['message'] = sprintf(
                'Der Lieferant mit der Lieferanten-Nr. %s wurde erfolgreich gelöscht.',
                $supplierNr
            );
            $logMessage = sprintf('Der Lieferant mit der Lieferanten-Nr. %s wurde gelöscht.', $supplierNr);
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierService->deleteSupplier($supplierNr);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'supplier/supplier_delete_ask.html.twig',
            [
                'supplierNr' => $supplierNr,
                'supplierForm' => $form->createView(),
            ]
        );
    }

    #[Route('/supplier_ajax', name: 'supplier_ajax')]
    public function getAllSuppliers(): JsonResponse
    {
        return $this->supplierService->getAllSuppliers();
    }

    /**
     * @throws Exception
     */
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
