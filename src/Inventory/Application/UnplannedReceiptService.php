<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InboundResult;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\StockDimensions;
use WebWMS\Inventory\Domain\UnplannedReceipt;
use WebWMS\Inventory\Domain\UnplannedReceiptBooking;
use WebWMS\Inventory\Domain\UnplannedReceiptItem;

final readonly class UnplannedReceiptService
{
    public function __construct(
        private InventoryRepository $inventory,
    ) {
    }

    /** @param list<array{productId: string, locationId: string, quantity: int, status?: string, batchNumber?: string|null, serialNumber?: string|null, expiresAt?: DateTimeImmutable|null}> $items */
    public function accept(string $tenantId, string $code, string $supplierCode, string $supplierName, ?string $deliveryNote, array $items, string $actorId, DateTimeImmutable $at): UnplannedReceipt
    {
        $receiptItems = array_map(static fn (array $item): UnplannedReceiptItem => new UnplannedReceiptItem(
            new InventoryId(Uuid::v7()->toRfc4122()),
            new InventoryId($item['productId']),
            new InventoryId($item['locationId']),
            $item['quantity'],
            StockDimensions::fromInput($item['status'] ?? 'available', $item['batchNumber'] ?? null, $item['serialNumber'] ?? null, $item['expiresAt'] ?? null),
        ), $items);
        $receipt = new UnplannedReceipt(new InventoryId(Uuid::v7()->toRfc4122()), new TenantId($tenantId), $code, $supplierCode, $supplierName, $deliveryNote, $receiptItems, new UserId($actorId), $at);
        $this->inventory->saveUnplannedReceipt($receipt);

        return $receipt;
    }

    public function book(string $tenantId, string $receiptId, string $actorId, DateTimeImmutable $at): InboundResult
    {
        return $this->inventory->bookUnplannedReceipt(new UnplannedReceiptBooking(new InventoryId($receiptId), new TenantId($tenantId), new UserId($actorId), $at));
    }
}
