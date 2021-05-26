<?php

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Entity\Customer as Customers;
use WebWMS\Repository\CustomerRepository;

class Customer extends AbstractController
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAllCustomers()
    {
        $customers = $this->customerRepository->findAll();

        if (!$customers) {
            throw $this->createNotFoundException('Keine Kunden gefunden');
        }

        return $customers;
    }

    /**
     * @Route("/order_customer_ajax", name="order_customer_ajax")
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllCustomersAjax()
    {
        return $this->getDoctrine()->getRepository(Customers::class)->getCustomers();
    }

    /**
     * @Route("/kunden", name="customer")
     */
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('customer/index.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Kundenübersicht',
            'data' => $this->getAllCustomersAjax(),
            'customer' => $this->getAllCustomers(),
        ]);
    }

    /**
     * @Route("/kunden_anlegen", name="create_customer")
     */
    public function createCustomer(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('customer/index.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Kundenübersicht',
            'data' => $this->getAllCustomersAjax(),
            'customer' => $this->getAllCustomers(),
        ]);
    }
}
