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
use WebWMS\Form\EditSupplierOrderType;
use WebWMS\Form\SupplierOrderType;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\Supplier\SupplierService;
use WebWMS\Service\SupplierOrder\SupplierOrderService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrder
 */
class SupplierOrder extends AbstractController
{
    public function __construct(
        private ArticleService $articleService,
        private SupplierOrderService $supplierOrderService,
        private SupplierService $supplierService,
        private Requirements $requirements,
        private LoggingService $loggingService
    ) {
    }

    #[Route('/bestellungen', name: 'orders')]
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
                'orders' => $this->getAllOrders(),
                'order_pos' => $this->getAllOrderPos(),
            ]
        );
    }

    #[Route('/bestellung_anlegen', name: 'new_supplier_order')]
    public function addNewSupplierOrder(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(SupplierOrderType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->supplierService->addSupplier($request);
            $this->addFlash('success', 'Die Bestellung wurde erfolgreich angelegt.');

            return $this->redirectToRoute('add_supplier');
        }

        return $this->render(
            'supplier_order/add_supplier_order.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Bestellung anlegen',
                'lastId' => $this->getLastSupplierOrderId()[0],
                'supplierOrderForm' => $form->createView(),
            ]
        );
    }

    #[Route('/bestellung_bearbeiten/id/{id}', name: 'edit_supplier_order')]
    public function editSupplierOrder(Request $request, $id): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $supplierOrder = $this->supplierOrderService->getSupplierOrderById((int) $id);

        $form = $this->createForm(EditSupplierOrderType::class, $supplierOrder);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $em->persist($article);
            // $em->flush();
            $this->addFlash('success', 'Article Updated! Inaccuracies squashed!');
            // return $this->redirectToRoute('admin_article_edit', [
            //    'id' => $article->getId(),
            // ]);
        }

        return $this->render('supplier_order/supplier_order_form_edit.html.twig', [
            'editSupplierOrderForm' => $form->createView(),
            'supplierData' => $this->supplierService->getSupplierById($supplierOrder->getSupplierId()),
            'supplierOrderPos' => $supplierOrder->getSupplierOrderPos()->toArray(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/supplier_oders_ajax', name: 'supplier_oders_ajax')]
    public function getAllOrders(): JsonResponse
    {
        return $this->supplierOrderService->getAllOrders();
    }

    /**
     * @throws Exception
     */
    #[Route('/supplier_order_pos_ajax', name: 'supplier_order_pos_ajax')]
    public function getAllOrderPos(): JsonResponse
    {
        return $this->supplierOrderService->getAllOrderPos();
    }

    /**
     * @throws Exception
     */
    #[Route('/article_supplier_order_ajax', name: 'article_supplier_order_ajax')]
    public function getAllArticleAjax(): JsonResponse
    {
        return $this->articleService->getArticle();
    }

    /**
     * Get last customer order id.
     *
     * @return object[]
     */
    public function getLastSupplierOrderId(): array
    {
        return $this->supplierOrderService->getLastSupplierOrderId();
    }
}
