<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create storage automation devices and idempotent command journal';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_automation_device (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(40) NOT NULL, name VARCHAR(100) NOT NULL, device_type VARCHAR(30) NOT NULL, endpoint_url VARCHAR(255) NOT NULL, credential_env VARCHAR(100) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_AUTOMATION_DEVICE_CODE (tenant_id, code), INDEX IDX_WMS_AUTOMATION_DEVICE_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_AUTOMATION_DEVICE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_AUTOMATION_DEVICE_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_AUTOMATION_DEVICE_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_device_command (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, device_id CHAR(36) NOT NULL, command_type VARCHAR(30) NOT NULL, location_id CHAR(36) NOT NULL, reference_type VARCHAR(30) NOT NULL, reference_id VARCHAR(100) NOT NULL, request_id VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, message VARCHAR(500) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_DEVICE_COMMAND_REQUEST (tenant_id, request_id), INDEX IDX_WMS_DEVICE_COMMAND_QUEUE (tenant_id, status, created_at), INDEX IDX_WMS_DEVICE_COMMAND_REFERENCE (tenant_id, reference_type, reference_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_DEVICE_COMMAND_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_DEVICE_COMMAND_DEVICE FOREIGN KEY (device_id) REFERENCES wms_automation_device (id), CONSTRAINT FK_WMS_DEVICE_COMMAND_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_DEVICE_COMMAND_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_DEVICE_COMMAND_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_device_command');
        $this->addSql('DROP TABLE wms_automation_device');
    }
}
