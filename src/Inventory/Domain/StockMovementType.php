<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

enum StockMovementType: string
{
    case Posting = 'posting';
    case TransferOut = 'transfer_out';
    case TransferIn = 'transfer_in';
    case AllocationConsumption = 'allocation_consumption';
    case ReturnReceipt = 'return_receipt';
    case InboundReceipt = 'inbound_receipt';
}
