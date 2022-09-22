<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api;

use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Service\CustomerOrderService;

/**
 * @package:    WebWMS\Controller\Api
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * @Route("/api",name="api_")
 *
 * Class        CustomerOrder
 */
class CustomerOrder extends AbstractFOSRestController
{
    public function __construct(
        private CustomerOrderService $customerOrderService
    ) {
    }

    /**
     * @Rest\Get("/customerOrders/{customerOrderId}")
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a Customer Order by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=CustomerOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="CustomerOrder")
     *
     * @throws EntityNotFoundException
     */
    public function getCustomerOrder(int $customerOrderId): View
    {
        $customerOrder = $this->customerOrderService->getCustomerOrderApi($customerOrderId);

        if (!$customerOrder) {
            throw new EntityNotFoundException('Customer order with id '.$customerOrderId.' does not exist!');
        }

        return $this->view($customerOrder, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/customerOrders")
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a collection of Customer Orders",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=CustomerOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="CustomerOrder")
     */
    public function getCustomerOrders(): View
    {
        $customerOrders = $this->customerOrderService->getAllCustomerOrdersApi();

        return $this->view($customerOrders, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/customerOrders")
     * @OA\Response(
     *     response=200,
     *     description="Creates a Customer Order",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=CustomerOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="CustomerOrder")
     */
    public function postCustomerOrder(Request $request): View
    {
        $customerOrder = $this->customerOrderService->addCustomerOrderApi(
            $request->get('customer_order_id'),
            $request->get('usr_id'),
            $request->get('customer_id'),
            $request->get('customer_order_nr'),
            $request->get('customer_order_reference'),
            $request->get('customer_order_date'),
            $request->get('customer_order_order_date'),
        );

        return $this->view($customerOrder, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/customerOrders/{customerOrderId}")
     * @OA\Response(
     *     response=200,
     *     description="Replace a Customer Order by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=CustomerOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="CustomerOrder")
     *
     * @throws EntityNotFoundException
     */
    public function putCustomerOrder(int $customerOrderId, Request $request): View
    {
        $customerOrder = $this->getCustomerOrder($customerOrderId);

        if (!$customerOrder) {
            throw new EntityNotFoundException('Customer order with id '.$customerOrderId.' does not exist!');
        }

        $customerOrder = $this->customerOrderService->updateCustomerOrderApi(
            $customerOrderId,
            $request->get('customer_order_id'),
            $request->get('usr_id'),
            $request->get('customer_id'),
            $request->get('customer_order_nr'),
            $request->get('customer_order_reference'),
            $request->get('customer_order_date'),
            $request->get('customer_order_order_date')
        );

        return $this->view($customerOrder, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/customerOrders/{customerOrderId}")
     * @OA\Response(
     *     response=200,
     *     description="Removes a Customer Order by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=CustomerOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="CustomerOrder")
     *
     * @throws EntityNotFoundException
     */
    public function deleteCustomerOrder(int $customerOrderId): View
    {
        $this->customerOrderService->deleteCustomerOrderApi($customerOrderId);

        return $this->view([], Response::HTTP_NO_CONTENT);
    }
}
