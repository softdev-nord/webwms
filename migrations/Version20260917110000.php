<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the WebWMS 3.0 inventory core tables';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_product_reference (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, sku VARCHAR(64) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_PRODUCT_TENANT_SKU (tenant_id, sku), PRIMARY KEY(id), CONSTRAINT FK_WMS_PRODUCT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id))' . $options);
        $this->addSql('CREATE TABLE wms_warehouse (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, site_id CHAR(36) NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_WAREHOUSE_TENANT_CODE (tenant_id, code), INDEX IDX_WMS_WAREHOUSE_SITE (site_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_WAREHOUSE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_WAREHOUSE_SITE FOREIGN KEY (site_id) REFERENCES wms_site (id))' . $options);
        $this->addSql('CREATE TABLE wms_storage_location (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, warehouse_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_LOCATION_WAREHOUSE_CODE (warehouse_id, code), INDEX IDX_WMS_LOCATION_TENANT (tenant_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_LOCATION_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_LOCATION_WAREHOUSE FOREIGN KEY (warehouse_id) REFERENCES wms_warehouse (id))' . $options);
        $this->addSql('CREATE TABLE wms_stock_balance (tenant_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, quantity INT NOT NULL, updated_at DATETIME(6) NOT NULL, PRIMARY KEY(tenant_id, product_id, location_id), CONSTRAINT FK_WMS_BALANCE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_BALANCE_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_BALANCE_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id))' . $options);
        $this->addSql('CREATE TABLE wms_stock_ledger (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, quantity_delta INT NOT NULL, resulting_quantity INT NOT NULL, reason VARCHAR(255) NOT NULL, performed_by CHAR(36) NOT NULL, occurred_at DATETIME(6) NOT NULL, INDEX IDX_WMS_LEDGER_STOCK (tenant_id, product_id, location_id, occurred_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_LEDGER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_LEDGER_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_LEDGER_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_LEDGER_USER FOREIGN KEY (performed_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_stock_ledger');
        $this->addSql('DROP TABLE wms_stock_balance');
        $this->addSql('DROP TABLE wms_storage_location');
        $this->addSql('DROP TABLE wms_warehouse');
        $this->addSql('DROP TABLE wms_product_reference');
    }
}
