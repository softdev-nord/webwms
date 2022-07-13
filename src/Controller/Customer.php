<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Form\EditCustomerType;
use WebWMS\Service\Customer\CustomerService;
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
        private CustomerValidationService $customerValidationService
    ) {
    }

    /**
     * @Route("/customer_ajax", name="customer_ajax")
     *
     */
    public function getAllCustomers(): JsonResponse
    {
        return $this->customerService->getAllCustomers();
    }

    /**
     * @Route("/order_customer_ajax", name="order_customer_ajax")
     */
    public function getAllCustomersAjax(): JsonResponse
    {
        return $this->customerService->getAllCustomersAjax();
    }

    /**
     * @Route("/kunden_anlegen", name="add_customer")
     *
     * @return RedirectResponse|Response
     */
    public function addNewCustomer(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(EditCustomerType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->customerService->addNewCustomer($request);
            $this->addFlash('success', 'Der Kunde wurde erfolgreich angelegt.');

            return $this->redirectToRoute('create_customer');
        }

        return $this->render(
            'customer/add_customer.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Kunden anlegen',
                'lastId' => $this->getLastCustomer()[0],
                'addCustomerForm' => $form->createView(),
            ]
        );
    }

    public function getLastCustomer(): array
    {
        return $this->customerService->getLastCustomer();
    }

    /**
     * @Route("/kunden", name="customer")
     */
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

    /**
     * @Route("kunden_bearbeiten/kundenNr/{customer_nr}", name="edit_customer", methods={"GET","POST"})
     */
    public function editCustomer(Request $request, $customer_nr): RedirectResponse|JsonResponse|Response
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

        $customer = $this->customerService->getCustomerRepository()->findOneBy(['customer_nr' => $customer_nr]);
        $form = $this->createForm(EditCustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Die Änderungen der Kundendaten wurden erfolgreich gespeichert.';
                $this->customerService->updateCustomer($requestData);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Die Änderungen der Kundendaten konnte nicht gespeichert werden.';

            return new JsonResponse($responseData);
        }

        return $this->render(
            'customer/edit.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Artikel bearbeiten',
                'editCustomerForm' => $form->createView(),
                'customers' => json_decode($this->getAllCustomers()->getContent()),
            ]
        );
    }

    /**
     * @Route("/kunden_anlegen", name="create_customer")
     */
    public function createCustomer(): Response
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
}
