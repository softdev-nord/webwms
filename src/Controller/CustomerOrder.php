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
use WebWMS\Entity\CustomerOrder as CustomerOrders;
use WebWMS\Form\CustomerOrderType;
use WebWMS\Services\ArticleService;
use WebWMS\Services\CustomerOrderService;

/**
 * Class        CustomerOrder
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 */
class CustomerOrder extends AbstractController
{
    /** @var ArticleService */
    private $articleService;

    /** @var CustomerOrderService */
    private $customerOrderService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        ArticleService $articleService,
        CustomerOrderService $customerOrderService,
        Requirements $requirements
    ) {
        $this->articleService = $articleService;
        $this->customerOrderService = $customerOrderService;
        $this->requirements = $requirements;
    }

    /**
     * @Route("/customer_order_ajax", name="customer_order_ajax")
     */
    public function getAllCustomerOrders(): JsonResponse
    {
        return $this->customerOrderService->getAllCustomerOrders();
    }

    /**
     * @Route("/customer_order_pos_ajax", name="customer_order_pos_ajax")
     * @throws Exception
     */
    public function getAllCustomerOrdersPos(): JsonResponse
    {
        return $this->customerOrderService->getAllCustomerOrderPos();
    }

    /**
     * @Route("/auftrag", name="customer_orders")
     * @throws Exception
     */
    public function index(): Response
    {
        return $this->render('customer_order/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Übersicht Aufträge',
                'customer_order' => $this->getAllCustomerOrders(),
                'customer_order_pos' => $this->getAllCustomerOrdersPos(),
            ]
        );
    }

    /**
     * @Route("/auftrag_anlegen", name="new_customer_order")
     *
     * @return RedirectResponse|Response
     */
    public function addNewCustomerOrder(EntityManagerInterface $em, Request $request)
    {
        //dd($request);

        $form = $this->createForm(CustomerOrderType::class);
        $form->handleRequest($request);
        //dd($form->getData());
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var CustomerOrders $customerOrder */
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

            return $this->redirectToRoute('new_customer_order');
        }

        return $this->render('customer_order/add_customer_order.html.twig',
            [
                'appName' => Requirements::APP_NAME,
                'appVersion' => Requirements::APP_VERSION,
                'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
                'page' => 'Auftrag anlegen',
                'article' => $this->getAllArticleAjax(),
                'lastId' => $this->getLastCustomerOrderId()[0],
                'customerForm' => $form->createView(),
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
    public function getLastCustomerOrderId(): array
    {
        return $this->customerOrderService->getLastCustomerOrderId();
    }
}
