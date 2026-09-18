<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tenant-scoped API clients for WebWMS API v3';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_api_client (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, secret_hash CHAR(64) NOT NULL, permissions JSON NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME(6) NOT NULL, last_used_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_API_CLIENT_NAME (tenant_id, name), INDEX IDX_WMS_API_CLIENT_TENANT_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_API_CLIENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_api_client');
    }
}
