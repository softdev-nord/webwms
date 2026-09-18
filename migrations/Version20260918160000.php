<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add permanent cycle-count plans and zero-crossing inventory audit data';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_cycle_count_plan (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, warehouse_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, location_prefix VARCHAR(50) NOT NULL, interval_days INT NOT NULL, next_due_at DATETIME(6) NOT NULL, last_started_at DATETIME(6) DEFAULT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_CYCLE_COUNT_CODE (tenant_id, code), INDEX IDX_WMS_CYCLE_COUNT_DUE (tenant_id, active, next_due_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_CYCLE_COUNT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CYCLE_COUNT_WAREHOUSE FOREIGN KEY (warehouse_id) REFERENCES wms_warehouse (id), CONSTRAINT FK_WMS_CYCLE_COUNT_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql("ALTER TABLE wms_inventory_count ADD count_type VARCHAR(30) NOT NULL DEFAULT 'stocktake', ADD cycle_count_plan_id CHAR(36) DEFAULT NULL, ADD trigger_ledger_entry_id CHAR(36) DEFAULT NULL");
        $this->addSql('ALTER TABLE wms_inventory_count ADD CONSTRAINT FK_WMS_INVENTORY_COUNT_CYCLE_PLAN FOREIGN KEY (cycle_count_plan_id) REFERENCES wms_cycle_count_plan (id), ADD CONSTRAINT FK_WMS_INVENTORY_COUNT_TRIGGER_LEDGER FOREIGN KEY (trigger_ledger_entry_id) REFERENCES wms_stock_ledger (id)');
        $this->addSql('CREATE INDEX IDX_WMS_INVENTORY_COUNT_TYPE ON wms_inventory_count (tenant_id, count_type, status)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_INVENTORY_COUNT_TRIGGER ON wms_inventory_count (trigger_ledger_entry_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_inventory_count DROP FOREIGN KEY FK_WMS_INVENTORY_COUNT_CYCLE_PLAN');
        $this->addSql('ALTER TABLE wms_inventory_count DROP FOREIGN KEY FK_WMS_INVENTORY_COUNT_TRIGGER_LEDGER');
        $this->addSql('DROP INDEX IDX_WMS_INVENTORY_COUNT_TYPE ON wms_inventory_count');
        $this->addSql('DROP INDEX UNIQ_WMS_INVENTORY_COUNT_TRIGGER ON wms_inventory_count');
        $this->addSql('ALTER TABLE wms_inventory_count DROP count_type, DROP cycle_count_plan_id, DROP trigger_ledger_entry_id');
        $this->addSql('DROP TABLE wms_cycle_count_plan');
    }
}
