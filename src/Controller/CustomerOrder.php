<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;
use WebWMS\Form\CustomerOrder\CustomerOrderType;
use WebWMS\Form\CustomerOrder\DeleteCustomerOrderType;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\CustomerOrder\CustomerOrderService;
use WebWMS\Service\CustomerOrderPos\CustomerOrderPosService;
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
        private CustomerOrderPosService $customerOrderPosService,
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
    public function addCustomerOrder(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrderForm = $this->createForm(CustomerOrderType::class);
        $customerOrderForm->handleRequest($request);
        if ($customerOrderForm->isSubmitted() && $customerOrderForm->isValid()) {
            $customerOrderRequestData = $customerOrderForm->getData();
            $customerOrderNr = $customerOrderRequestData->getSupplierOrderNr();

            $responseData['message'] = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde erfolgreich angelegt.';
            $logMessage = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderService->addCustomerOrder($customerOrderRequestData);

            return new JsonResponse($responseData);
        }

        $customerOrderPosForm = $this->createForm(CustomerOrderPosType::class);
        $customerOrderPosForm->handleRequest($request);
        if ($customerOrderPosForm->isSubmitted() && $customerOrderPosForm->isValid()) {
            $customerOrderRequestData = $customerOrderPosForm->getData();
            $customerOrderNr = $customerOrderRequestData->getSupplierOrderNr();

            $responseData['message'] = 'Die Position(en) für die Auftrags-Nr. ' . $customerOrderNr . ' wurde(n) erfolgreich angelegt.';
            $logMessage = 'Die Position(en) für die Auftrags-Nr. ' . $customerOrderNr . ' wurde(n) angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderPosService->addCustomerOrderPos($request);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer_order/customer_order_add.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Auftrag anlegen',
                'article' => $this->getAllArticleAjax(),
                'lastId' => $this->getLastCustomerOrderId()[0],
                'editCustomerOrder' => false,
                'customerOrderForm' => $customerOrderForm->createView(),
                'customerOrderPosForm' => $customerOrderPosForm->createView(),
            ]
        );
    }

    #[Route('/auftrag_bearbeiten/customerOrderId/{customerOrderId}', name: 'edit_customer_order')]
    public function editCustomerOrder(Request $request, int $customerOrderId): RedirectResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrder = $this->customerOrderService->getCustomerOrderById($customerOrderId);

        if (!$customerOrder) {
            return null;
        }

        $customerOrderForm = $this->createForm(CustomerOrderType::class, $customerOrder);
        $customerOrderPosForm = $this->createForm(CustomerOrderPosType::class);

        $customerOrderForm->handleRequest($request);
        if ($customerOrderForm->isSubmitted() && $customerOrderForm->isValid()) {
            $customerOrderRequestData = $customerOrderForm->getData();
            $customerOrderNr = $customerOrderRequestData->getSupplierOrderNr();
            $responseData['message'] = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde erfolgreich geändert.';
            $logMessage = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde geändert.';
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderService->updateCustomerOrder($customerOrderRequestData);

            return new JsonResponse($responseData);
        }

        $customerOrderPosForm->handleRequest($request);
        if ($customerOrderPosForm->isSubmitted() && $customerOrderPosForm->isValid()) {
            $customerOrderPosRequestData = $customerOrderPosForm->getData();
            $customerOrderNr = $customerOrderPosRequestData->getSupplierOrderNr();

            $responseData['message'] = 'Die Position(en) für die Auftrags-Nr. ' . $customerOrderNr . ' wurde(n) erfolgreich angelegt.';
            $logMessage = 'Die Position(en) für die Auftrags-Nr. ' . $customerOrderNr . ' wurde(n) angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderPosService->updateCustomerOrderPos($customerOrderPosRequestData);

            return new JsonResponse($responseData);
        }

        return $this->render('customer_order/customer_order_edit.html.twig', [
            'customerOrderForm' => $customerOrderForm->createView(),
            'customerOrderPosForm' => $customerOrderPosForm->createView(),
            'customerData' => $this->customerService->getCustomerById($customerOrder->getCustomerId()),
            'customerOrderPos' => $customerOrder->getCustomerOrderPos()->toArray(),
            'lastId' => $this->customerOrderService->getLastCustomerOrderId()[0],
            'editCustomerOrder' => true,
        ]);
    }

    #[Route('/auftrag_löschen/customerOrderId/{customerOrderId}', name: 'delete_customer_order')]
    public function deleteCustomerOrder(Request $request, int $customerOrderId): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrder = $this->customerOrderService->getCustomerOrderById($customerOrderId);

        if (!$customerOrder) {
            return null;
        }

        $customerOrderForm = $this->createForm(DeleteCustomerOrderType::class, $customerOrder);
        $customerOrderForm->handleRequest($request);
        $customerOrderRequestData = $customerOrderForm->getData();
        $customerOrderNr = $customerOrderRequestData->getSupplierOrderNr();

        if ($customerOrderForm->isSubmitted() && $customerOrderForm->isValid()) {
            $responseData['message'] = 'Die Bestellung mit der Bestell-Nr. ' . $customerOrderNr . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Die Bestellung mit der Bestell-Nr. ' . $customerOrderNr . ' wurde gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderService->deleteCustomerOrder($customerOrderRequestData);
            $customerOrderPos = $this->customerOrderPosService->getCustomerOrderPosByCustomerOrderId($customerOrderId);
            $this->customerOrderPosService->deleteCustomerOrderPos($customerOrderPos);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer_order/customer_order_delete_ask.html.twig',
            [
                'customerOrderForm' => $customerOrderForm->createView(),
                'customerOrderNr' => $customerOrderNr,
            ]
        );
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
