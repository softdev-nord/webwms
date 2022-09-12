<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;
use WebWMS\Form\Stock\StockInFinalType;
use WebWMS\Form\Stock\StockInType;
use WebWMS\Service\BookingMethod\BookingMethodConstants;
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
    public const KARTON = 'Durchlaufregal';
    public const PALETTE = 'Pal Regal';
    public const BLOCK = 'Block-Lager';

    public function __construct(
        private Requirements $requirements,
        private BookingMethodService $bookingMethodService,
        private BookingMethodConstants $bookingMethodConstants,
        private StockLocationService $stockLocationService,
        private TransportRequestService $transportRequestService
    ) {
    }

    /**
     * SI101 Einlagern direkt
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in', name: 'stock_in')]
    public function stockIn(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $freeStockLocations = [];
        $bookingMethod = $this->bookingMethodConstants::SI101;
        $this->bookingMethodService->getBookingMethod($bookingMethod);

        $form = $this->createForm(StockInType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $form->getData();
            $stockUnits = (int) ceil(
                (int)$requestData['quantity'] / (int)$requestData['le_quantity'],
            );

            $stockSystem = match ($requestData['standard_loading_equipment']) {
                'KARTON' => self::KARTON,
                'PALETTE' => self::PALETTE,
                'BLOCK' => self::BLOCK,
                default => 'KST',
            };

            $fullPal = intdiv((int)$requestData['quantity'], (int)$requestData['le_quantity']);
            $remainder = fmod((float) $requestData['quantity'], (float) $requestData['le_quantity']);

            $stockLocations = $this->stockLocationService->getFreeStockLocations($stockSystem, $stockUnits);
            $suId = $this->transportRequestService->getLastStockUnit();

            foreach ($stockLocations as $key => $stockLocation) {
                if ((string)$fullPal <= $stockUnits) {
                    $quantity = $key === array_key_last($stockLocations) ? number_format($remainder, 2, '.', '') : $requestData['le_quantity'];

                    $freeStockLocations[] = [
                        'su_id' => ++$suId,
                        'ln' => $stockLocation['ln'],
                        'fb' => $stockLocation['fb'],
                        'sp' => $stockLocation['sp'],
                        'tf' => $stockLocation['tf'],
                        'ln_komplett' => $stockLocation['ln'] . '-' . $stockLocation['fb'] . '-' . $stockLocation['sp'] . '-' . $stockLocation['tf'],
                        'koordinate' => $stockLocation['koordinate'],
                        'system' => $stockLocation['system'],
                        'quantity' => $quantity
                    ];
                }
            }

            $stockInFinal = $this->createForm(StockInFinalType::class, ['freeStockLocations' => $freeStockLocations]);

            // @TODO Eine Option finden, um im Formular mehrere Spalten zu nutzen!

            return $this->render(
                'modal/put_into_storage.html.twig',
                [
                    'appName' => $this->requirements->getAppName(),
                    'appVersion' => $this->requirements->getAppVersion(),
                    'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                    'appCopyright' => $this->requirements->getAppCopyright(),
                    'appLizenz' => $this->requirements->getAppLizenz(),
                    'page' => 'Einlagern direkt',
                    'stockInFinalForm' => $stockInFinal->createView(),
                    'freeStockLocations' => $freeStockLocations,
                    'charge' => $requestData['charge']
                ]
            );
        }

        return $this->render(
            'modal/stock_in_modal.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Einlagern direkt',
                'stockInForm' => $form->createView(),
                'selectedStockLocations' => $freeStockLocations
            ]
        );
    }

    /**
     * SI102 Zugang aus Wareneingang
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_from_goods_receipt', name: 'stock_in_from_goods_receipt')]
    public function stockInFromGoodsReceipt()
    {
        $bookingMethod = $this->bookingMethodConstants::SI102;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI103 Zugang aus Produktion
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_from_production', name: 'stock_in_from_production')]
    public function stockInFromProduction()
    {
        $bookingMethod = $this->bookingMethodConstants::SI103;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI104 Rückgabe von Kostenstelle
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_from_cost_centre', name: 'stock_in_from_cost_centre')]
    public function stockInFromCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SI104;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI105 Einlagern in Container
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_into_container', name: 'stock_in_into_container')]
    public function stockInIntoContainer()
    {
        $bookingMethod = $this->bookingMethodConstants::SI105;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI106 WE zur Bestellung
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_for_supplier_order', name: 'stock_in_for_supplier_order')]
    public function stockInForSupplierOrder()
    {
        $bookingMethod = $this->bookingMethodConstants::SI106;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI107 Einlagern mit Ladehilfsmittel
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_using_loading_equipment', name: 'stock_in_using_loading_equipment')]
    public function stockInUsingLoadingEquipment()
    {
        $bookingMethod = $this->bookingMethodConstants::SI107;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI111 Einlagern direkt in WE-Zone
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_into_receiving_area', name: 'stock_in_into_receiving_area')]
    public function stockInIntoReceivingArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SI111;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * ST112 Rückgabe von Kostenstelle
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_transfer_from_cost_centre', name: 'stock_transfer_from_cost_centre')]
    public function stockTransferFromCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::ST112;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI113 Einlagern direkt in Kostenstelle
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_into_cost_centre', name: 'stock_in_into_cost_centre')]
    public function stockInIntoCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SI113;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SI114 Einlagern direkt in WA-Zone
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_in_into_dispatch_area', name: 'stock_in_into_dispatch_area')]
    public function stockInIntoDispatchArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SI114;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO151 Auslagern direkt
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out', name: 'stock_out')]
    public function stockOut()
    {
        $bookingMethod = $this->bookingMethodConstants::SO151;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO152 Auslagern auf Kostenstelle
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_to_cost_centre', name: 'stock_out_to_cost_centre')]
    public function stockOutToCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO152;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO153 Ausleihen auf Kostenstelle
     *
     * @throws EntityNotFoundException
     */
    #[Route('/lending_to_cost_centre', name: 'lending_to_cost_centre')]
    public function lendingToCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO153;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO155 Auslagern aus Container
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_from_container', name: 'stock_out_from_container')]
    public function stockOutFromContainer()
    {
        $bookingMethod = $this->bookingMethodConstants::SO155;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO156 Auslagern aus Kostenstelle
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_from_cost_centre', name: 'stock_out_from_cost_centre')]
    public function stockOutFromCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO156;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO157 Auslagern direkt aus WA-Zone
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_from_dispatch_area', name: 'stock_out_from_dispatch_area')]
    public function stockOutFromDispatchArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SO157;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO158 Auftrag auslagern
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_by_order', name: 'stock_out_by_order')]
    public function stockOutByOrder()
    {
        $bookingMethod = $this->bookingMethodConstants::SO158;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO159 Auslagern direkt aus WE-Zone
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_from_receiving_area', name: 'stock_out_from_receiving_area')]
    public function stockOutFromReceivingArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SO159;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO181 Auftrag auslagern (Auftrag-Liste)
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_order_list', name: 'stock_out_order_list')]
    public function stockOutOrderList()
    {
        $bookingMethod = $this->bookingMethodConstants::SO181;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO182 Auftrag auslagern mit Kostenstelle (Auftrag-Liste)
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_using_cost_centre', name: 'stock_out_using_cost_centre')]
    public function stockOutUsingCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO182;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * ST182 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste)
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_transfer_to_cost_centre', name: 'stock_transfer_to_cost_centre')]
    public function stockTransferToCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::ST183;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO187 Auftrag auslagern in WA-Zone
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_to_dispatch_area', name: 'stock_out_to_dispatch_area')]
    public function stockOutToDispatchArea()
    {
        $bookingMethod = $this->bookingMethodConstants::SO187;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     * SO188 Sammelkommissionierung auf Kostenstelle
     *
     * @throws EntityNotFoundException
     */
    #[Route('/stock_out_order_consolidation_to_cost_centre', name: 'stock_out_order_consolidation_to_cost_centre')]
    public function stockOutOrderConsolidationToCostCentre()
    {
        $bookingMethod = $this->bookingMethodConstants::SO188;
        $this->bookingMethodService->getBookingMethod($bookingMethod);
    }

    /**
     */
    #[Route('/stock_in_final', name: 'stock_in_final')]
    public function stockInFinal(Request $request)
    {
        $newArray = [];
        //dd($request->request->all());

        $requestNew = $request;

        dd($requestNew);

        $result = []; // blank array to store result
        foreach ($requestNew['stock_in_final'] as $val) {
            //dd($val);
            foreach ($val as $item => $value) {
                dd($val['stock_su_id']);
                $result[$item] =
                dd($result);
                $result[$val] = $val["brand"];
            }
        }

        dd($this->transportRequestService->getLastTransportRequestNr());

        dd($result);

        foreach ($requestNew['stock_in_final'] as $key => $req) {
            //dd($key);
            foreach ($req as $item => $value) {
                //dd($item);
                $newArray[$item][$key] = $value;
            }
            //dd($key);
            //$newArray[] = $key;
            //echo $key ." ". $req . "<br>";
        }
        dd($newArray);
        die();

        $keys = array_keys($requestNew['stock_in_final']);
        for ($i = 0; $i < count($requestNew['stock_in_final']); $i++) {
            echo $requestNew['stock_in_final'] . "{<br>";
            foreach ($requestNew[$keys[$i]] as $key => $value) {
                echo $key . " : " . $value . "<br>";
            }
            echo "}<br>";
        }

        die();

        foreach (array_keys($requestNew['stock_in_final']) as $fieldKey) {
            //dd($fieldKey);
            foreach ($requestNew['stock_in_final'][$fieldKey] as $key=>$value) {
                $newArray[$key][$fieldKey] = $value;
            }
        }

        dd($newArray);
    }

    public function generateSuId($stockLocations): int
    {
        $count = count(array_keys($stockLocations));
        $suId = $this->transportRequestService->getLastStockUnit()[0]->getSuId();
        for ($i = 0; $i <= $count; $i++) {
            $suId += $i;
        }

        return $suId;
    }
}
