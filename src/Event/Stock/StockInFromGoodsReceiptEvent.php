<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package:    WebWMS\Event\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInFromGoodsReceiptEvent
 */
class StockInFromGoodsReceiptEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_in_from_goods_receipt';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockInFromGoodsReceipt(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
