<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917095500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the WebWMS 3.0 site table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE wms_site ('
            . 'id CHAR(36) NOT NULL, '
            . 'tenant_id CHAR(36) NOT NULL, '
            . 'code VARCHAR(20) NOT NULL, '
            . 'name VARCHAR(255) NOT NULL, '
            . 'timezone VARCHAR(64) NOT NULL, '
            . 'status VARCHAR(20) NOT NULL, '
            . 'created_at DATETIME(6) NOT NULL, '
            . 'updated_at DATETIME(6) NOT NULL, '
            . 'INDEX IDX_WMS_SITE_TENANT (tenant_id), '
            . 'UNIQUE INDEX UNIQ_WMS_SITE_TENANT_CODE (tenant_id, code), '
            . 'PRIMARY KEY(id), '
            . 'CONSTRAINT FK_WMS_SITE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id)'
            . ') DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_site');
    }
}
