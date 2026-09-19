<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260919160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tenant-scoped carrier connections and idempotent carrier request audit';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wms_carrier_connection (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, carrier_code VARCHAR(40) NOT NULL, endpoint_url VARCHAR(500) NOT NULL, credential_env VARCHAR(101) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_CARRIER_CODE (tenant_id, carrier_code), INDEX IDX_WMS_CARRIER_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_CARRIER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CARRIER_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_CARRIER_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wms_carrier_request (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, connection_id CHAR(36) NOT NULL, operation VARCHAR(30) NOT NULL, idempotency_key VARCHAR(100) NOT NULL, aggregate_type VARCHAR(40) NOT NULL, aggregate_id CHAR(36) NOT NULL, status VARCHAR(30) NOT NULL, response_payload JSON NOT NULL, executed_by CHAR(36) NOT NULL, executed_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_CARRIER_REQUEST (tenant_id, idempotency_key), INDEX IDX_WMS_CARRIER_REQUEST_AGGREGATE (tenant_id, aggregate_type, aggregate_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_CARRIER_REQUEST_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CARRIER_REQUEST_CONNECTION FOREIGN KEY (connection_id) REFERENCES wms_carrier_connection (id), CONSTRAINT FK_WMS_CARRIER_REQUEST_USER FOREIGN KEY (executed_by) REFERENCES wms_user_account (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_carrier_request');
        $this->addSql('DROP TABLE wms_carrier_connection');
    }
}
