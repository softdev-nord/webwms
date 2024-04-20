<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Dto\FreeStockLocationDto;
use WebWMS\Dto\OccupiedStockLocationDto;
use WebWMS\Dto\StockInDto;
use WebWMS\Dto\StockLocationDto;
use WebWMS\Entity\StockLocationEntity;
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
            $stockIn = StockInDto::hydrate($form->getData());

            $stockUnits = (int) ceil(
                (int) $stockIn->getQuantity() / (int) $stockIn->getLeQuantity(),
            );

            $stockSystem = match ($stockIn->getStandardLoadingEquipment()) {
                'KARTON' => StockEvents::KARTON,
                'PALETTE' => StockEvents::PALETTE,
                'BLOCK' => StockEvents::BLOCK,
                default => 'KST',
            };

            $fullPal = intdiv((int) $stockIn->getQuantity(), (int) $stockIn->getLeQuantity());
            $remainder = fmod((float) $stockIn->getQuantity(), (float) $stockIn->getLeQuantity());
            $suId = $this->getLastStockUnit();
            $quantity = ($stockIn->getQuantity() - $stockIn->getLeQuantity() !== 0) ? $stockIn->getLeQuantity() : $remainder;
            $stockLocations = $this->stockLocationService->getAllFreeStockLocationsWithLimit($stockSystem, $fullPal, (float) $stockIn->getLeQuantity());

            if ((int) $remainder !== 0) {
                $stockLocationsNew = $this->stockLocationHasSpaceForAddingRemainder(
                    (int) $stockIn->getArticleId(),
                    (int) $stockIn->getLeQuantity(),
                    $remainder,
                    $suId,
                );

                $stockLocations[] = $stockLocationsNew;
            }

            foreach ($stockLocations as $key => $stockLocation) {
                $stockLocation = StockLocationDto::hydrate($stockLocation);
                if ((int) $remainder !== 0) {
                    $quantity = $key === array_key_last($stockLocations) ? number_format(
                        $remainder,
                        2,
                        '.',
                        ''
                    ) : $stockIn->getLeQuantity();
                }

                if ($fullPal <= $stockUnits) {
                    $freeStockLocations[] = (new FreeStockLocationDto())
                        ->setId($stockLocation->getId())
                        ->setSuId(++$suId)
                        ->setLn((int) $stockLocation->getLn())
                        ->setFb((int) $stockLocation->getFb())
                        ->setSp((int) $stockLocation->getSp())
                        ->setTf((int) $stockLocation->getTf())
                        ->setLnKomplett($stockLocation->getLnKomplett())
                        ->setKoordinate($stockLocation->getKoordinate())
                        ->setSystem($stockLocation->getSystem())
                        ->setQuantity((string) $quantity)
                        ->setLeQuantity($stockIn->getLeQuantity());
                }
            }

            $stockInFinal = $this->formFactory
                ->create(
                    StockInFinalType::class,
                    ['freeStockLocations' => $freeStockLocations]
                );

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
                    'charge' => $stockIn->getCharge(),
                    'article_nr' => $stockIn->getArticleNr(),
                    'article_id' => $stockIn->getArticleId(),
                    'stock_location_id' => $stockIn->getStockLocationId(),
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
            $occupiedStockLocation = OccupiedStockLocationDto::hydrate($occupiedStockLocation);
            $sumLeQuantity = $occupiedStockLocation->getInStock() + $occupiedStockLocation->getIncomingStock() + $occupiedStockLocation->getReservedStock();
            if (($sumLeQuantity + $remainder) <= $leQuantity) {
                $withSpaceForAddingRemainder = $this->stockLocationService->getStockLocationById((int) $occupiedStockLocation->getId());

                if ($withSpaceForAddingRemainder instanceof StockLocationEntity) {
                    $freeStockLocations[] = [
                        'id' => $withSpaceForAddingRemainder->getStockLocationId(),
                        'su_id' => ++$suId,
                        'ln' => $withSpaceForAddingRemainder->getStockLocationLn(),
                        'fb' => $withSpaceForAddingRemainder->getStockLocationFb(),
                        'sp' => $withSpaceForAddingRemainder->getStockLocationSp(),
                        'tf' => $withSpaceForAddingRemainder->getStockLocationTf(),
                        'ln_komplett' => $withSpaceForAddingRemainder->getStockLocationLn() . '-' .
                            $withSpaceForAddingRemainder->getStockLocationFb() . '-' .
                            $withSpaceForAddingRemainder->getStockLocationSp() . '-' .
                            $withSpaceForAddingRemainder->getStockLocationTf(),
                        'koordinate' => $withSpaceForAddingRemainder->getStockLocationCoordinate(),
                        'system' => $withSpaceForAddingRemainder->getStockLocationDesc(),
                        'quantity' => $remainder,
                    ];
                }
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
