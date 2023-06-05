<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\CustomerOrder as CustomerOrderEntity;
use WebWMS\Entity\CustomerOrderPos as CustomerOrderPosEntity;
use WebWMS\Helper\FormHelper\CustomerOrderFormHelper;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\CustomerOrder\CustomerOrderService;
use WebWMS\Service\CustomerOrderPos\CustomerOrderPosService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrder
 */
class CustomerOrder extends AbstractController
{
    public function __construct(
        private ArticleService $articleService,
        private CustomerOrderService $customerOrderService,
        private CustomerOrderPosService $customerOrderPosService,
        private RequirementsService $requirementsService,
        private CustomerService $customerService,
        private LoggingService $loggingService,
        private CustomerOrderFormHelper $customerOrderFormHelper
    ) {
    }

    #[Route('/auftrag', name: 'customer_orders')]
    public function index(): Response
    {
        if ($this->getUser() === null) {
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
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrderForm = $this->customerOrderFormHelper->addCustomerOrderForm();

        $customerOrderForm->handleRequest($request);
        if ($customerOrderForm->isSubmitted() && $customerOrderForm->isValid()) {
            /** @var CustomerOrderEntity $customerOrderRequestData */
            $customerOrderRequestData = $customerOrderForm->getData();
            $customerOrderNr = $customerOrderRequestData->getCustomerOrderNr();
            $responseData = [];

            $responseData['message'] = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde erfolgreich angelegt.';
            $logMessage = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderService->addCustomerOrder($customerOrderRequestData);

            return new JsonResponse($responseData);
        }

        $customerOrderPosForm = $this->customerOrderFormHelper->addCustomerOrderPosForm();

        $customerOrderPosForm->handleRequest($request);
        if ($customerOrderPosForm->isSubmitted() && $customerOrderPosForm->isValid()) {
            /** @var CustomerOrderPosEntity $customerOrderPosRequestData */
            $customerOrderPosRequestData = $customerOrderPosForm->getData();
            $customerOrderId = $customerOrderPosRequestData->getCustomerOrderId();
            $responseData = [];

            $responseData['message'] = 'Die Position(en) für die Auftrags-Id. ' . $customerOrderId . ' wurde(n) erfolgreich angelegt.';
            $logMessage = 'Die Position(en) für die Auftrags-Id. ' . $customerOrderId . ' wurde(n) angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderPosService->addCustomerOrderPos($customerOrderPosRequestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer_order/customer_order_add.html.twig',
            [
                'article' => $this->getAllArticleAjax($request),
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
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrder = $this->customerOrderService->getCustomerOrderById($customerOrderId);
        $customerOrderPos = $this->customerOrderPosService->getCustomerOrderPosByCustomerOrderId($customerOrderId);

        if ($customerOrder === null) {
            return null;
        }

        $customerOrderForm = $this->customerOrderFormHelper->editCustomerOrderForm($customerOrder);
        $customerOrderForm->handleRequest($request);

        if ($customerOrderForm->isSubmitted() && $customerOrderForm->isValid()) {
            /** @var CustomerOrderEntity $customerOrderRequestData */
            $customerOrderRequestData = $customerOrderForm->getData();
            $customerOrderNr = $customerOrderRequestData->getCustomerOrderNr();
            $responseData = [];

            $responseData['message'] = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde erfolgreich geändert.';
            $logMessage = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde geändert.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderService->updateCustomerOrder($customerOrderRequestData);

            return new JsonResponse($responseData);
        }

        $customerOrderPosForm = $this->customerOrderFormHelper->editCustomerOrderPosForm($customerOrderPos);
        $customerOrderPosForm->handleRequest($request);

        if ($customerOrderPosForm->isSubmitted() && $customerOrderPosForm->isValid()) {
            /** @var CustomerOrderPosEntity $customerOrderPosRequestData */
            $customerOrderPosRequestData = $customerOrderPosForm->getData();
            $customerOrderId = $customerOrderPosRequestData->getCustomerOrderId();
            $responseData = [];

            $responseData['message'] = 'Die Position(en) für die Auftrags-Nr. VLS-01-' . $customerOrderId . ' wurde(n) erfolgreich angelegt.';
            $logMessage = 'Die Position(en) für die Auftrags-Nr. VLS-01-' . $customerOrderId . ' wurde(n) angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderPosService->updateCustomerOrderPos($customerOrderPosRequestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer_order/customer_order_edit.html.twig',
            [
                'customerOrderForm' => $customerOrderForm->createView(),
                'customerOrderPosForm' => $customerOrderPosForm->createView(),
                'customerData' => $this->customerService->getCustomerById($customerOrder->getCustomerId()),
                'customerOrderPos' => $customerOrder->getCustomerOrderPos()->toArray(),
                'lastId' => $this->customerOrderService->getLastCustomerOrderId()[0],
                'editCustomerOrder' => true,
            ]
        );
    }

    #[Route('/auftrag_löschen/customerOrderId/{customerOrderId}', name: 'delete_customer_order')]
    public function deleteCustomerOrder(Request $request, int $customerOrderId): RedirectResponse|JsonResponse|Response|null
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $customerOrder = $this->customerOrderService->getCustomerOrderById($customerOrderId);
        $customerOrderPos = $this->customerOrderPosService->getCustomerOrderPosByCustomerOrderId($customerOrderId);

        if ($customerOrder === null) {
            return null;
        }

        $customerOrderForm = $this->customerOrderFormHelper->deleteCustomerOrderForm($customerOrder);

        $customerOrderForm->handleRequest($request);
        if ($customerOrderForm->isSubmitted() && $customerOrderForm->isValid()) {
            /** @var CustomerOrderEntity $customerOrderRequestData */
            $customerOrderRequestData = $customerOrderForm->getData();
            $customerOrderNr = $customerOrderRequestData->getCustomerOrderNr();
            $responseData = [];

            $responseData['message'] = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Der Auftrag mit der Auftrags-Nr. ' . $customerOrderNr . ' wurde gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderService->deleteCustomerOrder($customerOrderRequestData);

            return new JsonResponse($responseData);
        }

        $customerOrderPosForm = $this->customerOrderFormHelper->deleteCustomerOrderPosForm($customerOrderPos);

        $customerOrderPosForm->handleRequest($request);
        if ($customerOrderPosForm->isSubmitted() && $customerOrderPosForm->isValid()) {
            /** @var CustomerOrderPosEntity $customerOrderPosRequestData */
            $customerOrderPosRequestData = $customerOrderPosForm->getData();
            $customerOrderId = $customerOrderPosRequestData->getCustomerOrderId();
            $responseData = [];

            $responseData['message'] = 'Die Position(en) für die Auftrags-Nr. VLS-01-' . $customerOrderId . ' wurde(n) erfolgreich gelöscht.';
            $logMessage = 'Die Position(en) für die Auftrags-Nr. VLS-01-' . $customerOrderId . ' wurde(n) gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerOrderPosService->deleteCustomerOrderPos($customerOrderPosRequestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer_order/customer_order_delete_ask.html.twig',
            [
                'customerOrderForm' => $customerOrderForm->createView(),
                'customerOrderNr' => $customerOrder->getCustomerOrderNr(),
            ]
        );
    }

    #[Route('/customer_order_ajax', name: 'customer_order_ajax')]
    public function getAllCustomerOrders(): JsonResponse
    {
        return $this->customerOrderService->getAllCustomerOrders();
    }

    #[Route('/customer_order_pos', name: 'customer_order_pos')]
    public function getAllCustomerOrdersPos(): JsonResponse
    {
        return $this->customerOrderService->getAllCustomerOrderPos();
    }

    #[Route('/customer_order_pos/customerOrderId/{customerOrderId}', name: 'customer_order_pos_by_customer_order_id')]
    public function getCustomerOrderPosByOrderId(int $customerOrderId): JsonResponse
    {
        return $this->customerOrderService->getCustomerOrderPosByOrderId($customerOrderId);
    }

    #[Route('/article_order_ajax', name: 'article_order_ajax')]
    public function getAllArticleAjax(Request $request): JsonResponse
    {
        return $this->articleService->getArticle($request);
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
