<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Event\BaseEvent;
use WebWMS\Form\Stock\StockInFinalType;
use WebWMS\Form\Stock\StockInType;

/**
 * @package:    WebWMS\Event\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInEvent
 */
class StockInEvent extends BaseEvent
{
    final public const EVENT_NAME = 'stock.stock_in';

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
                (int) $requestData['quantity'] / (int) $requestData['leQuantity'],
            );

            $stockSystem = match ($requestData['standardLoadingEquipment']) {
                'KARTON' => StockEvents::KARTON,
                'PALETTE' => StockEvents::PALETTE,
                'BLOCK' => StockEvents::BLOCK,
                default => 'KST',
            };

            $fullPal = intdiv((int) $requestData['quantity'], (int) $requestData['leQuantity']);
            $remainder = fmod((float) $requestData['quantity'], (float) $requestData['leQuantity']);
            $suId = $this->getLastStockUnit();
            $quantity = ($requestData['quantity'] - $requestData['leQuantity'] !== 0) ? $requestData['leQuantity'] : $remainder;
            $stockLocations = $this->stockLocationService->getAllFreeStockLocationsWithLimit($stockSystem, $fullPal);

            if ((int) $remainder !== 0) {
                $stockLocationsNew = $this->stockLocationHasSpaceForAddingRemainder(
                    (int) $requestData['articleId'],
                    (int) $requestData['leQuantity'],
                    $remainder,
                    $suId,
                );

                $stockLocations[] = $stockLocationsNew;
            }

            foreach ($stockLocations as $key => $stockLocation) {
                if ((int) $remainder !== 0) {
                    $quantity = $key === array_key_last($stockLocations) ? number_format(
                        $remainder,
                        2,
                        '.',
                        ''
                    ) : $requestData['leQuantity'];
                }

                if ($fullPal <= $stockUnits) {
                    $freeStockLocations[] = [
                        'id' => (int) $stockLocation['id'],
                        'su_id' => ++$suId,
                        'ln' => (int) $stockLocation['ln'],
                        'fb' => (int) $stockLocation['fb'],
                        'sp' => (int) $stockLocation['sp'],
                        'tf' => (int) $stockLocation['tf'],
                        'ln_komplett' => (int) $stockLocation['ln'] . '-' . (int) $stockLocation['fb'] . '-' . (int) $stockLocation['sp'] . '-' . (int) $stockLocation['tf'],
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

            $html = $this->twigEnvironment->render(
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
                    'article_nr' => $requestData['articleNr'],
                    'booking_method' => $bookingMethod,
                    'loading_equipment' => $stockSystem,
                ]
            );

            return new Response($html);
        }

        $html = $this->twigEnvironment->render(
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
     * @throws Exception
     * @return array<string, int|float|string>
     */
    public function stockLocationHasSpaceForAddingRemainder(int $articleId, int $leQuantity, float $remainder, int $suId): array
    {
        $freeStockLocations = [];
        $occupiedStockLocations = $this->stockLocationService->getOccupiedStockLocationsByArticleId($articleId);

        foreach ($occupiedStockLocations as $occupiedStockLocation) {
            $sumLeQuantity = $occupiedStockLocation['in_stock'] + $occupiedStockLocation['incoming_stock'] + $occupiedStockLocation['reserved_stock'];
            if (($sumLeQuantity + $remainder) <= $leQuantity) {
                $withSpaceForAddingRemainder = $this->stockLocationService->getStockLocationById((int) $occupiedStockLocation['id']);
                $freeStockLocations[] = [
                    'id' => $withSpaceForAddingRemainder[0]['stock_location_id'],
                    'su_id' => ++$suId,
                    'ln' => $withSpaceForAddingRemainder[0]['stock_location_ln'],
                    'fb' => $withSpaceForAddingRemainder[0]['stock_location_fb'],
                    'sp' => $withSpaceForAddingRemainder[0]['stock_location_sp'],
                    'tf' => $withSpaceForAddingRemainder[0]['stock_location_tf'],
                    'ln_komplett' => $withSpaceForAddingRemainder[0]['stock_location_ln'] . '-' .
                        $withSpaceForAddingRemainder[0]['stock_location_fb'] . '-' .
                        $withSpaceForAddingRemainder[0]['stock_location_sp'] . '-' .
                        $withSpaceForAddingRemainder[0]['stock_location_tf'],
                    'koordinate' => $withSpaceForAddingRemainder[0]['stock_location_coordinate'],
                    'system' => $withSpaceForAddingRemainder[0]['stock_location_desc'],
                    'quantity' => $remainder,
                ];
            }
        }

        return $freeStockLocations[0];
    }

    private function getLastStockUnit(): int
    {
        $lastStockUnitFromTransportRequest = $this->transportRequestService->getLastStockUnit();
        $lastStockUnitFromTransportHistory = $this->transportHistoryService->getLastStockUnit();

        return ($lastStockUnitFromTransportRequest !== []) ?
            $lastStockUnitFromTransportRequest[0]->getSuId() :
            $lastStockUnitFromTransportHistory[0]->getSuId();
    }
}
