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
use WebWMS\Service\Customer\CustomerService;

/**
 * @package:    WebWMS\Controller\Api
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * @Route("/api",name="api_")
 *
 * Class        Customer
 */
class Customer extends AbstractFOSRestController
{
    /**
     * @var CustomerService
     */
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @Rest\Get("/customers/{customerId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a Customer by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Customer")
     *
     * @throws EntityNotFoundException
     */
    public function getCustomer(int $customerId): View
    {
        $customer = $this->customerService->getCustomerApi($customerId);

        if (!$customer) {
            throw new EntityNotFoundException('Customer with id '.$customerId.' does not exist!');
        }

        return View::create($customer, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/customers")
     *
     * @OA\Response(
     *     response=200,
     *     description="Retrieves a collection of Customers",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Customer")
     */
    public function getCustomers(): View
    {
        $customers = $this->customerService->getAllCustomersApi();

        return View::create($customers, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/customers")
     *
     * @OA\Response(
     *     response=200,
     *     description="Creates a Customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Customer")
     */
    public function postCustomer(Request $request): View
    {
        $article = $this->customerService->addCustomerApi(
            $request->get('customer_id'),
            $request->get('customer_nr'),
            $request->get('customer_name'),
            $request->get('customer_address_addition'),
            $request->get('customer_address_street'),
            $request->get('customer_address_street_nr'),
            $request->get('customer_country_code'),
            $request->get('customer_zip_code'),
            $request->get('customer_city')
        );

        return View::create($article, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/customers/{customerId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Replace a Customer by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Customer")
     *
     * @throws EntityNotFoundException
     */
    public function putCustomer(int $customerId, Request $request): View
    {
        $customer = $this->getCustomer($customerId);

        if (!$customer) {
            throw new EntityNotFoundException('Customer with id '.$customerId.' does not exist!');
        }

        $customer = $this->customerService->updateCustomerApi(
            $customerId,
            $request->get('customer_id'),
            $request->get('customer_nr'),
            $request->get('customer_name'),
            $request->get('customer_address_addition'),
            $request->get('customer_address_street'),
            $request->get('customer_address_street_nr'),
            $request->get('customer_country_code'),
            $request->get('customer_zip_code'),
            $request->get('customer_city')
        );

        return View::create($customer, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/customers/{customerId}")
     *
     * @OA\Response(
     *     response=200,
     *     description="Removes a Customer by Id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"full"}))
     *     ),
     * )
     * @OA\Tag(name="Customer")
     */
    public function deleteCustomer(int $customerId): View
    {
        $this->customerService->deleteCustomerApi($customerId);

        return View::create([], Response::HTTP_NO_CONTENT);
    }
}
