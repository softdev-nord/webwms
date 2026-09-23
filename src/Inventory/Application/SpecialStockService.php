<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;
use WebWMS\Inventory\Domain\SpecialStockTypeDefinition;

final readonly class SpecialStockService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function createType(string $id, string $tenantId, string $code, string $name, string $kind, bool $allocatable, string $actorId, DateTimeImmutable $now): void
    {
        $definition = new SpecialStockTypeDefinition($code, $name, $kind, $allocatable);
        $this->assertActor($tenantId, $actorId);
        $this->connection->insert('wms_special_stock_type', [
            'id' => $id, 'tenant_id' => $tenantId, 'code' => $definition->code,
            'name' => $definition->name, 'classification_kind' => $definition->kind,
            'allocatable' => (int) $definition->allocatable, 'active' => 1,
            'created_by' => $actorId, 'created_at' => $this->date($now),
        ]);
    }

    /** @return array<string, mixed> */
    public function type(string $tenantId, string $typeId): array
    {
        $type = $this->connection->fetchAssociative('SELECT * FROM wms_special_stock_type WHERE id = :id AND tenant_id = :tenantId', ['id' => $typeId, 'tenantId' => $tenantId]);
        if ($type === false) {
            throw new InventoryReferenceNotFoundException('The special stock type does not exist in the tenant.');
        }

        return $type;
    }

    public function updateType(string $tenantId, string $typeId, string $code, string $name, string $kind, bool $allocatable, bool $active, string $actorId, DateTimeImmutable $now): void
    {
        $definition = new SpecialStockTypeDefinition($code, $name, $kind, $allocatable);
        $this->assertActor($tenantId, $actorId);
        $before = $this->type($tenantId, $typeId);
        $after = ['code' => $definition->code, 'name' => $definition->name, 'classification_kind' => $definition->kind, 'allocatable' => $definition->allocatable ? 1 : 0, 'active' => $active ? 1 : 0];
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $typeId, $actorId, $now, $before, $after): void {
            $connection->update('wms_special_stock_type', $after, ['id' => $typeId, 'tenant_id' => $tenantId]);
            $connection->insert('wms_administration_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => 'special_stock_type', 'aggregate_id' => $typeId, 'event_type' => 'updated', 'payload' => json_encode(['before' => $before, 'after' => $after], JSON_THROW_ON_ERROR), 'performed_by' => $actorId, 'occurred_at' => $this->date($now)]);
        });
    }

    public function classify(string $tenantId, string $productId, string $locationId, string $stockKey, string $typeId, ?string $ownerReference, string $reason, string $actorId, DateTimeImmutable $now): void
    {
        $ownerReference = $this->optional($ownerReference, 100);
        $reason = trim($reason);
        if ($reason === '' || mb_strlen($reason) > 255) {
            throw new \InvalidArgumentException('A classification reason with up to 255 characters is required.');
        }
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $productId, $locationId, $stockKey, $typeId, $ownerReference, $reason, $actorId, $now): void {
            $stockExists = $connection->fetchOne(
                'SELECT 1 FROM wms_stock_balance WHERE tenant_id = :tenantId AND product_id = :productId AND location_id = :locationId AND stock_key = :stockKey FOR UPDATE',
                ['tenantId' => $tenantId, 'productId' => $productId, 'locationId' => $locationId, 'stockKey' => $stockKey],
            ) !== false;
            $typeKind = $connection->fetchOne(
                'SELECT classification_kind FROM wms_special_stock_type WHERE id = :typeId AND tenant_id = :tenantId AND active = 1',
                ['typeId' => $typeId, 'tenantId' => $tenantId],
            );
            if (!$stockExists || $typeKind === false || !$this->actorExists($connection, $tenantId, $actorId)) {
                throw new InventoryReferenceNotFoundException('Stock, special stock type and user must exist in the tenant.');
            }
            if ($typeKind === 'owner' && $ownerReference === null) {
                throw new \InvalidArgumentException('An owner reference is required for owner stock.');
            }
            $criteria = ['tenant_id' => $tenantId, 'product_id' => $productId, 'location_id' => $locationId, 'stock_key' => $stockKey];
            $data = [
                'special_stock_type_id' => $typeId, 'owner_reference' => $ownerReference,
                'reason' => $reason, 'changed_by' => $actorId, 'changed_at' => $this->date($now),
            ];
            if ($connection->fetchOne(
                'SELECT 1 FROM wms_stock_classification WHERE tenant_id = :tenant_id AND product_id = :product_id AND location_id = :location_id AND stock_key = :stock_key',
                $criteria,
            ) === false) {
                $connection->insert('wms_stock_classification', [...$criteria, ...$data]);
            } else {
                $connection->update('wms_stock_classification', $data, $criteria);
            }
            $connection->insert('wms_stock_classification_event', [
                'id' => Uuid::v7()->toRfc4122(), ...$criteria,
                'special_stock_type_id' => $typeId, 'owner_reference' => $ownerReference,
                'reason' => $reason, 'performed_by' => $actorId, 'occurred_at' => $this->date($now),
            ]);
        });
    }

    private function assertActor(string $tenantId, string $actorId): void
    {
        if (!$this->actorExists($this->connection, $tenantId, $actorId)) {
            throw new InventoryReferenceNotFoundException('The user must exist in the tenant.');
        }
    }

    private function actorExists(Connection $connection, string $tenantId, string $actorId): bool
    {
        return $connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE id = :actorId AND tenant_id = :tenantId',
            ['actorId' => $actorId, 'tenantId' => $tenantId],
        ) !== false;
    }

    private function optional(?string $value, int $maximumLength): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $value = trim($value);
        if (mb_strlen($value) > $maximumLength) {
            throw new \InvalidArgumentException('The owner reference is too long.');
        }

        return $value;
    }

    private function date(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d H:i:s.u');
    }
}
