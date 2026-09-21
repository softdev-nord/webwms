<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260921050000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add inbound quantity discrepancy and blocked stock resolution workflow';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_inbound_discrepancy (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, receipt_id CHAR(36) NOT NULL, discrepancy_type VARCHAR(30) NOT NULL, expected_quantity INT NOT NULL, actual_quantity INT NOT NULL, reason VARCHAR(255) NOT NULL, status VARCHAR(30) NOT NULL, resolution_note VARCHAR(255) DEFAULT NULL, transfer_id CHAR(36) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, resolved_by CHAR(36) DEFAULT NULL, resolved_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_INBOUND_DISCREPANCY_RECEIPT (receipt_id), UNIQUE INDEX UNIQ_WMS_INBOUND_DISCREPANCY_TRANSFER (transfer_id), INDEX IDX_WMS_INBOUND_DISCREPANCY_TENANT_STATUS (tenant_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_INBOUND_DISCREPANCY_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_INBOUND_DISCREPANCY_RECEIPT FOREIGN KEY (receipt_id) REFERENCES wms_inbound_receipt (id), CONSTRAINT FK_WMS_INBOUND_DISCREPANCY_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_INBOUND_DISCREPANCY_RESOLVED_BY FOREIGN KEY (resolved_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_inbound_discrepancy');
    }
}
