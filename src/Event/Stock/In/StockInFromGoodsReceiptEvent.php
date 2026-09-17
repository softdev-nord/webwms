<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\In;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\In',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockInFromGoodsReceiptEvent'
)]
class StockInFromGoodsReceiptEvent extends StockInBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_in_from_goods_receipt';

    public const string EVENT = 'SI102';

    /**
     * SI102 Zugang aus Wareneingang
     */
    public function stockInFromGoodsReceipt(Request $request): RedirectResponse|Response
    {
        return $this->handleStockIn(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Zugang aus Wareneingang',
            stockInActionRoute: 'stock_in_from_goods_receipt'
        );
    }
}
