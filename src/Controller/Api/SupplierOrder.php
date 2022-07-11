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
use WebWMS\Service\SupplierOrderService;

/**
 * @package:    WebWMS\Controller\Api
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * @Route("/api",name="api_")
 *
 * Class        SupplierOrder
 */
class SupplierOrder extends AbstractFOSRestController
{
    /** @var SupplierOrderService */
    private $supplierOrderService;

    public function __construct(SupplierOrderService $supplierOrderService)
    {
        $this->supplierOrderService = $supplierOrderService;
    }

    /**
     * @Rest\Get("/supplierOrders/{supplierOrderId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a Order by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=SupplierOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="SupplierOrder")
     *
     * @throws EntityNotFoundException
     */
    public function getSupplierOrder(int $supplierOrderId): View
    {
        $supplierOrder = $this->supplierOrderService->getSupplierOrderApi($supplierOrderId);

        if (!$supplierOrder) {
            throw new EntityNotFoundException('Order with id '.$supplierOrderId.' does not exist!');
        }

        return View::create($supplierOrder, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/supplierOrders")
     *
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a collection of Orders",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=SupplierOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="SupplierOrder")
     */
    public function getSupplierOrders(): View
    {
        $supplierOrder = $this->supplierOrderService->getAllSupplierOrdersApi();

        return View::create($supplierOrder, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/supplierOrders")
     *
     * @OA\Response(
     *     response=200,
     *     description="Creates a Order",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=SupplierOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="SupplierOrder")
     */
    public function postSupplierOrder(Request $request): View
    {
        $supplierOrder = $this->supplierOrderService->addSupplierOrderApi(
            $request->get('supplier_order_id'),
            $request->get('usr_id'),
            $request->get('supplier_id'),
            $request->get('supplier_order_nr'),
            $request->get('supplier_order_reference'),
            $request->get('supplier_order_date'),
            $request->get('supplier_order_order_date'),
        );

        return View::create($supplierOrder, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/supplierOrders/{supplierOrderId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Replace a Order by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=SupplierOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="SupplierOrder")
     *
     * @throws EntityNotFoundException
     */
    public function putSupplierOrder(int $supplierOrderId, Request $request): View
    {
        $supplierOrder = $this->getSupplierOrder($supplierOrderId);

        if (!$supplierOrder) {
            throw new EntityNotFoundException('Order with id '.$supplierOrderId.' does not exist!');
        }

        $supplierOrder = $this->supplierOrderService->updateSupplierOrderApi(
            $supplierOrderId,
            $request->get('supplier_order_id'),
            $request->get('usr_id'),
            $request->get('supplier_id'),
            $request->get('supplier_order_nr'),
            $request->get('supplier_order_reference'),
            $request->get('supplier_order_date'),
            $request->get('supplier_order_order_date'),
        );

        return View::create($supplierOrder, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/supplierOrders/{supplierOrderId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Removes a Order by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=SupplierOrder::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="SupplierOrder")
     *
     * @throws EntityNotFoundException
     */
    public function deleteOrder(int $supplierOrderId): View
    {
        $this->supplierOrderService->deleteSupplierOrderApi($supplierOrderId);

        return View::create([], Response::HTTP_NO_CONTENT);
    }
}
