<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920220000 extends AbstractMigration
{
    public function getDescription(): string { return 'Create suppliers and unplanned goods receipts'; }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_supplier (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(40) NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_SUPPLIER_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_SUPPLIER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id))' . $options);
        $this->addSql('CREATE TABLE wms_unplanned_receipt (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, supplier_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, delivery_note VARCHAR(100) DEFAULT NULL, status VARCHAR(20) NOT NULL, accepted_by CHAR(36) NOT NULL, accepted_at DATETIME(6) NOT NULL, booked_by CHAR(36) DEFAULT NULL, booked_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_UNPLANNED_RECEIPT_CODE (tenant_id, code), INDEX IDX_WMS_UNPLANNED_RECEIPT_STATUS (tenant_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_UNPLANNED_RECEIPT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_UNPLANNED_RECEIPT_SUPPLIER FOREIGN KEY (supplier_id) REFERENCES wms_supplier (id), CONSTRAINT FK_WMS_UNPLANNED_RECEIPT_ACCEPTED_BY FOREIGN KEY (accepted_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_UNPLANNED_RECEIPT_BOOKED_BY FOREIGN KEY (booked_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_unplanned_receipt_item (id CHAR(36) NOT NULL, receipt_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, quantity INT NOT NULL, stock_status VARCHAR(20) NOT NULL, batch_number VARCHAR(100) DEFAULT NULL, serial_number VARCHAR(100) DEFAULT NULL, expires_at DATE DEFAULT NULL, INDEX IDX_WMS_UNPLANNED_RECEIPT_ITEM_RECEIPT (receipt_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_UNPLANNED_ITEM_RECEIPT FOREIGN KEY (receipt_id) REFERENCES wms_unplanned_receipt (id) ON DELETE CASCADE, CONSTRAINT FK_WMS_UNPLANNED_ITEM_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_UNPLANNED_ITEM_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_unplanned_receipt_item');
        $this->addSql('DROP TABLE wms_unplanned_receipt');
        $this->addSql('DROP TABLE wms_supplier');
    }
}
