<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917101000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create normalized WebWMS 3.0 users, roles and permissions';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';

        $this->addSql(
            'CREATE TABLE wms_role ('
            . 'id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, '
            . 'code VARCHAR(50) NOT NULL, name VARCHAR(150) NOT NULL, '
            . 'created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, '
            . 'INDEX IDX_WMS_ROLE_TENANT (tenant_id), '
            . 'UNIQUE INDEX UNIQ_WMS_ROLE_TENANT_CODE (tenant_id, code), PRIMARY KEY(id), '
            . 'CONSTRAINT FK_WMS_ROLE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id)'
            . ')' . $options
        );
        $this->addSql(
            'CREATE TABLE wms_role_permission ('
            . 'role_id CHAR(36) NOT NULL, permission_key VARCHAR(100) NOT NULL, '
            . 'INDEX IDX_WMS_ROLE_PERMISSION_ROLE (role_id), PRIMARY KEY(role_id, permission_key), '
            . 'CONSTRAINT FK_WMS_ROLE_PERMISSION_ROLE FOREIGN KEY (role_id) '
            . 'REFERENCES wms_role (id) ON DELETE CASCADE'
            . ')' . $options
        );
        $this->addSql(
            'CREATE TABLE wms_user_account ('
            . 'id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, '
            . 'email VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, '
            . 'password_hash VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, '
            . 'created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, '
            . 'INDEX IDX_WMS_USER_TENANT (tenant_id), '
            . 'UNIQUE INDEX UNIQ_WMS_USER_TENANT_EMAIL (tenant_id, email), PRIMARY KEY(id), '
            . 'CONSTRAINT FK_WMS_USER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id)'
            . ')' . $options
        );
        $this->addSql(
            'CREATE TABLE wms_user_role ('
            . 'user_id CHAR(36) NOT NULL, role_id CHAR(36) NOT NULL, '
            . 'INDEX IDX_WMS_USER_ROLE_USER (user_id), INDEX IDX_WMS_USER_ROLE_ROLE (role_id), '
            . 'PRIMARY KEY(user_id, role_id), '
            . 'CONSTRAINT FK_WMS_USER_ROLE_USER FOREIGN KEY (user_id) '
            . 'REFERENCES wms_user_account (id) ON DELETE CASCADE, '
            . 'CONSTRAINT FK_WMS_USER_ROLE_ROLE FOREIGN KEY (role_id) '
            . 'REFERENCES wms_role (id) ON DELETE CASCADE'
            . ')' . $options
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_user_role');
        $this->addSql('DROP TABLE wms_user_account');
        $this->addSql('DROP TABLE wms_role_permission');
        $this->addSql('DROP TABLE wms_role');
    }
}
