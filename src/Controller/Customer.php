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
use WebWMS\Form\CustomerType;
use WebWMS\Services\CustomerService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Customer
 */
class Customer extends AbstractController
{
    /** @var CustomerService */
    private $customerService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        CustomerService $customerService,
        Requirements $requirements
    ) {
        $this->customerService = $customerService;
        $this->requirements = $requirements;
    }

    public function getAllCustomers(): array
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
    public function addNewCustomer(Request $request)
    {
        //$params = $request->request->all();
        //dd($params['customer']['customer_name']);

        $form = $this->createForm(CustomerType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->customerService->addNewCustomer($request);
            $this->addFlash('success', 'Der Kunde wurde erfolgreich angelegt.');

            return $this->redirectToRoute('create_customer');
        }

        return $this->render('customer/add_customer.html.twig',
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

    /**
     * Get last customer
     */
    public function getLastCustomer(): array
    {
        return $this->customerService->getLastCustomer();
    }

    /**
     * @Route("/kunden", name="customer")
     */
    public function index(): Response
    {
        return $this->render('customer/index.html.twig',
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
     * @Route("/kunden_anlegen", name="create_customer")
     */
    public function createCustomer(): Response
    {
        return $this->render('customer/index.html.twig',
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
