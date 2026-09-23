<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;

final readonly class InboundProcessService
{
    public function __construct(
        private Connection $connection,
        private PostStockHandler $postStock
    ) {
    }

    public function attach(string $tenantId, string $aggregateType, string $aggregateId, string $category, string $name, string $mediaType, string $content, string $actorId, DateTimeImmutable $now): string
    {
        if ($content === '' || strlen($content) > 10 * 1024 * 1024) {
            throw new InvalidArgumentException('An attachment must contain between 1 byte and 10 MB.');
        }
        if (!in_array($aggregateType, ['inbound_receipt', 'unplanned_receipt', 'return_receipt', 'production_receipt'], true)) {
            throw new InvalidArgumentException('The inbound attachment type is not supported.');
        }
        $id = Uuid::v7()->toRfc4122();
        $this->connection->transactional(function (Connection $connection) use ($id, $tenantId, $aggregateType, $aggregateId, $category, $name, $mediaType, $content, $actorId, $now): void {
            $this->assertActor($connection, $tenantId, $actorId);
            $connection->insert('wms_inbound_attachment', ['id' => $id, 'tenant_id' => $tenantId, 'aggregate_type' => $aggregateType, 'aggregate_id' => $aggregateId, 'category' => $category, 'original_name' => $name, 'media_type' => $mediaType, 'byte_size' => strlen($content), 'checksum' => hash('sha256', $content), 'content' => $content, 'created_by' => $actorId, 'created_at' => $this->date($now)]);
        });

        return $id;
    }

    /** @param list<string> $questions */
    public function createChecklist(string $tenantId, string $code, string $name, array $questions, string $actorId, DateTimeImmutable $now): string
    {
        $questions = array_values(array_filter(array_map('trim', $questions), static fn (string $question): bool => $question !== ''));

        if ($questions === []) {
            throw new InvalidArgumentException('A quality checklist requires at least one question.');
        }
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_quality_checklist', ['id' => $id, 'tenant_id' => $tenantId, 'code' => mb_strtoupper(trim($code)), 'name' => trim($name), 'questions' => json_encode($questions, JSON_THROW_ON_ERROR), 'active' => 1, 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    public function requestLabel(string $tenantId, string $aggregateType, string $aggregateId, string $labelType, int $copies, string $actorId, DateTimeImmutable $now): string
    {
        if ($copies < 1 || $copies > 100 || !in_array($labelType, ['receipt', 'product', 'handling_unit'], true)) {
            throw new InvalidArgumentException('Label type or number of copies is invalid.');
        }
        $id = Uuid::v7()->toRfc4122();
        $this->connection->insert('wms_inbound_label_job', ['id' => $id, 'tenant_id' => $tenantId, 'aggregate_type' => $aggregateType, 'aggregate_id' => $aggregateId, 'label_type' => $labelType, 'copies' => $copies, 'payload' => json_encode(['reference' => $aggregateId], JSON_THROW_ON_ERROR), 'status' => 'queued', 'created_by' => $actorId, 'created_at' => $this->date($now)]);

        return $id;
    }

    public function assignCrossDock(string $tenantId, string $receiptId, string $outboundItemId, int $quantity, string $actorId, DateTimeImmutable $now): string
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Cross-dock quantity must be positive.');
        }
        $id = Uuid::v7()->toRfc4122();

        $this->connection->transactional(function (Connection $connection) use ($id, $tenantId, $receiptId, $outboundItemId, $quantity, $actorId, $now): void {
            $match = $connection->fetchAssociative('SELECT r.quantity receipt_quantity, pi.product_id inbound_product, oi.product_id outbound_product, oi.requested_quantity demand_quantity FROM wms_inbound_receipt r INNER JOIN wms_inbound_delivery_line dl ON dl.id = r.inbound_delivery_line_id INNER JOIN wms_purchase_order_item pi ON pi.id = dl.purchase_order_item_id INNER JOIN wms_outbound_order_item oi ON oi.id = :outboundItemId INNER JOIN wms_outbound_order o ON o.id = oi.outbound_order_id WHERE r.id = :receiptId AND o.tenant_id = :tenantId AND r.status = :status FOR UPDATE', ['receiptId' => $receiptId, 'outboundItemId' => $outboundItemId, 'tenantId' => $tenantId, 'status' => 'inspected']);
            if ($match === false || $match['inbound_product'] !== $match['outbound_product'] || $quantity > min((int) $match['receipt_quantity'], (int) $match['demand_quantity'])) {
                throw new InventoryReferenceNotFoundException('An inspected matching receipt and open outbound demand must exist in the tenant.');
            }
            $connection->insert('wms_cross_dock_assignment', ['id' => $id, 'tenant_id' => $tenantId, 'inbound_receipt_id' => $receiptId, 'outbound_order_item_id' => $outboundItemId, 'quantity' => $quantity, 'status' => 'staged', 'created_by' => $actorId, 'created_at' => $this->date($now), 'staged_at' => $this->date($now)]);
        });

        return $id;
    }

    public function receiveProduction(string $tenantId, string $productionOrder, string $productId, string $locationId, int $quantity, ?string $batchNumber, string $actorId, DateTimeImmutable $now): string
    {
        if ($quantity < 1 || trim($productionOrder) === '') {
            throw new InvalidArgumentException('Production order and positive quantity are required.');
        }
        $receiptId = Uuid::v7()->toRfc4122();
        $ledgerId = Uuid::v7()->toRfc4122();
        $this->connection->transactional(function (Connection $connection) use ($receiptId, $ledgerId, $tenantId, $productionOrder, $productId, $locationId, $quantity, $batchNumber, $actorId, $now): void {
            ($this->postStock)(new PostStockCommand($ledgerId, $tenantId, $productId, $locationId, $quantity, 'Production receipt ' . trim($productionOrder), $actorId, $now, 'available', $batchNumber));
            $connection->insert('wms_production_receipt', ['id' => $receiptId, 'tenant_id' => $tenantId, 'production_order' => trim($productionOrder), 'product_id' => $productId, 'location_id' => $locationId, 'quantity' => $quantity, 'batch_number' => $batchNumber, 'ledger_entry_id' => $ledgerId, 'status' => 'received', 'received_by' => $actorId, 'received_at' => $this->date($now)]);
        });

        return $receiptId;
    }

    private function assertActor(Connection $connection, string $tenantId, string $actorId): void
    {
        if ($connection->fetchOne('SELECT 1 FROM wms_user_account WHERE id = :id AND tenant_id = :tenantId', ['id' => $actorId, 'tenantId' => $tenantId]) === false) {
            throw new InventoryReferenceNotFoundException('The acting user must exist in the tenant.');
        }
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
