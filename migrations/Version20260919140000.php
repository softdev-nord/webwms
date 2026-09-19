<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260919140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tenant-scoped ERP connections with audited status changes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wms_erp_connection (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, endpoint_url VARCHAR(500) NOT NULL, credential_env VARCHAR(101) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_ERP_CONNECTION_NAME (tenant_id, name), INDEX IDX_WMS_ERP_CONNECTION_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_ERP_CONNECTION_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_ERP_CONNECTION_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_ERP_CONNECTION_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_erp_connection');
    }
}
