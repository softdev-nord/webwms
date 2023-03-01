<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\Customer\AddCustomerType;
use WebWMS\Form\Customer\DeleteCustomerType;
use WebWMS\Form\Customer\EditCustomerType;
use WebWMS\Service\Customer\CustomerService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\Validation\CustomerValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Customer
 */
class Customer extends AbstractController
{
    public function __construct(
        private CustomerService $customerService,
        private Requirements $requirements,
        private CustomerValidationService $customerValidationService,
        private LoggingService $loggingService
    ) {
    }

    #[Route('/kunden', name: 'customer')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'customer/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Kundenübersicht',
            ]
        );
    }

    #[Route('/kunden_anlegen', name: 'add_customer')]
    public function addCustomer(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AddCustomerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $form->getData();
            $customerNr = $requestData->getCustomerNr();
            $responseData['message'] = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurde erfolgreich angelegt.';
            $logMessage = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurde angelegt.';
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->customerService->addCustomer($requestData);

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
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $customer = $this->customerService->getCustomerById($customerId);

        if (!$customer) {
            return null;
        }

        $form = $this->createForm(EditCustomerType::class, $customer);
        $form->handleRequest($request);
        $requestData = $form->getData();
        $customerNr = $requestData->getCustomerNr();

        $responseData = $this->customerValidationService->validateCustomerData($requestData);
        $responseData['message'] = '';

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Die Änderungen am Kunden ' . $customerNr . ' wurden erfolgreich gespeichert.';
                $logMessage = 'Der Kunde mit der Kunden-Nr. ' . $customerNr . ' wurde geändert.';
                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->customerService->updateCustomer($requestData);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Die Änderungen der Kundendaten konnten nicht gespeichert werden.';

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
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $customer = $this->customerService->getCustomerById($customerId);

        if (!$customer) {
            return null;
        }

        $form = $this->createForm(DeleteCustomerType::class, $customer);
        $form->handleRequest($request);
        $requestData = $form->getData();
        $customerNr = $requestData->getCustomerNr();

        if ($form->isSubmitted() && $form->isValid()) {
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
                'customerNr' => $customerNr,
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
