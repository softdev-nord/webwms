<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260921210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tenant administration workspace, SSO, number ranges, process and deployment configuration';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_business_partner (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(150) NOT NULL, partner_type VARCHAR(30) NOT NULL, external_reference VARCHAR(100) DEFAULT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_PARTNER_CODE (tenant_id, code), INDEX IDX_WMS_PARTNER_TYPE (tenant_id, partner_type, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_PARTNER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PARTNER_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_PARTNER_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_tenant_context (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, business_partner_id CHAR(36) DEFAULT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(150) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_CONTEXT_CODE (tenant_id, code), INDEX IDX_WMS_CONTEXT_PARTNER (business_partner_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_CONTEXT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CONTEXT_PARTNER FOREIGN KEY (business_partner_id) REFERENCES wms_business_partner (id), CONSTRAINT FK_WMS_CONTEXT_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_CONTEXT_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_identity_provider (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, protocol VARCHAR(20) NOT NULL, issuer_url VARCHAR(500) NOT NULL, client_id VARCHAR(255) NOT NULL, client_secret_env VARCHAR(100) NOT NULL, scopes VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_IDP_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_IDP_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_IDP_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_IDP_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_external_identity (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, identity_provider_id CHAR(36) NOT NULL, user_id CHAR(36) NOT NULL, subject VARCHAR(255) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_EXTERNAL_SUBJECT (identity_provider_id, subject), UNIQUE INDEX UNIQ_WMS_EXTERNAL_USER (identity_provider_id, user_id), INDEX IDX_WMS_EXTERNAL_TENANT (tenant_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_EXTERNAL_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_EXTERNAL_PROVIDER FOREIGN KEY (identity_provider_id) REFERENCES wms_identity_provider (id) ON DELETE CASCADE, CONSTRAINT FK_WMS_EXTERNAL_USER FOREIGN KEY (user_id) REFERENCES wms_user_account (id) ON DELETE CASCADE)' . $options);
        $this->addSql('CREATE TABLE wms_number_range (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, object_type VARCHAR(50) NOT NULL, prefix VARCHAR(30) NOT NULL, suffix VARCHAR(30) NOT NULL, padding SMALLINT NOT NULL, next_value BIGINT NOT NULL, maximum_value BIGINT DEFAULT NULL, gs1_company_prefix VARCHAR(20) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_NUMBER_RANGE_CODE (tenant_id, code), UNIQUE INDEX UNIQ_WMS_NUMBER_RANGE_OBJECT (tenant_id, object_type), PRIMARY KEY(id), CONSTRAINT FK_WMS_NUMBER_RANGE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_NUMBER_RANGE_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_NUMBER_RANGE_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_process_configuration (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, process_key VARCHAR(100) NOT NULL, name VARCHAR(150) NOT NULL, enabled TINYINT(1) NOT NULL, configuration JSON NOT NULL, changed_by CHAR(36) NOT NULL, changed_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_PROCESS_KEY (tenant_id, process_key), PRIMARY KEY(id), CONSTRAINT FK_WMS_PROCESS_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PROCESS_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_device_profile (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, device_type VARCHAR(30) NOT NULL, start_route VARCHAR(255) NOT NULL, fullscreen TINYINT(1) NOT NULL, scan_suffix VARCHAR(20) NOT NULL, enabled TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_DEVICE_PROFILE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_DEVICE_PROFILE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_DEVICE_PROFILE_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_DEVICE_PROFILE_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_deployment_configuration (tenant_id CHAR(36) NOT NULL, deployment_mode VARCHAR(20) NOT NULL, public_url VARCHAR(500) NOT NULL, storage_driver VARCHAR(30) NOT NULL, queue_transport VARCHAR(30) NOT NULL, release_channel VARCHAR(20) NOT NULL, changed_by CHAR(36) NOT NULL, changed_at DATETIME(6) NOT NULL, PRIMARY KEY(tenant_id), CONSTRAINT FK_WMS_DEPLOYMENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_DEPLOYMENT_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_administration_event (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, aggregate_type VARCHAR(50) NOT NULL, aggregate_id VARCHAR(100) NOT NULL, event_type VARCHAR(50) NOT NULL, payload JSON NOT NULL, performed_by CHAR(36) NOT NULL, occurred_at DATETIME(6) NOT NULL, INDEX IDX_WMS_ADMIN_EVENT (tenant_id, occurred_at), INDEX IDX_WMS_ADMIN_AGGREGATE (tenant_id, aggregate_type, aggregate_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_ADMIN_EVENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_ADMIN_EVENT_USER FOREIGN KEY (performed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql("INSERT IGNORE INTO wms_role_permission (role_id, permission_key) SELECT DISTINCT rp.role_id, p.permission_key FROM wms_role_permission rp CROSS JOIN (SELECT 'administration.configuration.read' permission_key UNION ALL SELECT 'administration.configuration.write' UNION ALL SELECT 'administration.number_range.use') p WHERE rp.permission_key = 'administration.role.write'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM wms_role_permission WHERE permission_key IN ('administration.configuration.read', 'administration.configuration.write', 'administration.number_range.use')");
        $this->addSql('DROP TABLE wms_administration_event');
        $this->addSql('DROP TABLE wms_deployment_configuration');
        $this->addSql('DROP TABLE wms_device_profile');
        $this->addSql('DROP TABLE wms_process_configuration');
        $this->addSql('DROP TABLE wms_number_range');
        $this->addSql('DROP TABLE wms_external_identity');
        $this->addSql('DROP TABLE wms_identity_provider');
        $this->addSql('DROP TABLE wms_tenant_context');
        $this->addSql('DROP TABLE wms_business_partner');
    }
}
