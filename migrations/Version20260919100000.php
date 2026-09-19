<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260919100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the tenant-scoped integration status outbox';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wms_integration_outbox (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, event_name VARCHAR(100) NOT NULL, aggregate_type VARCHAR(50) NOT NULL, aggregate_id CHAR(36) NOT NULL, payload JSON NOT NULL, status VARCHAR(30) NOT NULL, occurred_at DATETIME(6) NOT NULL, created_by CHAR(36) NOT NULL, acknowledged_by CHAR(36) DEFAULT NULL, acknowledged_at DATETIME(6) DEFAULT NULL, INDEX IDX_WMS_OUTBOX_PENDING (tenant_id, status, id), INDEX IDX_WMS_OUTBOX_AGGREGATE (tenant_id, aggregate_type, aggregate_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_OUTBOX_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_OUTBOX_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_OUTBOX_ACKNOWLEDGED_BY FOREIGN KEY (acknowledged_by) REFERENCES wms_user_account (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_integration_outbox');
    }
}
