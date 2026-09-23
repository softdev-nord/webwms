<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260923120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Completes the inbound process file with media, configurable quality checks, labels, cross-docking and production receipts.';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_inbound_attachment (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, aggregate_type VARCHAR(30) NOT NULL, aggregate_id CHAR(36) NOT NULL, category VARCHAR(30) NOT NULL, original_name VARCHAR(255) NOT NULL, media_type VARCHAR(120) NOT NULL, byte_size INT UNSIGNED NOT NULL, checksum CHAR(64) NOT NULL, content LONGBLOB NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, INDEX IDX_WMS_INBOUND_ATTACHMENT_AGGREGATE (tenant_id, aggregate_type, aggregate_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_INBOUND_ATTACHMENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_INBOUND_ATTACHMENT_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_quality_checklist (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(120) NOT NULL, questions JSON NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_QUALITY_CHECKLIST_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_QUALITY_CHECKLIST_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_QUALITY_CHECKLIST_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_inbound_label_job (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, aggregate_type VARCHAR(30) NOT NULL, aggregate_id CHAR(36) NOT NULL, label_type VARCHAR(30) NOT NULL, copies SMALLINT UNSIGNED NOT NULL, payload JSON NOT NULL, status VARCHAR(20) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, printed_at DATETIME(6) DEFAULT NULL, INDEX IDX_WMS_INBOUND_LABEL_QUEUE (tenant_id, status, created_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_INBOUND_LABEL_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_INBOUND_LABEL_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_cross_dock_assignment (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, inbound_receipt_id CHAR(36) NOT NULL, outbound_order_item_id CHAR(36) NOT NULL, quantity INT UNSIGNED NOT NULL, status VARCHAR(20) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, staged_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_CROSS_DOCK_RECEIPT (inbound_receipt_id), INDEX IDX_WMS_CROSS_DOCK_DEMAND (outbound_order_item_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_CROSS_DOCK_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CROSS_DOCK_RECEIPT FOREIGN KEY (inbound_receipt_id) REFERENCES wms_inbound_receipt (id), CONSTRAINT FK_WMS_CROSS_DOCK_OUTBOUND_ITEM FOREIGN KEY (outbound_order_item_id) REFERENCES wms_outbound_order_item (id), CONSTRAINT FK_WMS_CROSS_DOCK_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_production_receipt (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, production_order VARCHAR(80) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, quantity INT UNSIGNED NOT NULL, batch_number VARCHAR(100) DEFAULT NULL, ledger_entry_id CHAR(36) NOT NULL, status VARCHAR(20) NOT NULL, received_by CHAR(36) NOT NULL, received_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_PRODUCTION_RECEIPT_ORDER (tenant_id, production_order), UNIQUE INDEX UNIQ_WMS_PRODUCTION_RECEIPT_LEDGER (ledger_entry_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_PRODUCTION_RECEIPT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PRODUCTION_RECEIPT_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_PRODUCTION_RECEIPT_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_PRODUCTION_RECEIPT_LEDGER FOREIGN KEY (ledger_entry_id) REFERENCES wms_stock_ledger (id), CONSTRAINT FK_WMS_PRODUCTION_RECEIPT_USER FOREIGN KEY (received_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_production_receipt');
        $this->addSql('DROP TABLE wms_cross_dock_assignment');
        $this->addSql('DROP TABLE wms_inbound_label_job');
        $this->addSql('DROP TABLE wms_quality_checklist');
        $this->addSql('DROP TABLE wms_inbound_attachment');
    }
}
