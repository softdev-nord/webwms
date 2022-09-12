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
use WebWMS\Form\CustomerOrderPosType;
use WebWMS\Form\CustomerOrderType;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\CustomerOrderService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrder
 */
class CustomerOrder extends AbstractController
{
    public function __construct(
        private ArticleService $articleService,
        private CustomerOrderService $customerOrderService,
        private Requirements $requirements
    ) {
    }

    #[Route('/auftrag', name: 'customer_orders')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'customer_order/index.html.twig',
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
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/auftrag_anlegen', name: 'new_customer_order')]
    public function addNewCustomerOrder(EntityManagerInterface $entityManager, Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrderForm = $this->createForm(CustomerOrderType::class);

        $customerOrder = new CustomerOrders();
        $customerOrderPosForm = $this->createForm(CustomerOrderPosType::class);

        $form = $this->createForm(CustomerOrderType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CustomerOrders $customerOrder */
            $customerOrder = $form->getData();

            //$customerOrder = new CustomerOrders();
            //$customerOrderPos = new CustomerOrderPos();
            /*$customerOrder->setCustomerId($data['customer_order[customer_id]']);
            $customerOrder->setCustomerOrderDate($data['customer_order[customer_order_date]']);
            $customerOrder->setCustomerOrderId($data['customer_order[customer_order_id]']);
            $customerOrder->setCustomerOrderNr($data['customer_order[customer_order_nr]']);
            $customerOrder->setCustomerOrderOrderDate($data['customer_order[customer_order_order_date]']);
            $customerOrder->setCustomerOrderReference($data['customer_order[customer_order_reference]']);*/
            $customerOrder->setUsrId((int) $this->getUser());

            $entityManager->persist($customerOrder);
            //$em->persist($customerOrderPos);
            $entityManager->flush();
            //return new Response('News added successfuly');

            $this->addFlash('success', 'Der Auftrag und die Position(en) wurden erfolgreich angelegt.');

            return $this->redirectToRoute('new_customer_order');
        }

        /*return $this->render(
            'customer_order/add_customer_order.html.twig',
            [
                'forms' => \array_map(
                    function($form) {
                        return $form->createView();
                    },
                    $forms
                ),
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Auftrag anlegen',
                'article' => $this->getAllArticleAjax(),
                'lastId' => $this->getLastCustomerOrderId()[0],
            ]
        );*/

        return $this->render(
            'customer_order/add_customer_order.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Auftrag anlegen',
                'article' => $this->getAllArticleAjax(),
                'lastId' => $this->getLastCustomerOrderId()[0],
                'customerForm' => $customerOrderForm->createView(),
                'customerOrderPosForm' => $customerOrderPosForm->createView(),
            ]
        );
    }

    #[Route('/customer_order_ajax', name: 'customer_order_ajax')]
    public function getAllCustomerOrders(): JsonResponse
    {
        return $this->customerOrderService->getAllCustomerOrders();
    }

    /**
     * @throws Exception
     */
    #[Route('/customer_order_pos_ajax', name: 'customer_order_pos_ajax')]
    public function getAllCustomerOrdersPos(): JsonResponse
    {
        return $this->customerOrderService->getAllCustomerOrderPos();
    }

    #[Route('/article_order_ajax', name: 'article_order_ajax')]
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
