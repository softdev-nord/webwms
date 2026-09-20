<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create generic TCP and webservice transport endpoints';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_transport_endpoint (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(40) NOT NULL, name VARCHAR(100) NOT NULL, adapter_type VARCHAR(30) NOT NULL, address VARCHAR(255) NOT NULL, credential_env VARCHAR(100) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_TRANSPORT_ENDPOINT_CODE (tenant_id, code), INDEX IDX_WMS_TRANSPORT_ENDPOINT_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_TRANSPORT_ENDPOINT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_TRANSPORT_ENDPOINT_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_TRANSPORT_ENDPOINT_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_protocol_configuration (endpoint_id CHAR(36) NOT NULL, protocol VARCHAR(30) NOT NULL, framing VARCHAR(20) NOT NULL, connect_timeout_ms INT NOT NULL, read_timeout_ms INT NOT NULL, PRIMARY KEY(endpoint_id), CONSTRAINT FK_WMS_PROTOCOL_CONFIGURATION_ENDPOINT FOREIGN KEY (endpoint_id) REFERENCES wms_transport_endpoint (id) ON DELETE CASCADE)' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_protocol_configuration');
        $this->addSql('DROP TABLE wms_transport_endpoint');
    }
}
