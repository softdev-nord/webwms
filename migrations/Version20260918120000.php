<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create stocktake inventory counts, count lines and approval audit';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_inventory_count (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, warehouse_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, location_prefix VARCHAR(50) NOT NULL, status VARCHAR(30) NOT NULL, line_count INT NOT NULL, difference_count INT NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, submitted_by CHAR(36) DEFAULT NULL, submitted_at DATETIME(6) DEFAULT NULL, approved_by CHAR(36) DEFAULT NULL, approved_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_INVENTORY_COUNT_CODE (tenant_id, code), INDEX IDX_WMS_INVENTORY_COUNT_STATUS (warehouse_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_INVENTORY_COUNT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_INVENTORY_COUNT_WAREHOUSE FOREIGN KEY (warehouse_id) REFERENCES wms_warehouse (id), CONSTRAINT FK_WMS_INVENTORY_COUNT_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_INVENTORY_COUNT_SUBMITTED_BY FOREIGN KEY (submitted_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_INVENTORY_COUNT_APPROVED_BY FOREIGN KEY (approved_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_inventory_count_line (id CHAR(36) NOT NULL, inventory_count_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, stock_key CHAR(64) NOT NULL, stock_status VARCHAR(30) NOT NULL, batch_number VARCHAR(100) DEFAULT NULL, serial_number VARCHAR(100) DEFAULT NULL, expires_at DATE DEFAULT NULL, expected_quantity INT NOT NULL, counted_quantity INT DEFAULT NULL, difference_quantity INT DEFAULT NULL, counted_by CHAR(36) DEFAULT NULL, counted_at DATETIME(6) DEFAULT NULL, ledger_entry_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_INVENTORY_COUNT_STOCK (inventory_count_id, product_id, location_id, stock_key), UNIQUE INDEX UNIQ_WMS_INVENTORY_COUNT_LEDGER (ledger_entry_id), INDEX IDX_WMS_INVENTORY_COUNT_OPEN_LINE (inventory_count_id, counted_quantity), PRIMARY KEY(id), CONSTRAINT FK_WMS_INVENTORY_LINE_COUNT FOREIGN KEY (inventory_count_id) REFERENCES wms_inventory_count (id), CONSTRAINT FK_WMS_INVENTORY_LINE_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_INVENTORY_LINE_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_INVENTORY_LINE_COUNTED_BY FOREIGN KEY (counted_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_inventory_count_line');
        $this->addSql('DROP TABLE wms_inventory_count');
    }
}
