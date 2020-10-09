<?php

namespace WebWMS\Controller;

use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Entity\Order AS Orders;
use WebWMS\Entity\OrderPos;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Order extends AbstractController
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @Route("/oders_ajax", name="orders_ajax")
     */
    public function getAllOrders()
    {
        return $this->getDoctrine()->getRepository(Orders::class)->getAllOrders();
    }

    /**
     * @Route("/order_pos_ajax", name="order_pos_ajax")
     */
    public function getAllOrderPos()
    {
        return $this->getDoctrine()->getRepository(OrderPos::class)->getAllOrderPos();
    }

    /**
     * @Route("/bestellungen", name="orders")
     */
    public function index()
    {
        return $this->render('order/index.html.twig', [
            'appName' => Requirements::APP_NAME,
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'page' => 'Übersicht Bestellungen',
            'orders' => $this->getAllOrders(),
            'order_pos' => $this->getAllOrderPos()
        ]);
    }
}
