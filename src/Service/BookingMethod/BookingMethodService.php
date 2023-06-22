<?php

declare(strict_types=1);

namespace WebWMS\Service\BookingMethod;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use WebWMS\Form\Stock\StockInFinalType;
use WebWMS\Form\Stock\StockInType;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportRequestService;

/**
 * @package:    WebWMS\Service\BookingMethod
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BookingMethod
 */
class BookingMethodService
{
    public const KARTON = 'Durchlaufregal';
    public const PALETTE = 'Pal Regal';
    public const BLOCK = 'Block-Lager';

    public function __construct(
        private readonly RequirementsService $requirementsService,
        private readonly StockLocationService $stockLocationService,
        private readonly TransportRequestService $transportRequestService,
        private readonly FormFactoryInterface $formFactory,
        private readonly Environment $twig
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getBookingMethod(mixed $bookingMethod, Request $request): RedirectResponse|Response|null
    {
        return match ($bookingMethod) {
            'stock_in' => $this->stockIn($request), // SI101 Einlagern direkt
            'stock_in_from_goods_receipt' => $this->stockInFromGoodsReceipt(), // SI102 Zugang aus Wareneingang
            'stock_in_from_production' => $this->stockInFromProduction(), // SI103 Zugang aus Produktion
            'stock_in_from_cost_centre' => $this->stockInFromCostCentre(), // SI104 Rückgabe von Kostenstelle
            'stock_in_into_container' => $this->stockInIntoContainer(), // SI105 Einlagern in Container
            'stock_in_for_supplier_order' => $this->stockInForSupplierOrder(), // SI106 WE zur Bestellung
            'stock_in_using_loading_equipment' => $this->stockInUsingLoadingEquipment(), // SI107 Einlagern mit Ladehilfsmittel
            'stock_in_into_receiving_area' => $this->stockInIntoReceivingArea(), // SI111 Einlagern direkt in WE-Zone
            'stock_transfer_from_cost_centre' => $this->stockTransferFromCostCentre(), // ST112 Rückgabe von Kostenstelle
            'stock_in_into_cost_centre' => $this->stockInIntoCostCentre(), // SI113 Einlagern direkt in Kostenstelle
            'stock_in_into_dispatch_area' => $this->stockInIntoDispatchArea(), // SI114 Einlagern direkt in WA-Zone
            'stock_out' => $this->stockOut(), // SO151 Auslagern direkt
            'stock_out_to_cost_centre' => $this->stockOutToCostCentre(), // SO152 Auslagern auf Kostenstelle
            'lending_to_cost_centre' => $this->lendingToCostCentre(), // SO153 Ausleihen auf Kostenstelle
            'stock_out_from_container' => $this->stockOutFromContainer(), // SO155 Auslagern aus Container
            'stock_out_from_cost_centre' => $this->stockOutFromCostCentre(), // SO156 Auslagern aus Kostenstelle
            'stock_out_from_dispatch_area' => $this->stockOutFromDispatchArea(), // SO157 Auslagern direkt aus WA-Zone
            'stock_out_by_customer_order' => $this->stockOutByOrder(), // SO158 Auftrag auslagern
            'stock_out_from_receiving_area' => $this->stockOutFromReceivingArea(), // SO159 Auslagern direkt aus WE-Zone
            'stock_out_customer_order_list' => $this->stockOutOrderList(), // SO181 Auftrag auslagern (Auftrag-Liste)
            'stock_out_using_cost_centre' => $this->stockOutUsingCostCentre(), // SO182 Auftrag auslagern mit Kostenstelle (Auftrag-Liste)
            'stock_transfer_to_cost_centre' => $this->stockTransferToCostCentre(), // ST182 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste)
            'stock_out_to_dispatch_area' => $this->stockOutToDispatchArea(), // SO187 Auftrag auslagern in WA-Zone
            'stock_out_customer_order_consolidation_to_cost_centre' => $this->stockOutOrderConsolidationToCostCentre(), // SO188 Sammelkommissionierung auf Kostenstelle
            'stock_transfer_between' => $this->stockTransferBetween(), // ST201 Umlagern
            'stock_correction' => $this->stockCorrection(), // ST203 Bestandskorrektur
            'stock_transfer_from_receiving_area_to_stock' => $this->stockTransferFromReceivingAreaToStock(), // ST207 Umlagerung aus WE-Zone ins LV-Lager (aus Artikelbelegung)
            'stockTransferFromStockToDispatchArea' => $this->stockTransferFromStockToDispatchArea(), // ST208 Umlagerung aus LV-Lager in WA-Zone (aus Artikelbelegung)
            default => throw new EntityNotFoundException('Buchungsmethode ' . $bookingMethod . ' wurde nicht gefunden!'),
        };
    }

    /**
     * SI101 Einlagern direkt.
     */
    public function stockIn(Request $request): RedirectResponse|Response
    {
        $bookingMethod = 'SI101';
        $freeStockLocations = [];

        $form = $this->formFactory->create(StockInType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $form->getData();
            $stockUnits = (int) ceil(
                intval($requestData['quantity']) / intval($requestData['le_quantity']),
            );

            $stockSystem = match ($requestData['standard_loading_equipment']) {
                'KARTON' => self::KARTON,
                'PALETTE' => self::PALETTE,
                'BLOCK' => self::BLOCK,
                default => 'KST',
            };

            $fullPal = intdiv(intval($requestData['quantity']), intval($requestData['le_quantity']));
            $remainder = fmod(floatval($requestData['quantity']), floatval($requestData['le_quantity']));

            $stockLocations = $this->stockLocationService->getAllFreeStockLocationsWithLimit($stockSystem, $stockUnits);
            $suId = $this->transportRequestService->getLastStockUnit();

            foreach ($stockLocations as $key => $stockLocation) {
                if ((string) $fullPal <= $stockUnits) {
                    $quantity = $key === array_key_last($stockLocations) ? number_format(
                        $remainder,
                        2,
                        '.',
                        ''
                    ) : $requestData['le_quantity'];

                    $freeStockLocations[] = [
                        'id' => $stockLocation['id'],
                        'su_id' => ++$suId,
                        'ln' => $stockLocation['ln'],
                        'fb' => $stockLocation['fb'],
                        'sp' => $stockLocation['sp'],
                        'tf' => $stockLocation['tf'],
                        'ln_komplett' => $stockLocation['ln'] . '-' . $stockLocation['fb'] . '-' . $stockLocation['sp'] . '-' . $stockLocation['tf'],
                        'koordinate' => $stockLocation['koordinate'],
                        'system' => $stockLocation['system'],
                        'quantity' => $quantity,
                    ];
                }
            }

            $stockInFinal = $this->formFactory
                ->create(
                    StockInFinalType::class,
                    ['freeStockLocations' => $freeStockLocations]
                );

            // @TODO Eine Option finden, um im Formular mehrere Spalten zu nutzen!

            $html = $this->twig->render(
                'modal/put_into_storage.html.twig',
                [
                    'appName' => $this->requirementsService->getAppName(),
                    'appVersion' => $this->requirementsService->getAppVersion(),
                    'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                    'appCopyright' => $this->requirementsService->getAppCopyright(),
                    'appLizenz' => $this->requirementsService->getAppLizenz(),
                    'page' => 'Einlagern direkt',
                    'stockInFinalForm' => $stockInFinal->createView(),
                    'freeStockLocations' => $freeStockLocations,
                    'charge' => $requestData['charge'],
                    'article_nr' => $requestData['article_nr'],
                    'booking_method' => $bookingMethod,
                    'loading_equipment' => $stockSystem,
                ]
            );

            return new Response($html);
        }

        $html = $this->twig->render(
            'modal/stock_in_modal.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Einlagern direkt',
                'stockInForm' => $form->createView(),
                'selectedStockLocations' => $freeStockLocations,
            ]
        );

        return new Response($html);
    }

    /**
     * SI102 Zugang aus Wareneingang.
     */
    public function stockInFromGoodsReceipt(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SI103 Zugang aus Produktion.
     */
    public function stockInFromProduction(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SI104 Rückgabe von Kostenstelle.
     */
    public function stockInFromCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SI105 Einlagern in Container.
     */
    public function stockInIntoContainer(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SI106 WE zur Bestellung.
     */
    public function stockInForSupplierOrder(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SI107 Einlagern mit Ladehilfsmittel.
     */
    public function stockInUsingLoadingEquipment(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SI111 Einlagern direkt in WE-Zone.
     */
    public function stockInIntoReceivingArea(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * ST112 Rückgabe von Kostenstelle.
     */
    public function stockTransferFromCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SI113 Einlagern direkt in Kostenstelle.
     */
    public function stockInIntoCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SI114 Einlagern direkt in WA-Zone.
     */
    public function stockInIntoDispatchArea(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO151 Auslagern direkt.
     */
    public function stockOut(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO152 Auslagern auf Kostenstelle.
     */
    public function stockOutToCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO153 Ausleihen auf Kostenstelle.
     */
    public function lendingToCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO155 Auslagern aus Container.
     */
    public function stockOutFromContainer(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO156 Auslagern aus Kostenstelle.
     */
    public function stockOutFromCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO157 Auslagern direkt aus WA-Zone.
     */
    public function stockOutFromDispatchArea(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO158 Auftrag auslagern.
     */
    public function stockOutByOrder(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO159 Auslagern direkt aus WE-Zone.
     */
    public function stockOutFromReceivingArea(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO181 Auftrag auslagern (Auftrag-Liste).
     */
    public function stockOutOrderList(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO182 Auftrag auslagern mit Kostenstelle (Auftrag-Liste).
     */
    public function stockOutUsingCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * ST182 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste).
     */
    public function stockTransferToCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO187 Auftrag auslagern in WA-Zone.
     */
    public function stockOutToDispatchArea(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * SO188 Sammelkommissionierung auf Kostenstelle.
     */
    public function stockOutOrderConsolidationToCostCentre(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * ST201 Umlagern.
     */
    public function stockTransferBetween(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * ST203 Bestandskorrektur.
     */
    public function stockCorrection(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * ST207 Umlagerung aus WE-Zone ins LV-Lager (aus Artikelbelegung).
     */
    public function stockTransferFromReceivingAreaToStock(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

    /**
     * ST208 Umlagerung aus LV-Lager in WA-Zone (aus Artikelbelegung).
     */
    public function stockTransferFromStockToDispatchArea(): RedirectResponse|Response|null
    {
        // TODO: Implement logic
        return null;
    }

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
