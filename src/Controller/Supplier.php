<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Form\SupplierType;
use WebWMS\Service\SupplierService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Supplier
 */
class Supplier extends AbstractController
{
    /** @var SupplierService */
    private $supplierService;

    /** @var Requirements */
    private $requirements;

    public function __construct(
        SupplierService $supplierService,
        Requirements $requirements
    ) {
        $this->supplierService = $supplierService;
        $this->requirements = $requirements;
    }

    public function getAllSuppliers(): array
    {
        return $this->supplierService->getAllSuppliers();
    }

    /**
     * @Route("/order_supplier_ajax", name="order_supplier_ajax")
     *
     * @throws Exception
     */
    public function getAllSuppliersAjax(): JsonResponse
    {
        return $this->supplierService->getAllSuppliersAjax();
    }

    /**
     * @Route("/lieferanten", name="supplier")
     *
     * @throws Exception
     */
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('supplier/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lieferantenübersicht',
                'data' => $this->getAllSuppliersAjax(),
                'supplier' => $this->getAllSuppliers(),
            ]
        );
    }

    /**
     * @Route("/lieferant_anlegen", name="add_supplier")
     *
     * @return RedirectResponse|Response
     */
    public function addNewSupplier(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(SupplierType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->supplierService->addNewSupplier($request);
            $this->addFlash('success', 'Der Lieferant wurde erfolgreich angelegt.');

            return $this->redirectToRoute('add_supplier');
        }

        return $this->render('supplier/add_supplier.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Lieferant anlegen',
                'lastId' => $this->getLastSupplier()[0],
                'addSupplierForm' => $form->createView(),
            ]
        );
    }

    /**
     * Get last supplier.
     */
    public function getLastSupplier(): array
    {
        return $this->supplierService->getLastSupplier();
    }
}
