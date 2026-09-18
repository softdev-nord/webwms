<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create replenishment policies and replenishment orders';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_replenishment_policy (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, warehouse_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, target_location_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, source_location_prefix VARCHAR(50) NOT NULL, minimum_quantity INT NOT NULL, target_quantity INT NOT NULL, priority INT NOT NULL, enabled TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_REPLENISHMENT_CODE (tenant_id, code), UNIQUE INDEX UNIQ_WMS_REPLENISHMENT_TARGET (product_id, target_location_id), INDEX IDX_WMS_REPLENISHMENT_POLICY (warehouse_id, enabled, priority), PRIMARY KEY(id), CONSTRAINT FK_WMS_REPLENISHMENT_POLICY_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_REPLENISHMENT_POLICY_WAREHOUSE FOREIGN KEY (warehouse_id) REFERENCES wms_warehouse (id), CONSTRAINT FK_WMS_REPLENISHMENT_POLICY_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_REPLENISHMENT_POLICY_TARGET FOREIGN KEY (target_location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_REPLENISHMENT_POLICY_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_replenishment_order (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, policy_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, source_location_id CHAR(36) NOT NULL, target_location_id CHAR(36) NOT NULL, quantity INT NOT NULL, stock_key CHAR(64) NOT NULL, stock_status VARCHAR(30) NOT NULL, batch_number VARCHAR(100) DEFAULT NULL, serial_number VARCHAR(100) DEFAULT NULL, expires_at DATE DEFAULT NULL, status VARCHAR(30) NOT NULL, transfer_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, confirmed_by CHAR(36) DEFAULT NULL, confirmed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_REPLENISHMENT_TRANSFER (transfer_id), INDEX IDX_WMS_REPLENISHMENT_POLICY_STATUS (policy_id, status), INDEX IDX_WMS_REPLENISHMENT_SOURCE_STOCK (source_location_id, stock_key, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_REPLENISHMENT_ORDER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_REPLENISHMENT_ORDER_POLICY FOREIGN KEY (policy_id) REFERENCES wms_replenishment_policy (id), CONSTRAINT FK_WMS_REPLENISHMENT_ORDER_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_REPLENISHMENT_ORDER_SOURCE FOREIGN KEY (source_location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_REPLENISHMENT_ORDER_TARGET FOREIGN KEY (target_location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_REPLENISHMENT_ORDER_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_REPLENISHMENT_ORDER_CONFIRMED_BY FOREIGN KEY (confirmed_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_replenishment_order');
        $this->addSql('DROP TABLE wms_replenishment_policy');
    }
}
