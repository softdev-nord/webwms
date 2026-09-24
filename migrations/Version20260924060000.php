<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260924060000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add configurable extension resources and executable workflows';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_extension_configuration (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, resource_type VARCHAR(60) NOT NULL, code VARCHAR(80) NOT NULL, name VARCHAR(160) NOT NULL, configuration_json LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_by CHAR(36) NOT NULL, updated_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_EXTENSION_CONFIG (tenant_id, resource_type, code), INDEX IDX_WMS_EXTENSION_CONFIG_LIST (tenant_id, resource_type, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_EXTENSION_CONFIG_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_EXTENSION_CONFIG_CREATED FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_EXTENSION_CONFIG_UPDATED FOREIGN KEY (updated_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_extension_work_item (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, workflow_type VARCHAR(60) NOT NULL, reference VARCHAR(120) NOT NULL, status VARCHAR(40) NOT NULL, payload_json LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) NOT NULL, changed_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_EXTENSION_WORK (tenant_id, workflow_type, reference), INDEX IDX_WMS_EXTENSION_WORK_LIST (tenant_id, workflow_type, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_EXTENSION_WORK_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_EXTENSION_WORK_CREATED FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_EXTENSION_WORK_CHANGED FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_login_event (id CHAR(36) NOT NULL, tenant_id CHAR(36) DEFAULT NULL, user_identifier VARCHAR(190) NOT NULL, successful TINYINT(1) NOT NULL, client_ip VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, failure_reason VARCHAR(255) DEFAULT NULL, occurred_at DATETIME(6) NOT NULL, INDEX IDX_WMS_LOGIN_EVENT_TENANT (tenant_id, occurred_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_LOGIN_EVENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id))' . $options);
        $this->addSql("INSERT IGNORE INTO wms_role_permission (role_id, permission_key) SELECT role_id, 'platform.extension.read' FROM wms_role_permission WHERE permission_key = 'platform.read'");
        $this->addSql("INSERT IGNORE INTO wms_role_permission (role_id, permission_key) SELECT role_id, 'platform.extension.write' FROM wms_role_permission WHERE permission_key = 'platform.write'");
        $this->addSql("INSERT IGNORE INTO wms_role_permission (role_id, permission_key) SELECT role_id, 'platform.extension.execute' FROM wms_role_permission WHERE permission_key = 'platform.execute'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_login_event');
        $this->addSql('DROP TABLE wms_extension_work_item');
        $this->addSql('DROP TABLE wms_extension_configuration');
    }
}
