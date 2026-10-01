<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261001060000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tenant-scoped data exchange, mapping, SAP IDoc and commerce integration foundation';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_integration_mapping (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, system_type VARCHAR(30) NOT NULL, message_type VARCHAR(100) NOT NULL, source_field VARCHAR(190) NOT NULL, target_field VARCHAR(190) NOT NULL, transformation VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_MAPPING_FIELD (tenant_id, system_type, message_type, source_field), INDEX IDX_WMS_MAPPING_LOOKUP (tenant_id, system_type, message_type), PRIMARY KEY(id), CONSTRAINT FK_WMS_MAPPING_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_MAPPING_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_integration_job (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, direction VARCHAR(10) NOT NULL, format VARCHAR(10) NOT NULL, resource_type VARCHAR(80) NOT NULL, source_reference VARCHAR(190) DEFAULT NULL, status VARCHAR(20) NOT NULL, row_count INT NOT NULL, payload JSON NOT NULL, error_message VARCHAR(1000) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, completed_at DATETIME(6) DEFAULT NULL, INDEX IDX_WMS_EXCHANGE_JOB (tenant_id, created_at, id), INDEX IDX_WMS_EXCHANGE_STATUS (tenant_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_EXCHANGE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_EXCHANGE_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_commerce_connection (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, channel_type VARCHAR(30) NOT NULL, endpoint_url VARCHAR(500) NOT NULL, credential_env VARCHAR(101) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_COMMERCE_NAME (tenant_id, name), INDEX IDX_WMS_COMMERCE_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_COMMERCE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_COMMERCE_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_channel_order (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, connection_id CHAR(36) NOT NULL, external_order_id VARCHAR(190) NOT NULL, status VARCHAR(30) NOT NULL, payload JSON NOT NULL, imported_by CHAR(36) NOT NULL, imported_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_CHANNEL_ORDER (tenant_id, connection_id, external_order_id), INDEX IDX_WMS_CHANNEL_ORDER_STATUS (tenant_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_CHANNEL_ORDER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CHANNEL_ORDER_CONNECTION FOREIGN KEY (connection_id) REFERENCES wms_commerce_connection (id), CONSTRAINT FK_WMS_CHANNEL_ORDER_USER FOREIGN KEY (imported_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_channel_order');
        $this->addSql('DROP TABLE wms_commerce_connection');
        $this->addSql('DROP TABLE wms_integration_job');
        $this->addSql('DROP TABLE wms_integration_mapping');
    }
}
