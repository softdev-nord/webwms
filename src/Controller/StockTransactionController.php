<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use WebWMS\Entity\StockLocation;
use WebWMS\Event\Stock\Correction\StockCorrectionEvent;
use WebWMS\Event\Stock\In\StockInEvent;
use WebWMS\Event\Stock\In\StockInForSupplierOrderEvent;
use WebWMS\Event\Stock\In\StockInFromCostCentreEvent;
use WebWMS\Event\Stock\In\StockInFromGoodsReceiptEvent;
use WebWMS\Event\Stock\In\StockInFromProductionEvent;
use WebWMS\Event\Stock\In\StockInToContainerEvent;
use WebWMS\Event\Stock\In\StockInToCostCentreEvent;
use WebWMS\Event\Stock\In\StockInToDispatchAreaEvent;
use WebWMS\Event\Stock\In\StockInToReceivingAreaEvent;
use WebWMS\Event\Stock\In\StockInUsingLoadingEquipmentEvent;
use WebWMS\Event\Stock\Lending\StockLendingToCostCentreEvent;
use WebWMS\Event\Stock\Lending\StockLendingUsingCostCentreEvent;
use WebWMS\Event\Stock\Out\StockOutByCustomerOrderEvent;
use WebWMS\Event\Stock\Out\StockOutByCustomerOrderListEvent;
use WebWMS\Event\Stock\Out\StockOutEvent;
use WebWMS\Event\Stock\Out\StockOutFromContainerEvent;
use WebWMS\Event\Stock\Out\StockOutFromCostCentreEvent;
use WebWMS\Event\Stock\Out\StockOutFromDispatchAreaEvent;
use WebWMS\Event\Stock\Out\StockOutFromReceivingAreaEvent;
use WebWMS\Event\Stock\Out\StockOutOrderConsolidationToCostCentreEvent;
use WebWMS\Event\Stock\Out\StockOutToCostCentreEvent;
use WebWMS\Event\Stock\Out\StockOutToDispatchAreaEvent;
use WebWMS\Event\Stock\Out\StockOutUsingCostCentreEvent;
use WebWMS\Event\Stock\Transfer\StockTransferBetweenStockLocationsEvent;
use WebWMS\Event\Stock\Transfer\StockTransferFromCostCentreEvent;
use WebWMS\Event\Stock\Transfer\StockTransferFromReceivingAreaEvent;
use WebWMS\Event\Stock\Transfer\StockTransferToDispatchAreaEvent;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportHistory\TransportHistoryService;
use WebWMS\Service\TransportRequest\TransportRequestService;

/**
 * @SuppressWarnings(CouplingBetweenObjects)
 */
#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockTransactionsController'
)]
class StockTransactionController extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService,
        private readonly StockLocationService $stockLocationService,
        private readonly TransportRequestService $transportRequestService,
        private readonly TransportHistoryService $transportHistoryService,
        private readonly FormFactoryInterface $formFactory,
        private readonly Environment $twigEnvironment,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggingService $loggingService
    ) {
    }

    /**
     * SI101 Einlagern direkt
     */
    #[Route('/stock_in', name: 'stock_in')]
    public function stockIn(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $event = new StockInEvent(
            $this->requirementsService,
            $this->stockLocationService,
            $this->transportRequestService,
            $this->transportHistoryService,
            $this->formFactory,
            $this->twigEnvironment
        );

        $this->eventDispatcher->dispatch(
            $event,
            StockInEvent::EVENT_NAME
        );

        return $event->stockIn($request);
    }

    /**
     * SI102 Zugang aus Wareneingang
     */
    #[Route('/stock_in_from_goods_receipt', name: 'stock_in_from_goods_receipt')]
    public function stockInFromGoodsReceipt(Request $request): RedirectResponse|Response
    {
        $event = new StockInFromGoodsReceiptEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInFromGoodsReceiptEvent::EVENT_NAME
        );

        return $event->stockInFromGoodsReceipt($request);
    }

    /**
     * SI103 Zugang aus Produktion
     */
    #[Route('/stock_in_from_production', name: 'stock_in_from_production')]
    public function stockInFromProduction(Request $request): RedirectResponse|Response
    {
        $event = new StockInFromProductionEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInFromProductionEvent::EVENT_NAME
        );

        return $event->stockInFromProduction($request);
    }

    /**
     * SI104 Rückgabe von Kostenstelle
     */
    #[Route('/stock_in_from_cost_centre', name: 'stock_in_from_cost_centre')]
    public function stockInFromCostCentre(Request $request): RedirectResponse|Response
    {
        $event = new StockInFromCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInFromCostCentreEvent::EVENT_NAME
        );

        return $event->stockInFromCostCentre($request);
    }

    /**
     * SI105 Einlagern in Container
     */
    #[Route('/stock_in_into_container', name: 'stock_in_into_container')]
    public function stockInIntoContainer(Request $request): RedirectResponse|Response
    {
        $event = new StockInToContainerEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInToContainerEvent::EVENT_NAME
        );

        return $event->stockInToContainer($request);
    }

    /**
     * SI106 WE zur Bestellung
     */
    #[Route('/stock_in_for_supplier_order', name: 'stock_in_for_supplier_order')]
    public function stockInForSupplierOrder(Request $request): RedirectResponse|Response
    {
        $event = new StockInForSupplierOrderEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInForSupplierOrderEvent::EVENT_NAME
        );

        return $event->stockInForSupplierOrder($request);
    }

    /**
     * SI107 Einlagern mit Ladehilfsmittel
     */
    #[Route('/stock_in_using_loading_equipment', name: 'stock_in_using_loading_equipment')]
    public function stockInUsingLoadingEquipment(Request $request): RedirectResponse|Response
    {
        $event = new StockInUsingLoadingEquipmentEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInUsingLoadingEquipmentEvent::EVENT_NAME
        );

        return $event->stockInUsingLoadingEquipment($request);
    }

    /**
     * SI108 Einlagern direkt in WE-Zone
     */
    #[Route('/stock_in_to_receiving_area', name: 'stock_in_to_receiving_area')]
    public function stockInToReceivingArea(Request $request): RedirectResponse|Response|null
    {
        $event = new StockInToReceivingAreaEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInToReceivingAreaEvent::EVENT_NAME
        );

        return $event->stockInToReceivingArea($request);
    }

    /**
     * SI109 Einlagern direkt in Kostenstelle
     */
    #[Route('/stock_in_to_cost_centre', name: 'stock_in_to_cost_centre')]
    public function stockInToCostCentre(Request $request): RedirectResponse|Response
    {
        $event = new StockInToCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInToCostCentreEvent::EVENT_NAME
        );

        return $event->stockInToCostCentre($request);
    }

    /**
     * SI110 Einlagern direkt in WA-Zone
     */
    #[Route('/stock_in_into_dispatch_area', name: 'stock_in_into_dispatch_area')]
    public function stockInToDispatchArea(Request $request): RedirectResponse|Response
    {
        $event = new StockInToDispatchAreaEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockInToDispatchAreaEvent::EVENT_NAME
        );

        return $event->stockInToDispatchArea($request);
    }

    /**
     * SO101 Auslagern direkt
     */
    #[Route('/stock_out', name: 'stock_out')]
    public function stockOut(Request $request): RedirectResponse|Response
    {
        $event = new StockOutEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutEvent::EVENT_NAME
        );

        return $event->stockOut($request);
    }

    /**
     * SO102 Auslagern auf Kostenstelle
     */
    #[Route('/stock_out_to_cost_centre', name: 'stock_out_to_cost_centre')]
    public function stockOutToCostCentre(Request $request): RedirectResponse|Response|null
    {
        $event = new StockOutToCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutEvent::EVENT_NAME
        );

        return $event->stockOutToCostCentre($request);
    }

    /**
     * SO103 Auslagern aus Container
     */
    #[Route('/stock_out_from_container', name: 'stock_out_from_container')]
    public function stockOutFromContainer(Request $request): RedirectResponse|Response
    {
        $event = new StockOutFromContainerEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutFromContainerEvent::EVENT_NAME
        );

        return $event->stockOutFromContainer($request);
    }

    /**
     * SO104 Auslagern aus Kostenstelle
     */
    #[Route('/stock_out_from_cost_centre', name: 'stock_out_from_cost_centre')]
    public function stockOutFromCostCentre(Request $request): RedirectResponse|Response
    {
        $event = new StockOutFromCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutFromCostCentreEvent::EVENT_NAME
        );

        return $event->stockOutFromCostCentre($request);
    }

    /**
     * SO105 Auslagern direkt aus WA-Zone
     */
    #[Route('/stock_out_from_dispatch_area', name: 'stock_out_from_dispatch_area')]
    public function stockOutFromDispatchArea(Request $request): RedirectResponse|Response
    {
        $event = new StockOutFromDispatchAreaEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutFromDispatchAreaEvent::EVENT_NAME
        );

        return $event->stockOutFromDispatchArea($request);
    }

    /**
     * SO106 Auftrag auslagern
     */
    #[Route('/stock_out_by_customer_order', name: 'stock_out_by_customer_order')]
    public function stockOutByCustomerOrder(Request $request): RedirectResponse|Response
    {
        $event = new StockOutByCustomerOrderEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutByCustomerOrderEvent::EVENT_NAME
        );

        return $event->stockOutByCustomerOrder($request);
    }

    /**
     * SO107 Auslagern direkt aus WE-Zone
     */
    #[Route('/stock_out_from_receiving_area', name: 'stock_out_from_receiving_area')]
    public function stockOutFromReceivingArea(Request $request): RedirectResponse|Response
    {
        $event = new StockOutFromReceivingAreaEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutFromReceivingAreaEvent::EVENT_NAME
        );

        return $event->stockOutFromReceivingArea($request);
    }

    /**
     * SO108 Auftrag auslagern (Auftrag-Liste)
     */
    #[Route('/stock_out_by_customer_order_list', name: 'stock_out_by_customer_order_list')]
    public function stockOutByCustomerOrderList(Request $request): RedirectResponse|Response
    {
        $event = new StockOutByCustomerOrderListEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutByCustomerOrderListEvent::EVENT_NAME
        );

        return $event->stockOutByCustomerOrderList($request);
    }

    /**
     * SO109 Auftrag auslagern mit Kostenstelle (Auftrag-Liste)
     */
    #[Route('/stock_out_using_cost_centre', name: 'stock_out_using_cost_centre')]
    public function stockOutUsingCostCentre(Request $request): RedirectResponse|Response
    {
        $event = new StockOutUsingCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutUsingCostCentreEvent::EVENT_NAME
        );

        return $event->stockOutUsingCostCentre($request);
    }

    /**
     * SO110 Auftrag auslagern in WA-Zone
     */
    #[Route('/stock_out_to_dispatch_area', name: 'stock_out_to_dispatch_area')]
    public function stockOutToDispatchArea(Request $request): RedirectResponse|Response
    {
        $event = new StockOutToDispatchAreaEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutToDispatchAreaEvent::EVENT_NAME
        );

        return $event->stockOutToDispatchArea($request);
    }

    /**
     * SO111 Sammelkommissionierung auf Kostenstelle
     */
    #[Route('/stock_out_order_consolidation_to_cost_centre', name: 'stock_out_order_consolidation_to_cost_centre')]
    public function stockOutOrderConsolidationToCostCentre(Request $request): RedirectResponse|Response
    {
        $event = new StockOutOrderConsolidationToCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockOutOrderConsolidationToCostCentreEvent::EVENT_NAME
        );

        return $event->stockOutOrderConsolidationToCostCentre($request);
    }

    /**
     * SL101 Ausleihen auf Kostenstelle
     */
    #[Route('/stock_lending_to_cost_centre', name: 'stock_lending_to_cost_centre')]
    public function stockLendingToCostCentre(Request $request): RedirectResponse|Response
    {
        $event = new StockLendingToCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockLendingToCostCentreEvent::EVENT_NAME
        );

        return $event->stockLendingToCostCentre($request);
    }

    /**
     * SL102 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste)
     */
    #[Route('/stock_lending_using_cost_centre', name: 'stock_lending_using_cost_centre')]
    public function stockLendingUsingCostCentre(Request $request): RedirectResponse|Response
    {
        $event = new StockLendingUsingCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockLendingUsingCostCentreEvent::EVENT_NAME
        );

        return $event->stockLendingUsingCostCentre($request);
    }

    /**
     * ST101 Umlagern
     */
    #[Route('/stock_transfer_between_stock_locations', name: 'stock_transfer_between_stock_locations')]
    public function stockTransferBetweenStockLocations(Request $request): RedirectResponse|Response
    {
        $event = new StockTransferBetweenStockLocationsEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockTransferBetweenStockLocationsEvent::EVENT_NAME
        );

        return $event->stockTransferBetweenStockLocations($request);
    }

    /**
     * ST102 Rückgabe von Kostenstelle
     */
    #[Route('/stock_transfer_from_cost_centre', name: 'stock_transfer_from_cost_centre')]
    public function stockTransferFromCostCentre(Request $request): RedirectResponse|Response
    {
        $event = new StockTransferFromCostCentreEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockTransferFromCostCentreEvent::EVENT_NAME
        );

        return $event->stockTransferFromCostCentre($request);
    }

    /**
     * ST103 Umlagerung aus WE-Zone ins LV-Lager (aus Artikelbelegung)
     */
    #[Route('/stock_transfer_from_receiving_area', name: 'stock_transfer_from_receiving_area')]
    public function stockTransferFromReceivingArea(Request $request): RedirectResponse|Response
    {
        $event = new StockTransferFromReceivingAreaEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockTransferFromReceivingAreaEvent::EVENT_NAME
        );

        return $event->stockTransferFromReceivingArea($request);
    }

    /**
     * ST104 Umlagerung aus LV-Lager in WA-Zone
     */
    #[Route('/stock_transfer_to_dispatch_area', name: 'stock_transfer_to_dispatch_area')]
    public function stockTransferToDispatchArea(Request $request): RedirectResponse|Response
    {
        $event = new StockTransferToDispatchAreaEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockTransferToDispatchAreaEvent::EVENT_NAME
        );

        return $event->stockTransferToDispatchArea($request);
    }

    /**
     * SC101 Bestandskorrektur
     */
    #[Route('/stock_correction', name: 'stock_correction')]
    public function stockCorrection(Request $request): RedirectResponse|Response
    {
        $event = new StockCorrectionEvent();

        $this->eventDispatcher->dispatch(
            $event,
            StockCorrectionEvent::EVENT_NAME
        );

        return $event->stockCorrection($request);
    }

    /**
     * @throws Exception
     *
     * @SuppressWarnings(ExitExpression)
     */
    #[Route('/stock_in_final', name: 'stock_in_final')]
    public function stockInFinal(Request $request): ?JsonResponse
    {
        $user = '';
        $clientIp = $request->getClientIp() ?? '';

        if ($this->getUser() instanceof UserInterface) {
            $user = $this->getUser()->getUserIdentifier();
        }

        $responseData = [];

        $response = $this->transportRequestService->createTransportRequest($request, $user, $clientIp);
        $responseData['message'] = 'Der Transportauftrag wurde erfolgreich erstellt.';

        if ($response?->getContent() === 'success') {
            $responseData['message'] = 'Der Transportauftrag wurde erfolgreich erstellt.';
            $logMessage = 'Der Transportauftrag wurde erfolgreich erstellt.';

            $this->loggingService->write($request, $logMessage, $user);

            return new JsonResponse($responseData);
        }

        return new JsonResponse($responseData);
    }

    /**
     * @param array<string> $stockLocations
     */
    public function generateSuId(array $stockLocations): int
    {
        $count = count(array_keys($stockLocations));
        $suId = $this->transportRequestService->getLastStockUnit()[0]->getSuId();
        for ($i = 0; $i <= $count; ++$i) {
            $suId += $i;
        }

        return $suId;
    }

    #[Route('/edit_pre_selected_stock_location/id/{stockLocationId}', name: 'edit_pre_selected_stock_location')]
    public function editPreSelectedStockLocation(Request $request): Response
    {
        $stockLocationId = $request->attributes->getString('stockLocationId');
        /* @var $stockSystem StockLocation */
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
