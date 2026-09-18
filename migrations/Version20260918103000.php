<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create purchase orders, inbound advices, receipts and quality checklists';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_purchase_order (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(80) NOT NULL, supplier_reference VARCHAR(80) NOT NULL, status VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_PURCHASE_ORDER_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_PURCHASE_ORDER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PURCHASE_ORDER_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_purchase_order_item (id CHAR(36) NOT NULL, purchase_order_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, ordered_quantity INT NOT NULL, advised_quantity INT NOT NULL, received_quantity INT NOT NULL, status VARCHAR(30) NOT NULL, INDEX IDX_WMS_PURCHASE_ITEM_ORDER (purchase_order_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_PURCHASE_ITEM_ORDER FOREIGN KEY (purchase_order_id) REFERENCES wms_purchase_order (id), CONSTRAINT FK_WMS_PURCHASE_ITEM_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id))' . $options);
        $this->addSql('CREATE TABLE wms_inbound_delivery (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, purchase_order_id CHAR(36) NOT NULL, code VARCHAR(80) NOT NULL, delivery_note VARCHAR(80) NOT NULL, expected_at DATETIME(6) NOT NULL, status VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_INBOUND_CODE (tenant_id, code), UNIQUE INDEX UNIQ_WMS_INBOUND_NOTE (tenant_id, delivery_note), PRIMARY KEY(id), CONSTRAINT FK_WMS_INBOUND_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_INBOUND_ORDER FOREIGN KEY (purchase_order_id) REFERENCES wms_purchase_order (id), CONSTRAINT FK_WMS_INBOUND_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_inbound_delivery_line (id CHAR(36) NOT NULL, inbound_delivery_id CHAR(36) NOT NULL, purchase_order_item_id CHAR(36) NOT NULL, advised_quantity INT NOT NULL, status VARCHAR(30) NOT NULL, INDEX IDX_WMS_INBOUND_LINE_DELIVERY (inbound_delivery_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_INBOUND_LINE_DELIVERY FOREIGN KEY (inbound_delivery_id) REFERENCES wms_inbound_delivery (id), CONSTRAINT FK_WMS_INBOUND_LINE_ITEM FOREIGN KEY (purchase_order_item_id) REFERENCES wms_purchase_order_item (id))' . $options);
        $this->addSql('CREATE TABLE wms_inbound_receipt (id CHAR(36) NOT NULL, inbound_delivery_line_id CHAR(36) NOT NULL, quantity INT NOT NULL, status VARCHAR(30) NOT NULL, quality_decision VARCHAR(30) DEFAULT NULL, stock_status VARCHAR(30) DEFAULT NULL, location_id CHAR(36) DEFAULT NULL, ledger_entry_id CHAR(36) DEFAULT NULL, received_by CHAR(36) NOT NULL, received_at DATETIME(6) NOT NULL, inspected_by CHAR(36) DEFAULT NULL, inspected_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_INBOUND_RECEIPT_LINE (inbound_delivery_line_id), UNIQUE INDEX UNIQ_WMS_INBOUND_RECEIPT_LEDGER (ledger_entry_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_INBOUND_RECEIPT_LINE FOREIGN KEY (inbound_delivery_line_id) REFERENCES wms_inbound_delivery_line (id), CONSTRAINT FK_WMS_INBOUND_RECEIPT_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_INBOUND_RECEIPT_LEDGER FOREIGN KEY (ledger_entry_id) REFERENCES wms_stock_ledger (id), CONSTRAINT FK_WMS_INBOUND_RECEIVED_BY FOREIGN KEY (received_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_INBOUND_INSPECTED_BY FOREIGN KEY (inspected_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_inbound_quality_answer (receipt_id CHAR(36) NOT NULL, position INT NOT NULL, question VARCHAR(150) NOT NULL, passed TINYINT(1) NOT NULL, note VARCHAR(255) NOT NULL, PRIMARY KEY(receipt_id, position), CONSTRAINT FK_WMS_INBOUND_QUALITY_RECEIPT FOREIGN KEY (receipt_id) REFERENCES wms_inbound_receipt (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_inbound_quality_answer');
        $this->addSql('DROP TABLE wms_inbound_receipt');
        $this->addSql('DROP TABLE wms_inbound_delivery_line');
        $this->addSql('DROP TABLE wms_inbound_delivery');
        $this->addSql('DROP TABLE wms_purchase_order_item');
        $this->addSql('DROP TABLE wms_purchase_order');
    }
}
