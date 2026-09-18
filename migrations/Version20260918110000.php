<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create automatic putaway strategies and putaway orders';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('ALTER TABLE wms_storage_location ADD putaway_enabled TINYINT(1) DEFAULT 1 NOT NULL, ADD putaway_priority INT DEFAULT 100 NOT NULL, ADD capacity_quantity INT DEFAULT 0 NOT NULL');
        $this->addSql('CREATE TABLE wms_putaway_strategy (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, warehouse_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, stock_status VARCHAR(30) NOT NULL, location_prefix VARCHAR(50) NOT NULL, priority INT NOT NULL, enabled TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_PUTAWAY_STRATEGY_CODE (tenant_id, code), INDEX IDX_WMS_PUTAWAY_STRATEGY_MATCH (warehouse_id, stock_status, enabled, priority), PRIMARY KEY(id), CONSTRAINT FK_WMS_PUTAWAY_STRATEGY_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PUTAWAY_STRATEGY_WAREHOUSE FOREIGN KEY (warehouse_id) REFERENCES wms_warehouse (id), CONSTRAINT FK_WMS_PUTAWAY_STRATEGY_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_putaway_order (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, inbound_receipt_id CHAR(36) NOT NULL, strategy_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, source_location_id CHAR(36) NOT NULL, target_location_id CHAR(36) NOT NULL, quantity INT NOT NULL, stock_status VARCHAR(30) NOT NULL, batch_number VARCHAR(100) DEFAULT NULL, serial_number VARCHAR(100) DEFAULT NULL, expires_at DATE DEFAULT NULL, status VARCHAR(30) NOT NULL, transfer_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, confirmed_by CHAR(36) DEFAULT NULL, confirmed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_PUTAWAY_RECEIPT (inbound_receipt_id), UNIQUE INDEX UNIQ_WMS_PUTAWAY_TRANSFER (transfer_id), INDEX IDX_WMS_PUTAWAY_TARGET_STATUS (target_location_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_PUTAWAY_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PUTAWAY_RECEIPT FOREIGN KEY (inbound_receipt_id) REFERENCES wms_inbound_receipt (id), CONSTRAINT FK_WMS_PUTAWAY_STRATEGY FOREIGN KEY (strategy_id) REFERENCES wms_putaway_strategy (id), CONSTRAINT FK_WMS_PUTAWAY_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_PUTAWAY_SOURCE FOREIGN KEY (source_location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_PUTAWAY_TARGET FOREIGN KEY (target_location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_PUTAWAY_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_PUTAWAY_CONFIRMED_BY FOREIGN KEY (confirmed_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_putaway_order');
        $this->addSql('DROP TABLE wms_putaway_strategy');
        $this->addSql('ALTER TABLE wms_storage_location DROP putaway_enabled, DROP putaway_priority, DROP capacity_quantity');
    }
}
