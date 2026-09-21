<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260921130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add auditable special stock types and stock classifications';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_special_stock_type (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, classification_kind VARCHAR(20) NOT NULL, allocatable TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_SPECIAL_STOCK_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_SPECIAL_STOCK_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_SPECIAL_STOCK_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_SPECIAL_STOCK_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_stock_classification (tenant_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, stock_key VARCHAR(64) NOT NULL, special_stock_type_id CHAR(36) NOT NULL, owner_reference VARCHAR(100) DEFAULT NULL, reason VARCHAR(255) NOT NULL, changed_by CHAR(36) NOT NULL, changed_at DATETIME(6) NOT NULL, PRIMARY KEY(tenant_id, product_id, location_id, stock_key), INDEX IDX_WMS_CLASSIFICATION_TYPE (special_stock_type_id), CONSTRAINT FK_WMS_CLASSIFICATION_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CLASSIFICATION_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_CLASSIFICATION_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_CLASSIFICATION_TYPE FOREIGN KEY (special_stock_type_id) REFERENCES wms_special_stock_type (id), CONSTRAINT FK_WMS_CLASSIFICATION_USER FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_stock_classification_event (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, stock_key VARCHAR(64) NOT NULL, special_stock_type_id CHAR(36) NOT NULL, owner_reference VARCHAR(100) DEFAULT NULL, reason VARCHAR(255) NOT NULL, performed_by CHAR(36) NOT NULL, occurred_at DATETIME(6) NOT NULL, INDEX IDX_WMS_CLASSIFICATION_EVENT_STOCK (tenant_id, product_id, location_id, stock_key, occurred_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_CLASS_EVENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CLASS_EVENT_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_CLASS_EVENT_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_CLASS_EVENT_TYPE FOREIGN KEY (special_stock_type_id) REFERENCES wms_special_stock_type (id), CONSTRAINT FK_WMS_CLASS_EVENT_USER FOREIGN KEY (performed_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_stock_classification_event');
        $this->addSql('DROP TABLE wms_stock_classification');
        $this->addSql('DROP TABLE wms_special_stock_type');
    }
}
