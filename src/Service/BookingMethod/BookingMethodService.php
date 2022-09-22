<?php

declare(strict_types=1);

namespace WebWMS\Service\BookingMethod;

use Doctrine\ORM\EntityNotFoundException;

/**
 * @package:    WebWMS\Service\BookingMethod
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        BookingMethodService
 */
class BookingMethodService
{
    public function __construct(
        private BookingMethodConstants $bookingMethodConstants
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getBookingMethod($bookingMethod)
    {
        match ($bookingMethod) {
            $this->bookingMethodConstants::SI101 => $this->stockIn(), // SI101 Einlagern direkt
            $this->bookingMethodConstants::SI102 => $this->stockInFromGoodsReceipt(), // SI102 Zugang aus Wareneingang
            $this->bookingMethodConstants::SI103 => $this->stockInFromProduction(), // SI103 Zugang aus Produktion
            $this->bookingMethodConstants::SI104 => $this->stockInFromCostCentre(), // SI104 Rückgabe von Kostenstelle
            $this->bookingMethodConstants::SI105 => $this->stockInIntoContainer(), // SI105 Einlagern in Container
            $this->bookingMethodConstants::SI106 => $this->stockInForSupplierOrder(), // SI106 WE zur Bestellung
            $this->bookingMethodConstants::SI107 => $this->stockInUsingLoadingEquipment(), // SI107 Einlagern mit Ladehilfsmittel
            $this->bookingMethodConstants::SI111 => $this->stockInIntoReceivingArea(), // SI111 Einlagern direkt in WE-Zone
            $this->bookingMethodConstants::ST112 => $this->stockTransferFromCostCentre(), // ST112 Rückgabe von Kostenstelle
            $this->bookingMethodConstants::SI113 => $this->stockInIntoCostCentre(), // SI113 Einlagern direkt in Kostenstelle
            $this->bookingMethodConstants::SI114 => $this->stockInIntoDispatchArea(), // SI114 Einlagern direkt in WA-Zone
            $this->bookingMethodConstants::SO151 => $this->stockOut(), // SO151 Auslagern direkt
            $this->bookingMethodConstants::SO152 => $this->stockOutToCostCentre(), // SO152 Auslagern auf Kostenstelle
            $this->bookingMethodConstants::SO153 => $this->lendingToCostCentre(), // SO153 Ausleihen auf Kostenstelle
            $this->bookingMethodConstants::SO155 => $this->stockOutFromContainer(), // SO155 Auslagern aus Container
            $this->bookingMethodConstants::SO156 => $this->stockOutFromCostCentre(), // SO156 Auslagern aus Kostenstelle
            $this->bookingMethodConstants::SO157 => $this->stockOutFromDispatchArea(), // SO157 Auslagern direkt aus WA-Zone
            $this->bookingMethodConstants::SO158 => $this->stockOutByOrder(), // SO158 Auftrag auslagern
            $this->bookingMethodConstants::SO159 => $this->stockOutFromReceivingArea(), // SO159 Auslagern direkt aus WE-Zone
            $this->bookingMethodConstants::SO181 => $this->stockOutOrderList(), // SO181 Auftrag auslagern (Auftrag-Liste)
            $this->bookingMethodConstants::SO182 => $this->stockOutUsingCostCentre(), // SO182 Auftrag auslagern mit Kostenstelle (Auftrag-Liste)
            $this->bookingMethodConstants::ST183 => $this->stockTransferToCostCentre(), // ST182 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste)
            $this->bookingMethodConstants::SO187 => $this->stockOutToDispatchArea(), // SO187 Auftrag auslagern in WA-Zone
            $this->bookingMethodConstants::SO188 => $this->stockOutOrderConsolidationToCostCentre(), // SO188 Sammelkommissionierung auf Kostenstelle
            default => throw new EntityNotFoundException('Buchungsmethode mit der Nr. '.$bookingMethod.' wurde nicht gefunden!'),
        };
    }

    /**
     * SI101 Einlagern direkt.
     */
    public function stockIn(): string
    {
        return 'SI101 Einlagern direkt';
    }

    /**
     * SI102 Zugang aus Wareneingang.
     */
    public function stockInFromGoodsReceipt()
    {
        // TODO: Implement logic
    }

    /**
     * SI103 Zugang aus Produktion.
     */
    public function stockInFromProduction()
    {
        // TODO: Implement logic
    }

    /**
     * SI104 Rückgabe von Kostenstelle.
     */
    public function stockInFromCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * SI105 Einlagern in Container.
     */
    public function stockInIntoContainer()
    {
        // TODO: Implement logic
    }

    /**
     * SI106 WE zur Bestellung.
     */
    public function stockInForSupplierOrder()
    {
        // TODO: Implement logic
    }

    /**
     * SI107 Einlagern mit Ladehilfsmittel.
     */
    public function stockInUsingLoadingEquipment()
    {
        // TODO: Implement logic
    }

    /**
     * SI111 Einlagern direkt in WE-Zone.
     */
    public function stockInIntoReceivingArea()
    {
        // TODO: Implement logic
    }

    /**
     * ST112 Rückgabe von Kostenstelle.
     */
    public function stockTransferFromCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * SI113 Einlagern direkt in Kostenstelle.
     */
    public function stockInIntoCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * SI114 Einlagern direkt in WA-Zone.
     */
    public function stockInIntoDispatchArea()
    {
        // TODO: Implement logic
    }

    /**
     * SO151 Auslagern direkt.
     */
    public function stockOut()
    {
        // TODO: Implement logic
    }

    /**
     * SO152 Auslagern auf Kostenstelle.
     */
    public function stockOutToCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * SO153 Ausleihen auf Kostenstelle.
     */
    public function lendingToCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * SO155 Auslagern aus Container.
     */
    public function stockOutFromContainer()
    {
        // TODO: Implement logic
    }

    /**
     * SO156 Auslagern aus Kostenstelle.
     */
    public function stockOutFromCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * SO157 Auslagern direkt aus WA-Zone.
     */
    public function stockOutFromDispatchArea()
    {
        // TODO: Implement logic
    }

    /**
     * SO158 Auftrag auslagern.
     */
    public function stockOutByOrder()
    {
        // TODO: Implement logic
    }

    /**
     * SO159 Auslagern direkt aus WE-Zone.
     */
    public function stockOutFromReceivingArea()
    {
        // TODO: Implement logic
    }

    /**
     * SO181 Auftrag auslagern (Auftrag-Liste).
     */
    public function stockOutOrderList()
    {
        // TODO: Implement logic
    }

    /**
     * SO182 Auftrag auslagern mit Kostenstelle (Auftrag-Liste).
     */
    public function stockOutUsingCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * ST182 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste).
     */
    public function stockTransferToCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * SO187 Auftrag auslagern in WA-Zone.
     */
    public function stockOutToDispatchArea()
    {
        // TODO: Implement logic
    }

    /**
     * SO188 Sammelkommissionierung auf Kostenstelle.
     */
    public function stockOutOrderConsolidationToCostCentre()
    {
        // TODO: Implement logic
    }

    /**
     * ST201 Umlagern.
     */
    public function stockTransferBetween()
    {
        // TODO: Implement logic
    }

    /**
     * ST203 Bestandskorrektur.
     */
    public function stockCorrection()
    {
        // TODO: Implement logic
    }

    /**
     * ST207 Umlagerung aus WE-Zone ins LV-Lager (aus Artikelbelegung).
     */
    public function stockTransferFromReceivingAreaToStock()
    {
        // TODO: Implement logic
    }

    /**
     * ST208 Umlagerung aus LV-Lager in WA-Zone (aus Artikelbelegung).
     */
    public function stockTransferFromStockToDispatchArea()
    {
        // TODO: Implement logic
    }

// 207,  'move receicvings to storage'              bfid_uml_we_lv      207   // umlagerung aus we-zone ins lv-lager (aus artikelbelegung)
// 208,  'move storage to dispatch'                 bfid_uml_lv_wa      208   // umlagerung aus lv-lager in wa-zone
// 209,  'move between cost centres'                bfid_uml_kst_kst    209   // umlagern von kst nach kst
// 210,  'move between stock units'                 bfid_uml_con_con    210   // umlagern container -> container
// 211,  'move storage to stock units'              bfid_uml_lv_con     211   // umlagern lv-lager -> container
// 212,  'move stock unit to storage'               bfid_uml_con_lv     212   // umlagern container -> lv-lager
// 213,  'transfer with complete tools'             bfid_uml_lv_set     213   // umlagern in komplettwerkzeug
// 214,  'transfer from complete tools'             bfid_uml_set_lv     214   // umlagern aus komplettwerkzeug
// 215,  'inventory'                                bfid_kor_inv        215   // inventurbuchung, falls mengenkorrektur
//                                                                            // einen lpbestand < 0 ergibt
//                                                  bfid_uml_lv_kst     216   // umlagerung aus lv-lager in kst
// 217,  'transfer with balance'                    bfid_uml_waage      217   // umlagern direkt mit waage
// 218,  'transfer su to dispatch area'             bfid_uml_con_wa     218   // umlagern container -> wa-zone
// 219,  'transfer dispatch area to su'             bfid_uml_wa_con     219   // umlagern wa-zone -> container
// 220,  'transfer dispatch area to su'             bfid_kor_vlm        220   // korrektur abbuchung der vorlaufmenge
// 221,  'transfer cc + su to storage'              bfid_uml_kst_con_lv 221   // lagereinheiten vom kst nach container umlagern und
//                                                                            // container vom kst nach lv umlagern
// 222,  'receivings to dispatch 222'               bfid_uml_we_wa      222   // umlagern vom we-zone nach wa-zone
// 223,  'receivings to dispatch 223'               bfid_uml_we_wa_fol  223   // umlagern vom we-zone nach wa-zone als follower
//
// 351,  'quality control tools'                    bfid_iwe            351   // istwertaufnahme (einzelteil-pruefung)
// 361,  'spontaneous picking'                      bfid_artnr_abruf    361   // artikelnummerabruf am paternoster
// 365,  'scan to : move carousels'                 bfid_scan_ta_zone   365   // scannen von ta-nummer und zone
//                                                                            // -> anfahren der paternoster
// 366                                              bfid_scan_tanr      366   // scannen von ta-nummer
//
// 401,  'inventory booking', 32);                  bfid_inventur_sng   401   // einzelne inventurbuchung (stichtag inventur)
// 402,  'inventory with to', 32);                  bfid_inventur_fahr  402   // inventur über fahrbefehl (permanente inventur)
// 403,  'inventory for host', 32);                 bfid_inventur_host  403   // host inventur (nur 1 eintrag in historie pro artikel)
//
// 501,  'printing client dep. labels'              bfid_versand_eti    501   // druck von versand-etiketten
}
