<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Entity\Order as Orders;
use WebWMS\Form\OrderType;
use WebWMS\Services\ArticleService;
use WebWMS\Services\OrderService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Order
 */
class Order extends AbstractController
{
    /** @var ArticleService */
    private $articleService;

    /** @var OrderService */
    private $orderService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        ArticleService $articleService,
        OrderService $orderService,
        Requirements $requirements
    ) {
        $this->articleService = $articleService;
        $this->orderService = $orderService;
        $this->requirements = $requirements;
    }

    /**
     * @Route("/bestellungen", name="orders")
     */
    public function index(): Response
    {
        return $this->render('order/index.html.twig',
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
     * @Route("/oders_ajax", name="orders_ajax")
     */
    public function getAllOrders(): JsonResponse
    {
        return $this->orderService->getAllOrders();
    }

    /**
     * @Route("/order_pos_ajax", name="order_pos_ajax")
     */
    public function getAllOrderPos(): JsonResponse
    {
        return $this->orderService->getAllOrderPos();
    }

    /**
     * @Route("/bestellung_anlegen", name="new_order")
     */
    public function addNewOrder(EntityManagerInterface $em, Request $request)
    {
        //dd($request);

        $form = $this->createForm(OrderType::class);
        $form->handleRequest($request);
        //dd($form->getData());
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Orders $order */
            $customerOrder = $form->getData();
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

            return $this->redirectToRoute('new_order');
        }

        return $this->render('order/add_order.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Bestellung anlegen',
                'article' => $this->getAllArticleAjax(),
                'lastId' => $this->getLastOrderId()[0],
                'orderForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/article_order_ajax", name="article_order_ajax")
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
    public function getLastOrderId(): array
    {
        return $this->orderService->getLastOrderId();
    }
}
