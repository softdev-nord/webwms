<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\CustomerOrder as CustomerOrders;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;
use WebWMS\Form\CustomerOrder\CustomerOrderType;
use WebWMS\Form\CustomerOrder\EditCustomerOrderType;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\CustomerOrder\CustomerOrderService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrder
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerOrder extends AbstractController
{
    public function __construct(
        private ArticleService $articleService,
        private CustomerOrderService $customerOrderService,
        private RequirementsService $requirementsService,
        private CustomerService $customerService,
        private LoggingService $loggingService
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
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Übersicht Aufträge',
            ]
        );
    }

    #[Route('/auftrag_anlegen', name: 'add_customer_order')]
    public function addCustomerOrder(EntityManagerInterface $entityManager, Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrderForm = $this->createForm(CustomerOrderType::class);
        $customerOrderPosForm = $this->createForm(CustomerOrderPosType::class);

        $customerOrderForm->handleRequest($request);
        if ($customerOrderForm->isSubmitted() && $customerOrderForm->isValid()) {
            $customerOrderRequestData = $customerOrderForm->getData();
            $customerOrderNr = $customerOrderRequestData->getSupplierOrderNr();

            $responseData['message'] = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde erfolgreich angelegt.';
            $logMessage = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderService->addNewCustomerOrderAndRelatedPositions();

            return new JsonResponse($responseData);
        }

        $supplierOrderPosForm->handleRequest($request);
        if ($supplierOrderPosForm->isSubmitted() && $supplierOrderPosForm->isValid()) {
            $supplierOrderPosRequestData = $supplierOrderPosForm->getData();
            $supplierOrderNr = $supplierOrderPosRequestData->getSupplierOrderNr();

            $responseData['message'] = 'Die Position(en) für die Bestell-Nr. ' . $supplierOrderNr . ' wurde(n) erfolgreich angelegt.';
            $logMessage = 'Die Position(en) für die Bestell-Nr. ' . $supplierOrderNr . ' wurde(n) angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderPosService->addSupplierOrderPos($request);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'supplier_order/supplier_order_add.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Bestellung anlegen',
                'lastId' => $this->supplierOrderService->getLastSupplierOrderId()[0],
                'supplierOrderPos' => $this->supplierService->getAllSuppliers()->getContent(),
                'editSupplierOrder' => false,
                'supplierOrderForm' => $supplierOrderForm->createView(),
                'supplierOrderPosForm' => $supplierOrderPosForm->createView(),
            ]
        );

        $customerOrderForm = $this->createForm(CustomerOrderType::class);

        $customerOrder = new CustomerOrders();
        $customerOrderPosForm = $this->createForm(CustomerOrderPosType::class);

        $form = $this->createForm(CustomerOrderType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CustomerOrders $customerOrder */
            $customerOrder = $form->getData();

            // $customerOrder = new CustomerOrders();
            // $customerOrderPos = new CustomerOrderPos();
            /*$customerOrder->setCustomerId($data['customer_order[customer_id]']);
            $customerOrder->setCustomerOrderDate($data['customer_order[customer_order_date]']);
            $customerOrder->setCustomerOrderId($data['customer_order[customer_order_id]']);
            $customerOrder->setCustomerOrderNr($data['customer_order[customer_order_nr]']);
            $customerOrder->setCustomerOrderOrderDate($data['customer_order[customer_order_order_date]']);
            $customerOrder->setCustomerOrderReference($data['customer_order[customer_order_reference]']);
            $customerOrder->setUsrId((int) $this->getUser());*/

            $entityManager->persist($customerOrder);
            // $em->persist($customerOrderPos);
            $entityManager->flush();
            // return new Response('News added successfuly');

            $this->addFlash('success', 'Der Auftrag und die Position(en) wurden erfolgreich angelegt.');

            return $this->redirectToRoute('add_customer_order');
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
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Auftrag anlegen',
                'article' => $this->getAllArticleAjax(),
                'lastId' => $this->getLastCustomerOrderId()[0],
            ]
        );*/

        return $this->render(
            'customer_order/add_customer_order.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Auftrag anlegen',
                'article' => $this->getAllArticleAjax(),
                'lastId' => $this->getLastCustomerOrderId()[0],
                'customerForm' => $customerOrderForm->createView(),
                'customerOrderPosForm' => $customerOrderPosForm->createView(),
            ]
        );
    }

    #[Route('/auftrag_bearbeiten/id/{id}', name: 'edit_customer_order')]
    public function editCustomerOrder(Request $request, int $id): RedirectResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrder = $this->customerOrderService->getCustomerOrderById($id);

        if (!$customerOrder) {
            return null;
        }

        $form = $this->createForm(EditCustomerOrderType::class, $customerOrder);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $em->persist($article);
            // $em->flush();
            $this->addFlash('success', 'Article Updated! Inaccuracies squashed!');
            // return $this->redirectToRoute('admin_article_edit', [
            //    'id' => $article->getId(),
            // ]);
        }

        return $this->render('customer_order/customer_order_form_edit.html.twig', [
            'editCustomerOrderForm' => $form->createView(),
            'customerData' => $this->customerService->getCustomerById($customerOrder->getCustomerId()),
            'customerOrderPos' => $customerOrder->getCustomerOrderPos()->toArray(),
        ]);
    }

    #[Route('/customer_order_ajax', name: 'customer_order_ajax')]
    public function getAllCustomerOrders(): JsonResponse
    {
        return $this->customerOrderService->getAllCustomerOrders();
    }

    #[Route('/customer_order_pos_ajax', name: 'customer_order_pos_ajax')]
    public function getAllCustomerOrdersPos(): JsonResponse
    {
        return $this->customerOrderService->getAllCustomerOrderPos();
    }

    #[Route('/customer_order_pos_ajax/id/{id}', name: 'customer_order_pos_ajax_by_id')]
    public function getCustomerOrderPosByOrderId(string $id): JsonResponse
    {
        return $this->customerOrderService->getCustomerOrderPosByOrderId((int) $id);
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
