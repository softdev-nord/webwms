<?php

namespace WebWMS\Controller;

use WebWMS\Controller\Requirements as Requirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
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
            throw $this->createNotFoundException(
                'Keine Kunden gefunden'
            );
        }

        return $customers;
    }

    /**
     * @Route("/kunden", name="customer")
     */
    public function index()
    {
        return $this->render('customer/index.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Kundenübersicht',
            'customer' => $this->getAllCustomers(),
        ]);
    }
}
