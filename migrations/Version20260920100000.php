<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tenant-scoped scanner devices and idempotent scan event journal';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wms_device (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(40) NOT NULL, name VARCHAR(100) NOT NULL, device_type VARCHAR(30) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_DEVICE_CODE (tenant_id, code), INDEX IDX_WMS_DEVICE_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_DEVICE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_DEVICE_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_DEVICE_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wms_scan_event (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, device_id CHAR(36) NOT NULL, scan_type VARCHAR(30) NOT NULL, scan_value VARCHAR(255) NOT NULL, process_type VARCHAR(30) NOT NULL, context_reference VARCHAR(100) NOT NULL, request_id VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, message VARCHAR(500) DEFAULT NULL, scanned_by CHAR(36) NOT NULL, scanned_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_SCAN_REQUEST (tenant_id, request_id), INDEX IDX_WMS_SCAN_JOURNAL (tenant_id, scanned_at), INDEX IDX_WMS_SCAN_CONTEXT (tenant_id, process_type, context_reference), PRIMARY KEY(id), CONSTRAINT FK_WMS_SCAN_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_SCAN_DEVICE FOREIGN KEY (device_id) REFERENCES wms_device (id), CONSTRAINT FK_WMS_SCAN_USER FOREIGN KEY (scanned_by) REFERENCES wms_user_account (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_scan_event');
        $this->addSql('DROP TABLE wms_device');
    }
}
