<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260919173000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tenant-scoped printers and idempotent print job queue';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wms_printer (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, endpoint_url VARCHAR(500) NOT NULL, credential_env VARCHAR(101) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_PRINTER_NAME (tenant_id, name), INDEX IDX_WMS_PRINTER_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_PRINTER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PRINTER_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_PRINTER_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wms_print_job (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, printer_id CHAR(36) NOT NULL, document_type VARCHAR(40) NOT NULL, document_reference VARCHAR(500) NOT NULL, format VARCHAR(10) NOT NULL, copies SMALLINT UNSIGNED NOT NULL, idempotency_key VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, attempts SMALLINT UNSIGNED NOT NULL, external_reference VARCHAR(255) DEFAULT NULL, last_error VARCHAR(1000) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, completed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_PRINT_JOB_REQUEST (tenant_id, idempotency_key), INDEX IDX_WMS_PRINT_JOB_QUEUE (tenant_id, status, created_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_PRINT_JOB_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PRINT_JOB_PRINTER FOREIGN KEY (printer_id) REFERENCES wms_printer (id), CONSTRAINT FK_WMS_PRINT_JOB_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_print_job');
        $this->addSql('DROP TABLE wms_printer');
    }
}
