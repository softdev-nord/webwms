<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create WCS connections, machine commands and machine status journal';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_wcs_connection (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(40) NOT NULL, name VARCHAR(100) NOT NULL, system_type VARCHAR(30) NOT NULL, endpoint_url VARCHAR(255) NOT NULL, credential_env VARCHAR(100) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_WCS_CONNECTION_CODE (tenant_id, code), INDEX IDX_WMS_WCS_CONNECTION_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_WCS_CONNECTION_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_WCS_CONNECTION_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_WCS_CONNECTION_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_machine_command (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, connection_id CHAR(36) NOT NULL, command_type VARCHAR(30) NOT NULL, source VARCHAR(100) NOT NULL, destination VARCHAR(100) NOT NULL, load_unit VARCHAR(100) NOT NULL, request_id VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, message VARCHAR(500) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_MACHINE_COMMAND_REQUEST (tenant_id, request_id), INDEX IDX_WMS_MACHINE_COMMAND_QUEUE (tenant_id, status, created_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_MACHINE_COMMAND_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_MACHINE_COMMAND_CONNECTION FOREIGN KEY (connection_id) REFERENCES wms_wcs_connection (id), CONSTRAINT FK_WMS_MACHINE_COMMAND_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_MACHINE_COMMAND_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_machine_status (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, connection_id CHAR(36) NOT NULL, command_id CHAR(36) DEFAULT NULL, machine_code VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, message VARCHAR(500) DEFAULT NULL, external_event_id VARCHAR(100) NOT NULL, recorded_by CHAR(36) NOT NULL, recorded_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_MACHINE_STATUS_EVENT (tenant_id, external_event_id), INDEX IDX_WMS_MACHINE_STATUS_MACHINE (tenant_id, machine_code, recorded_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_MACHINE_STATUS_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_MACHINE_STATUS_CONNECTION FOREIGN KEY (connection_id) REFERENCES wms_wcs_connection (id), CONSTRAINT FK_WMS_MACHINE_STATUS_COMMAND FOREIGN KEY (command_id) REFERENCES wms_machine_command (id), CONSTRAINT FK_WMS_MACHINE_STATUS_RECORDED_BY FOREIGN KEY (recorded_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_machine_status');
        $this->addSql('DROP TABLE wms_machine_command');
        $this->addSql('DROP TABLE wms_wcs_connection');
    }
}
