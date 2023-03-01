<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\SupplierOrder\DeleteSupplierOrderType;
use WebWMS\Form\SupplierOrder\SupplierOrderPosType;
use WebWMS\Form\SupplierOrder\SupplierOrderType;
use WebWMS\Service\LoggingService;
use WebWMS\Service\Supplier\SupplierService;
use WebWMS\Service\SupplierOrder\SupplierOrderService;
use WebWMS\Service\SupplierOrderPos\SupplierOrderPosService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrder
 */
class SupplierOrder extends AbstractController
{
    public function __construct(
        private SupplierOrderService $supplierOrderService,
        private SupplierOrderPosService $supplierOrderPosService,
        private SupplierService $supplierService,
        private Requirements $requirements,
        private LoggingService $loggingService
    ) {
    }

    #[Route('/bestellungen', name: 'supplier_orders')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'supplier_order/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Übersicht Bestellungen',
            ]
        );
    }

    #[Route('/bestellung_anlegen', name: 'add_supplier_order')]
    public function addSupplierOrder(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $supplierOrderForm = $this->createForm(SupplierOrderType::class);
        $supplierOrderPosForm = $this->createForm(SupplierOrderPosType::class);

        $supplierOrderForm->handleRequest($request);
        if ($supplierOrderForm->isSubmitted() && $supplierOrderForm->isValid()) {
            $supplierOrderRequestData = $supplierOrderForm->getData();
            $supplierOrderNr = $supplierOrderRequestData->getSupplierOrderNr();

            $responseData['message'] = 'Die Bestellung mit der Bestell-Nr. '.$supplierOrderNr.' wurde erfolgreich angelegt.';
            $logMessage = 'Die Bestellung mit der Bestell-Nr. '.$supplierOrderNr.' wurde angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierOrderService->addSupplierOrder($supplierOrderRequestData);

            return new JsonResponse($responseData);
        }

        $supplierOrderPosForm->handleRequest($request);
        if ($supplierOrderPosForm->isSubmitted() && $supplierOrderPosForm->isValid()) {
            $supplierOrderPosRequestData = $supplierOrderPosForm->getData();
            $supplierOrderNr = $supplierOrderPosRequestData->getSupplierOrderNr();

            $responseData['message'] = 'Die Position(en) für die Bestell-Nr. '.$supplierOrderNr.' wurde(n) erfolgreich angelegt.';
            $logMessage = 'Die Position(en) für die Bestell-Nr. '.$supplierOrderNr.' wurde(n) angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierOrderPosService->addSupplierOrderPos($request);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'supplier_order/supplier_order_add.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Bestellung anlegen',
                'lastId' => $this->supplierOrderService->getLastSupplierOrderId()[0],
                'supplierOrderPos' => $this->supplierService->getAllSuppliers()->getContent(),
                'editSupplierOrder' => false,
                'supplierOrderForm' => $supplierOrderForm->createView(),
                'supplierOrderPosForm' => $supplierOrderPosForm->createView(),
            ]
        );
    }

    #[Route('/bestellung_bearbeiten/supplierOrderId/{supplierOrderId}', name: 'edit_supplier_order')]
    public function editSupplierOrder(Request $request, int $supplierOrderId): RedirectResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $supplierOrder = $this->supplierOrderService->getSupplierOrderById($supplierOrderId);

        if (!$supplierOrder) {
            return null;
        }

        $supplierOrderForm = $this->createForm(SupplierOrderType::class, $supplierOrder);
        $supplierOrderPosForm = $this->createForm(SupplierOrderPosType::class);

        $supplierOrderForm->handleRequest($request);
        if ($supplierOrderForm->isSubmitted() && $supplierOrderForm->isValid()) {
            $supplierOrderRequestData = $supplierOrderForm->getData();
            $supplierOrderNr = $supplierOrderRequestData->getSupplierOrderNr();
            $responseData['message'] = 'Die Bestellung mit der Bestell-Nr. '.$supplierOrderNr.' wurde erfolgreich geändert.';
            $logMessage = 'Die Bestellung mit der Bestell-Nr. '.$supplierOrderNr.' wurde geändert.';
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierOrderService->updateSupplierOrder($supplierOrderRequestData);

            return new JsonResponse($responseData);
        }

        $supplierOrderPosForm->handleRequest($request);
        if ($supplierOrderPosForm->isSubmitted() && $supplierOrderPosForm->isValid()) {
            $supplierOrderPosRequestData = $supplierOrderPosForm->getData();
            $supplierOrderNr = $supplierOrderPosRequestData->getSupplierOrderNr();

            $responseData['message'] = 'Die Position(en) für die Bestell-Nr. '.$supplierOrderNr.' wurde(n) erfolgreich angelegt.';
            $logMessage = 'Die Position(en) für die Bestell-Nr. '.$supplierOrderNr.' wurde(n) angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierOrderPosService->addSupplierOrderPos($request);

            return new JsonResponse($responseData);
        }

        return $this->render('supplier_order/supplier_order_edit.html.twig', [
            'supplierOrderForm' => $supplierOrderForm->createView(),
            'supplierOrderPosForm' => $supplierOrderPosForm->createView(),
            'supplierData' => $this->supplierService->getSupplierById($supplierOrder->getSupplierId()),
            'supplierOrderPos' => $supplierOrder->getSupplierOrderPos()->toArray(),
            'lastId' => $this->supplierOrderService->getLastSupplierOrderId()[0],
            'editSupplierOrder' => true,
        ]);
    }

    #[Route('/bestellung_löschen/supplierOrderId/{supplierOrderId}', name: 'delete_supplier_order')]
    public function deleteArticle(Request $request, int $supplierOrderId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $supplierOrder = $this->supplierOrderService->getSupplierOrderById($supplierOrderId);

        if (!$supplierOrder) {
            return null;
        }

        $supplierOrderForm = $this->createForm(DeleteSupplierOrderType::class, $supplierOrder);
        $supplierOrderForm->handleRequest($request);
        $supplierOrderRequestData = $supplierOrderForm->getData();
        $supplierOrderNr = $supplierOrderRequestData->getSupplierOrderNr();

        if ($supplierOrderForm->isSubmitted() && $supplierOrderForm->isValid()) {
            $responseData['message'] = 'Die Bestellung mit der Bestell-Nr. '.$supplierOrderNr.' wurde erfolgreich gelöscht.';
            $logMessage = 'Die Bestellung mit der Bestell-Nr. '.$supplierOrderNr.' wurde gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->supplierOrderService->deleteSupplierOrder($supplierOrderRequestData);
            $supplierOrderPos = $this->supplierOrderPosService->getSupplierOrderPosBySupplierOrderId($supplierOrderId);
            $this->supplierOrderPosService->deleteSupplierOrderPos($supplierOrderPos);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'supplier_order/supplier_order_delete_ask.html.twig',
            [
                'supplierOrderForm' => $supplierOrderForm->createView(),
                'supplierOrderNr' => $supplierOrderNr,
            ]
        );
    }

    #[Route('/supplier_orders_ajax', name: 'supplier_orders_ajax')]
    public function getAllSupplierOrder(): JsonResponse
    {
        return $this->supplierOrderService->getAllSupplierOrder();
    }

    #[Route('/supplier_order_pos_ajax', name: 'supplier_order_pos_ajax')]
    public function getAllOrderPos(): JsonResponse
    {
        return $this->supplierOrderService->getAllOrderPos();
    }
}
