<?php

namespace WebWMS\Controller;

use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Entity\CustomerOrder AS CustomerOrders;
use WebWMS\Entity\CustomerOrderPos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CustomerOrder extends AbstractController
{
    /**
     * @Route("/customer_order_ajax", name="customer_order_ajax")
     */
    public function getAllCustomerOrders()
    {
        return $this->getDoctrine()->getRepository(CustomerOrders::class)->getAllCustomerOrders();
    }

    /**
     * @Route("/customer_order_pos_ajax", name="customer_order_pos_ajax")
     */
    public function getAllCustomerOrdersPos()
    {
        return $this->getDoctrine()->getRepository(CustomerOrderPos::class)->getAllCustomerOrderPos();
    }

    /**
     * @Route("/auftrag", name="customer_orders")
     */
    public function index()
    {
        return $this->render('customer_order/index.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Übersicht Bestellungen',
            'customer_order' => $this->getAllCustomerOrders(),
            'customer_order_pos' => $this->getAllCustomerOrdersPos()
        ]);
    }
}
