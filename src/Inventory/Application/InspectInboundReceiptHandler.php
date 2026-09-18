<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use InvalidArgumentException;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InboundInspection;
use WebWMS\Inventory\Domain\InboundQualityDecision;
use WebWMS\Inventory\Domain\InboundResult;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\QualityCheckAnswer;
use WebWMS\Inventory\Domain\StockDimensions;

final readonly class InspectInboundReceiptHandler
{
    public function __construct(private InventoryRepository $inventory) {}
    public function __invoke(InspectInboundReceiptCommand $command): InboundResult
    {
        $decision = InboundQualityDecision::tryFrom(mb_strtolower(trim($command->decision))) ?? throw new InvalidArgumentException('The inbound quality decision is not supported.');
        $answers = array_map(static fn (array $answer): QualityCheckAnswer => new QualityCheckAnswer($answer['question'], $answer['passed'], $answer['note']), $command->answers);

        return $this->inventory->inspectInboundReceipt(new InboundInspection(new InventoryId($command->receiptId), new InventoryId($command->ledgerEntryId), new TenantId($command->tenantId), new InventoryId($command->locationId), $decision, $answers, new StockDimensions(batchNumber: $command->batchNumber, serialNumber: $command->serialNumber, expiresAt: $command->expiresAt), new UserId($command->inspectedBy), $command->inspectedAt));
    }
}
