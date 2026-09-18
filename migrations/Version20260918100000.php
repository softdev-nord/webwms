<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create return orders, expected items and inspected receipts';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_return_order (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(80) NOT NULL, order_reference VARCHAR(80) NOT NULL, status VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, completed_by CHAR(36) DEFAULT NULL, completed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_RETURN_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_RETURN_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_RETURN_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_RETURN_COMPLETED_BY FOREIGN KEY (completed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_return_item (id CHAR(36) NOT NULL, return_order_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, expected_quantity INT NOT NULL, reason VARCHAR(255) NOT NULL, status VARCHAR(30) NOT NULL, INDEX IDX_WMS_RETURN_ITEM_ORDER (return_order_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_RETURN_ITEM_ORDER FOREIGN KEY (return_order_id) REFERENCES wms_return_order (id), CONSTRAINT FK_WMS_RETURN_ITEM_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id))' . $options);
        $this->addSql('CREATE TABLE wms_return_receipt (id CHAR(36) NOT NULL, return_item_id CHAR(36) NOT NULL, quantity INT NOT NULL, status VARCHAR(30) NOT NULL, quality_decision VARCHAR(30) DEFAULT NULL, stock_status VARCHAR(30) DEFAULT NULL, location_id CHAR(36) DEFAULT NULL, ledger_entry_id CHAR(36) DEFAULT NULL, inspection_note VARCHAR(255) DEFAULT NULL, received_by CHAR(36) NOT NULL, received_at DATETIME(6) NOT NULL, inspected_by CHAR(36) DEFAULT NULL, inspected_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_RETURN_RECEIPT_ITEM (return_item_id), UNIQUE INDEX UNIQ_WMS_RETURN_RECEIPT_LEDGER (ledger_entry_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_RETURN_RECEIPT_ITEM FOREIGN KEY (return_item_id) REFERENCES wms_return_item (id), CONSTRAINT FK_WMS_RETURN_RECEIPT_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_RETURN_RECEIPT_LEDGER FOREIGN KEY (ledger_entry_id) REFERENCES wms_stock_ledger (id), CONSTRAINT FK_WMS_RETURN_RECEIVED_BY FOREIGN KEY (received_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_RETURN_INSPECTED_BY FOREIGN KEY (inspected_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_return_receipt');
        $this->addSql('DROP TABLE wms_return_item');
        $this->addSql('DROP TABLE wms_return_order');
    }
}
