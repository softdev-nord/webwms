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
use WebWMS\Service\Supplier\SupplierService;

/**
 * @package:    WebWMS\Controller\Api
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * @Route("/api",name="api_")
 *
 * Class        Supplier
 */
class Supplier extends AbstractFOSRestController
{
    public function __construct(
        private SupplierService $supplierService
    ) {
    }

    /**
     * @Rest\Get("/suppliers/{supplierId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a Supplier by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Supplier::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Supplier")
     *
     * @throws EntityNotFoundException
     */
    public function getSupplier(int $supplierId): View
    {
        $supplier = $this->supplierService->getSupplierApi($supplierId);
        if (!$supplier) {
            throw new EntityNotFoundException('Supplier with id '.$supplierId.' does not exist!');
        }

        return $this->view($supplier, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/suppliers")
     *
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a collection of Suppliers",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Supplier::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Supplier")
     */
    public function getSuppliers(): View
    {
        $suppliers = $this->supplierService->getAllSuppliersApi();

        return $this->view($suppliers, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/suppliers")
     *
     * @OA\Response(
     *     response=200,
     *     description="Creates a Supplier",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Supplier::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Supplier")
     */
    public function postSupplier(Request $request): View
    {
        $supplier = $this->supplierService->addSupplierApi(
            $request->get('supplier_id'),
            $request->get('supplier_nr'),
            $request->get('supplier_name'),
            $request->get('supplier_address_addition'),
            $request->get('supplier_address_street'),
            $request->get('supplier_address_street_nr'),
            $request->get('supplier_address_country_code'),
            $request->get('supplier_address_zipcode'),
            $request->get('supplier_address_city')
        );

        return $this->view($supplier, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/suppliers/{supplierId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Replace a Supplier by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Supplier::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Supplier")
     *
     * @throws EntityNotFoundException
     */
    public function putSupplier(int $supplierId, Request $request): View
    {
        $supplier = $this->getSupplier($supplierId);

        if (!$supplier) {
            throw new EntityNotFoundException('Supplier with id '.$supplierId.' does not exist!');
        }

        $supplier = $this->supplierService->updateSupplierApi(
            $supplierId,
            $request->get('supplierId'),
            $request->get('supplier_nr'),
            $request->get('supplier_name'),
            $request->get('supplier_address_addition'),
            $request->get('supplier_address_street'),
            $request->get('supplier_address_street_nr'),
            $request->get('supplier_address_country_code'),
            $request->get('supplier_address_zipcode'),
            $request->get('supplier_address_city')
        );

        return $this->view($supplier, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/suppliers/{supplierId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Removes a Supplier by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Supplier::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Supplier")
     *
     * @throws EntityNotFoundException
     */
    public function deleteSupplier(int $supplierId): View
    {
        $this->supplierService->deleteSupplierApi($supplierId);

        return $this->view([], Response::HTTP_NO_CONTENT);
    }
}
