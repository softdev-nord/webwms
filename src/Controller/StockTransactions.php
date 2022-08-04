<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Service\BookingMethod\BookingMethodConstants;
use WebWMS\Service\BookingMethod\BookingMethodService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockTransactions
 */
class StockTransactions
{
    public function __construct(
        private BookingMethodService $bookingMethodService,
        private BookingMethodConstants $bookingMethodConstants
    ) {
    }

    /**
     * SI101 Einlagern direkt
     *
     * @Route("/stock_in", name="stock_in")
     * @throws EntityNotFoundException
     */
    public function stockIn()
    {
        $bookingMethod = $this->bookingMethodConstants::SI101;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI102 Zugang aus Wareneingang
     *
     * @Route("/stock_in_from_goods_receipt", name="stock_in_from_goods_receipt")
     * @throws EntityNotFoundException
     */
    public function stockInFromGoodsReceipt()
    {
        $bookingMethod = $this->bookingMethodConstants::SI102;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI103 Zugang aus Produktion
     *
     * @Route("/stock_in_from_production", name="stock_in_from_production")
     * @throws EntityNotFoundException
     */
    public function stockInFromProduction()
    {
        $bookingMethod = $this->bookingMethodConstants::SI103;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI104 Rückgabe von Kostenstelle
     *
     * @Route("/stock_in_from_cost_centre", name="stock_in_from_cost_centre")
     * @throws EntityNotFoundException
     */
    public function stockInFromCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SI104;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI105 Einlagern in Container
     *
     * @Route("/stock_in_into_container", name="stock_in_into_container")
     * @throws EntityNotFoundException
     */
    public function stockInIntoContainer()
    {
        $bookingMethod = $this->bookingMethodConstants::SI105;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI106 WE zur Bestellung
     *
     * @Route("/stock_in_for_supplier_order", name="stock_in_for_supplier_order")
     * @throws EntityNotFoundException
     */
    public function stockInForSupplierOrder()
    {
        $bookingMethod = $this->bookingMethodConstants::SI106;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI107 Einlagern mit Ladehilfsmittel
     *
     * @Route("/stock_in_using_loading_equipment", name="stock_in_using_loading_equipment")
     * @throws EntityNotFoundException
     */
    public function stockInUsingLoadingEquipment()
    {
        $bookingMethod = $this->bookingMethodConstants::SI107;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI111 Einlagern direkt in WE-Zone
     *
     * @Route("/stock_in_into_receiving_area", name="stock_in_into_receiving_area")
     * @throws EntityNotFoundException
     */
    public function stockInIntoReceivingArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SI111;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * ST112 Rückgabe von Kostenstelle
     *
     * @Route("/stock_transfer_from_cost_centre", name="stock_transfer_from_cost_centre")
     * @throws EntityNotFoundException
     */
    public function stockTransferFromCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::ST112;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI113 Einlagern direkt in Kostenstelle
     *
     * @Route("/stock_in_into_cost_centre", name="stock_in_into_cost_centre")
     * @throws EntityNotFoundException
     */
    public function stockInIntoCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SI113;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI114 Einlagern direkt in WA-Zone
     *
     * @Route("/stock_in_into_dispatch_area", name="stock_in_into_dispatch_area")
     * @throws EntityNotFoundException
     */
    public function stockInIntoDispatchArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SI114;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO151 Auslagern direkt
     *
     * @Route("/stock_out", name="stock_out")
     * @throws EntityNotFoundException
     */
    public function stockOut()
    {
        $bookingMethod = $this->bookingMethodConstants::SO151;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO152 Auslagern auf Kostenstelle
     *
     * @Route("/stock_out_to_cost_centre", name="stock_out_to_cost_centre")
     * @throws EntityNotFoundException
     */
    public function stockOutToCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO152;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO153 Ausleihen auf Kostenstelle
     *
     * @Route("/lending_to_cost_centre", name="lending_to_cost_centre")
     * @throws EntityNotFoundException
     */
    public function lendingToCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO153;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO155 Auslagern aus Container
     *
     * @Route("/stock_out_from_container", name="stock_out_from_container")
     * @throws EntityNotFoundException
     */
    public function stockOutFromContainer()
    {
        $bookingMethod = $this->bookingMethodConstants::SO155;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO156 Auslagern aus Kostenstelle
     *
     * @Route("/stock_out_from_cost_centre", name="stock_out_from_cost_centre")
     * @throws EntityNotFoundException
     */
    public function stockOutFromCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO156;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO157 Auslagern direkt aus WA-Zone
     *
     * @Route("/stock_out_from_dispatch_area", name="stock_out_from_dispatch_area")
     * @throws EntityNotFoundException
     */
    public function stockOutFromDispatchArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SO157;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO158 Auftrag auslagern
     *
     * @Route("/stock_out_by_order", name="stock_out_by_order")
     * @throws EntityNotFoundException
     */
    public function stockOutByOrder()
    {
        $bookingMethod = $this->bookingMethodConstants::SO158;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO159 Auslagern direkt aus WE-Zone
     *
     * @Route("/stock_out_from_receiving_area", name="stock_out_from_receiving_area")
     * @throws EntityNotFoundException
     */
    public function stockOutFromReceivingArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SO159;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO181 Auftrag auslagern (Auftrag-Liste)
     *
     * @Route("/stock_out_order_list", name="stock_out_order_list")
     * @throws EntityNotFoundException
     */
    public function stockOutOrderList()
    {
        $bookingMethod = $this->bookingMethodConstants::SO181;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO182 Auftrag auslagern mit Kostenstelle (Auftrag-Liste)
     *
     * @Route("/stock_out_using_cost_centre", name="stock_out_using_cost_centre")
     * @throws EntityNotFoundException
     */
    public function stockOutUsingCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO182;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * ST182 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste)
     *
     * @Route("/stock_transfer_to_cost_centre", name="stock_transfer_to_cost_centre")
     * @throws EntityNotFoundException
     */
    public function stockTransferToCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::ST183;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO187 Auftrag auslagern in WA-Zone
     *
     * @Route("/stock_out_to_dispatch_area", name="stock_out_to_dispatch_area")
     * @throws EntityNotFoundException
     */
    public function stockOutToDispatchArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SO187;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO188 Sammelkommissionierung auf Kostenstelle
     *
     * @Route("/stock_out_order_consolidation_to_cost_centre", name="stock_out_order_consolidation_to_cost_centre")
     * @throws EntityNotFoundException
     */
    public function stockOutOrderConsolidationToCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO188;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }
}
