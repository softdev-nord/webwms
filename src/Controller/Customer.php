<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\Customer as CustomerEntity;
use WebWMS\Helper\FormHelper\CustomerFormHelper;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Validation\CustomerValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        Customer
 */
class Customer extends AbstractController
{
    public function __construct(
        private CustomerService $customerService,
        private RequirementsService $requirementsService,
        private CustomerValidationService $customerValidationService,
        private LoggingService $loggingService,
        private CustomerFormHelper $customerFormHelper
    ) {
    }

    #[Route('/kunden', name: 'customer')]
    public function index(): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'customer/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Kundenübersicht',
            ]
        );
    }

    #[Route('/kunden_anlegen', name: 'add_customer')]
    public function addCustomer(Request $request): RedirectResponse|Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->customerFormHelper->addCustomerForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CustomerEntity $requestData */
            $requestData = $form->getData();
            $customerNr = $requestData->getCustomerNr();
            $responseData = $this->customerValidationService
                ->validateCustomerData(
                    (array) $request->request->all()['add_customer']
                );

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurde erfolgreich angelegt.';
                $logMessage = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurde angelegt.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->customerService->addCustomer($requestData);

                return new JsonResponse($responseData);
            }

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer/customer_add.html.twig',
            [
                'customerForm' => $form->createView(),
                'lastId' => $this->getLastCustomer()[0],
                'editCustomer' => false,
            ]
        );
    }

    #[Route('kunden_bearbeiten/customerId/{customerId}', name: 'edit_customer')]
    public function editCustomer(Request $request, int $customerId): RedirectResponse|JsonResponse|Response|null
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $customer = $this->customerService->getCustomerById($customerId);

        if ($customer === null) {
            return null;
        }

        $form = $this->customerFormHelper->editCustomerForm($customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CustomerEntity $requestData */
            $requestData = $form->getData();
            $customerNr = $requestData->getCustomerNr();
            $responseData = $this->customerValidationService
                ->validateCustomerData(
                    (array) $request->request->all()['edit_customer']
                );

            if (isset($responseData['success'])) {
                $responseData['message'] = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurden erfolgreich geändert.';
                $logMessage = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurde geändert.';

                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->customerService->updateCustomer($requestData);

                return new JsonResponse($responseData);
            }

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer/customer_edit.html.twig',
            [
                'customerForm' => $form->createView(),
                'customers' => $customer,
                'editCustomer' => true,
            ]
        );
    }

    #[Route('/kunden_löschen/customerId/{customerId}', name: 'delete_customer')]
    public function deleteCustomer(Request $request, int $customerId): RedirectResponse|JsonResponse|Response|null
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $customer = $this->customerService->getCustomerById($customerId);

        if ($customer === null) {
            return null;
        }

        $form = $this->customerFormHelper->deleteCustomerForm($customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CustomerEntity $requestData */
            $requestData = $form->getData();
            $customerNr = $requestData->getCustomerNr();
            $responseData = [];

            $responseData['message'] = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurde gelöscht.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerService->deleteCustomer($requestData);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer/customer_delete_ask.html.twig',
            [
                'customerForm' => $form->createView(),
                'customerNr' => $customer->getCustomerNr(),
            ]
        );
    }

    #[Route('/customer_ajax', name: 'customer_ajax')]
    public function getAllCustomers(): JsonResponse
    {
        return $this->customerService->getAllCustomers();
    }

    #[Route('/order_customer_ajax', name: 'order_customer_ajax')]
    public function getAllCustomersAjax(): JsonResponse
    {
        return $this->customerService->getAllCustomersAjax();
    }

    /**
     * @return object[]
     */
    public function getLastCustomer(): array
    {
        return $this->customerService->getLastCustomer();
    }
}
