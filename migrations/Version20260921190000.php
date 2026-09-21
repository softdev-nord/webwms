<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260921190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add configurable stock block reasons and audited release workflow';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_stock_block_reason (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_BLOCK_REASON_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_BLOCK_REASON_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_BLOCK_REASON_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_stock_block (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, reason_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, source_stock_key VARCHAR(64) NOT NULL, blocked_stock_key VARCHAR(64) NOT NULL, original_status VARCHAR(30) NOT NULL, batch_number VARCHAR(100) DEFAULT NULL, serial_number VARCHAR(100) DEFAULT NULL, expires_at DATE DEFAULT NULL, quantity INT NOT NULL, note VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, blocked_by CHAR(36) NOT NULL, blocked_at DATETIME(6) NOT NULL, reviewed_by CHAR(36) DEFAULT NULL, reviewed_at DATETIME(6) DEFAULT NULL, review_note VARCHAR(255) DEFAULT NULL, released_by CHAR(36) DEFAULT NULL, released_at DATETIME(6) DEFAULT NULL, INDEX IDX_WMS_STOCK_BLOCK_STATUS (tenant_id, status, blocked_at), INDEX IDX_WMS_STOCK_BLOCK_POSITION (tenant_id, product_id, location_id, blocked_stock_key), PRIMARY KEY(id), CONSTRAINT FK_WMS_STOCK_BLOCK_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_STOCK_BLOCK_REASON FOREIGN KEY (reason_id) REFERENCES wms_stock_block_reason (id), CONSTRAINT FK_WMS_STOCK_BLOCK_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_STOCK_BLOCK_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_STOCK_BLOCK_BLOCKED_BY FOREIGN KEY (blocked_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_STOCK_BLOCK_REVIEWED_BY FOREIGN KEY (reviewed_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_STOCK_BLOCK_RELEASED_BY FOREIGN KEY (released_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_stock_block_event (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, block_id CHAR(36) NOT NULL, event_type VARCHAR(20) NOT NULL, note VARCHAR(255) NOT NULL, performed_by CHAR(36) NOT NULL, occurred_at DATETIME(6) NOT NULL, INDEX IDX_WMS_STOCK_BLOCK_EVENT (tenant_id, block_id, occurred_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_STOCK_BLOCK_EVENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_STOCK_BLOCK_EVENT_BLOCK FOREIGN KEY (block_id) REFERENCES wms_stock_block (id), CONSTRAINT FK_WMS_STOCK_BLOCK_EVENT_USER FOREIGN KEY (performed_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_stock_block_event');
        $this->addSql('DROP TABLE wms_stock_block');
        $this->addSql('DROP TABLE wms_stock_block_reason');
    }
}
