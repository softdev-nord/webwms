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
                'data' => $this->getAllCustomersAjax(),
                'customer' => $this->getAllCustomers(),
            ]
        );
    }

    #[Route('/kunden_anlegen', name: 'add_customer')]
    public function addNewCustomer(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestData = $request->request->all();

        if (!empty($requestData)) {
            $requestData = $requestData['add_customer'];
        }

        $form = $this->createForm(AddCustomerType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $logMessage = sprintf('Der Kunde mit der Kunden-Nr. %s wurde angelegt.', $requestData['customerNr']);
            $this->loggingService->write($request, $logMessage);
            $this->customerService->addCustomer($requestData);

            return new JsonResponse($requestData);
        }

        return $this->render(
            'customer/customer_add.html.twig',
            [
                'lastId' => $this->getLastCustomer()[0],
                'customerForm' => $form->createView(),
                'editCustomer' => false,
            ]
        );
    }

    #[Route('kunden_bearbeiten/kundenNr/{customerNr}', name: 'edit_customer')]
    public function editCustomer(Request $request, $customerNr): RedirectResponse|JsonResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestData = $request->request->all();

        if (!empty($requestData)) {
            $requestData = $requestData['edit_customer'];
        }

        $responseData = $this->customerValidationService->validateCustomerData($requestData);
        $responseData['message'] = '';

        $customer = $this->customerService->getCustomerByNr((int) $customerNr);
        $form = $this->createForm(EditCustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Die Änderungen der Kundendaten wurden erfolgreich gespeichert.';
                $logMessage = sprintf('Der Der Kunde mit der Kunden-Nr. %s wurde geändert.', $requestData['customerNr']);
                $this->loggingService->write($request, $logMessage);
                $this->customerService->updateCustomer($requestData);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Die Änderungen der Kundendaten konnte nicht gespeichert werden.';

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

    public function getLastCustomer(): array
    {
        return $this->customerService->getLastCustomer();
    }
}
