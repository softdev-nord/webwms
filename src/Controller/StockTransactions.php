<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Service\BookingMethod\BookingMethodService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportRequestService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockTransactions
 */
class StockTransactions extends AbstractController
{
    public function __construct(
        private BookingMethodService $bookingMethodService,
        private TransportRequestService $transportRequestService,
        private StockLocationService $stockLocationService
    ) {
    }

    /**
     * SI101 Einlagern direkt.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in', name: 'stock_in')]
    public function stockIn(Request $request): RedirectResponse|Response|null
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI102 Zugang aus Wareneingang.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_from_goods_receipt', name: 'stock_in_from_goods_receipt')]
    public function stockInFromGoodsReceipt(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI103 Zugang aus Produktion.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_from_production', name: 'stock_in_from_production')]
    public function stockInFromProduction(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI104 Rückgabe von Kostenstelle.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_from_cost_centre', name: 'stock_in_from_cost_centre')]
    public function stockInFromCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI105 Einlagern in Container.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_into_container', name: 'stock_in_into_container')]
    public function stockInIntoContainer(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI106 WE zur Bestellung.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_for_supplier_order', name: 'stock_in_for_supplier_order')]
    public function stockInForSupplierOrder(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI107 Einlagern mit Ladehilfsmittel.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_using_loading_equipment', name: 'stock_in_using_loading_equipment')]
    public function stockInUsingLoadingEquipment(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI111 Einlagern direkt in WE-Zone.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_into_receiving_area', name: 'stock_in_into_receiving_area')]
    public function stockInIntoReceivingArea(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * ST112 Rückgabe von Kostenstelle.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_transfer_from_cost_centre', name: 'stock_transfer_from_cost_centre')]
    public function stockTransferFromCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI113 Einlagern direkt in Kostenstelle.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_into_cost_centre', name: 'stock_in_into_cost_centre')]
    public function stockInIntoCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SI114 Einlagern direkt in WA-Zone.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_into_dispatch_area', name: 'stock_in_into_dispatch_area')]
    public function stockInIntoDispatchArea(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO151 Auslagern direkt.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out', name: 'stock_out')]
    public function stockOut(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO152 Auslagern auf Kostenstelle.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_to_cost_centre', name: 'stock_out_to_cost_centre')]
    public function stockOutToCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO153 Ausleihen auf Kostenstelle.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/lending_to_cost_centre', name: 'lending_to_cost_centre')]
    public function lendingToCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO155 Auslagern aus Container.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_from_container', name: 'stock_out_from_container')]
    public function stockOutFromContainer(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO156 Auslagern aus Kostenstelle.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_from_cost_centre', name: 'stock_out_from_cost_centre')]
    public function stockOutFromCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO157 Auslagern direkt aus WA-Zone.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_from_dispatch_area', name: 'stock_out_from_dispatch_area')]
    public function stockOutFromDispatchArea(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO158 Auftrag auslagern.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_by_order', name: 'stock_out_by_order')]
    public function stockOutByOrder(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO159 Auslagern direkt aus WE-Zone.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_from_receiving_area', name: 'stock_out_from_receiving_area')]
    public function stockOutFromReceivingArea(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO181 Auftrag auslagern (Auftrag-Liste).
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_order_list', name: 'stock_out_order_list')]
    public function stockOutOrderList(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO182 Auftrag auslagern mit Kostenstelle (Auftrag-Liste).
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_using_cost_centre', name: 'stock_out_using_cost_centre')]
    public function stockOutUsingCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * ST182 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste).
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_transfer_to_cost_centre', name: 'stock_transfer_to_cost_centre')]
    public function stockTransferToCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO187 Auftrag auslagern in WA-Zone.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_to_dispatch_area', name: 'stock_out_to_dispatch_area')]
    public function stockOutToDispatchArea(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * SO188 Sammelkommissionierung auf Kostenstelle.
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_order_consolidation_to_cost_centre', name: 'stock_out_order_consolidation_to_cost_centre')]
    public function stockOutOrderConsolidationToCostCentre(Request $request): RedirectResponse|Response|null
    {
        $bookingMethod = $request->attributes->get('_route');

        return $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
    }

    /**
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @throws \Exception
     */
    #[Route('/stock_in_final', name: 'stock_in_final')]
    public function stockInFinal(Request $request): void
    {
        $user = '';
        if ($this->getUser() !== null) {
            $user = $this->getUser()->getUserIdentifier();
        }

        $this->transportRequestService->createTransportRequest($request, $user);
    }

    /**
     * @param array<string> $stockLocations
     */
    public function generateSuId(array $stockLocations): int
    {
        $count = count(array_keys($stockLocations));
        $suId = $this->transportRequestService->getLastStockUnit();
        for ($i = 0; $i <= $count; ++$i) {
            $suId += $i;
        }

        return $suId;
    }

    #[Route('/edit_pre_selected_stock_location/id/{stockLocationId}', name: 'edit_pre_selected_stock_location')]
    public function editPreSelectedStockLocation(Request $request): Response
    {
        $stockLocationId = strval($request->attributes->get('stockLocationId'));
        /* @var $stockSystem \WebWMS\Entity\StockLocation */
        $stockSystem = $this->stockLocationService->getStockLocationDetailsById($stockLocationId);

        /**
         * @phpstan-ignore-next-line
         */
        $preSelectedStockLocation = $this->stockLocationService->getAllFreeStockLocations($stockSystem[0]->getStockLocationDesc());

        return $this->render(
            'stock/stock_in_edit.html.twig',
            [
                'freeStockLocations' => $preSelectedStockLocation,
                'editArticle' => true,
            ]
        );
    }
}
