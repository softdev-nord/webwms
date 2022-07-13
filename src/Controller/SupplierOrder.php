<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Entity\SupplierOrder as SupplierOrders;
use WebWMS\Form\SupplierOrderType;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\SupplierOrderService;

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
        private Requirements $requirements
    ) {
    }

    /**
     * @Route("/bestellungen", name="orders")
     */
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

    /**
     * @Route("/supplier_oders_ajax", name="supplier_orders_ajax")
     * @throws Exception
     */
    public function getAllOrders(): JsonResponse
    {
        return $this->supplierOrderService->getAllOrders();
    }

    /**
     * @Route("/supplier_order_pos_ajax", name="supplier_order_pos_ajax")
     * @throws Exception
     */
    public function getAllOrderPos(): JsonResponse
    {
        return $this->supplierOrderService->getAllOrderPos();
    }

    /**
     * @Route("/bestellung_anlegen", name="new_supplier_order")
     */
    public function addNewOrder(EntityManagerInterface $em, Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        //dd($request);

        $form = $this->createForm(SupplierOrderType::class);
        $form->handleRequest($request);
        //dd($form->getData());
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SupplierOrders $order */
            $customerOrder = $form->getData();
            //dd($customerOrder);

            //dd($customerOrder);

            //$customerOrder = new CustomerOrders();
            //$customerOrderPos = new CustomerOrderPos();
            /*$customerOrder->setCustomerId($data['customer_order[customer_id]']);
            $customerOrder->setCustomerOrderDate($data['customer_order[customer_order_date]']);
            $customerOrder->setCustomerOrderId($data['customer_order[customer_order_id]']);
            $customerOrder->setCustomerOrderNr($data['customer_order[customer_order_nr]']);
            $customerOrder->setCustomerOrderOrderDate($data['customer_order[customer_order_order_date]']);
            $customerOrder->setCustomerOrderReference($data['customer_order[customer_order_reference]']);*/
            $customerOrder->setUsrId((int) $this->getUser());

            //$em = $this->getDoctrine()->getManager();

            $em->persist($customerOrder);
            //$em->persist($customerOrderPos);
            $em->flush();
            //return new Response('News added successfuly');

            $this->addFlash('success', 'Der Auftrag und die Position(en) wurden erfolgreich angelegt.');

            return $this->redirectToRoute('new_supplier_order');
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
                'article' => $this->getAllArticleAjax(),
                'lastId' => $this->getLastSupplierOrderId()[0],
                'supplierOrderForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/article_supplier_order_ajax", name="article_supplier_order_ajax")
     */
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
