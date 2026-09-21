<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260921090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add auditable warehouse topology with areas, aisles, levels and bins';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('ALTER TABLE wms_site ADD created_by CHAR(36) DEFAULT NULL, ADD CONSTRAINT FK_WMS_SITE_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id)');
        $this->addSql("ALTER TABLE wms_warehouse ADD warehouse_type VARCHAR(30) DEFAULT 'standard' NOT NULL, ADD created_by CHAR(36) DEFAULT NULL, ADD changed_by CHAR(36) DEFAULT NULL, ADD changed_at DATETIME(6) DEFAULT NULL, ADD CONSTRAINT FK_WMS_WAREHOUSE_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), ADD CONSTRAINT FK_WMS_WAREHOUSE_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id)");
        $this->addSql('CREATE TABLE wms_warehouse_area (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, warehouse_id CHAR(36) NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, area_type VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_AREA_CODE (warehouse_id, code), INDEX IDX_WMS_AREA_TENANT (tenant_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_AREA_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_AREA_WAREHOUSE FOREIGN KEY (warehouse_id) REFERENCES wms_warehouse (id), CONSTRAINT FK_WMS_AREA_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_AREA_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_warehouse_aisle (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, area_id CHAR(36) NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(100) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_AISLE_CODE (area_id, code), INDEX IDX_WMS_AISLE_TENANT (tenant_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_AISLE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_AISLE_AREA FOREIGN KEY (area_id) REFERENCES wms_warehouse_area (id), CONSTRAINT FK_WMS_AISLE_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_AISLE_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql("ALTER TABLE wms_storage_location ADD area_id CHAR(36) DEFAULT NULL, ADD aisle_id CHAR(36) DEFAULT NULL, ADD level_code VARCHAR(20) DEFAULT '' NOT NULL, ADD bin_code VARCHAR(20) DEFAULT '' NOT NULL, ADD location_type VARCHAR(30) DEFAULT 'storage' NOT NULL, ADD created_by CHAR(36) DEFAULT NULL, ADD changed_by CHAR(36) DEFAULT NULL, ADD changed_at DATETIME(6) DEFAULT NULL, ADD INDEX IDX_WMS_LOCATION_AREA (area_id), ADD INDEX IDX_WMS_LOCATION_AISLE (aisle_id), ADD CONSTRAINT FK_WMS_LOCATION_AREA FOREIGN KEY (area_id) REFERENCES wms_warehouse_area (id), ADD CONSTRAINT FK_WMS_LOCATION_AISLE FOREIGN KEY (aisle_id) REFERENCES wms_warehouse_aisle (id), ADD CONSTRAINT FK_WMS_LOCATION_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), ADD CONSTRAINT FK_WMS_LOCATION_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_storage_location DROP FOREIGN KEY FK_WMS_LOCATION_AREA, DROP FOREIGN KEY FK_WMS_LOCATION_AISLE, DROP FOREIGN KEY FK_WMS_LOCATION_CREATED_BY, DROP FOREIGN KEY FK_WMS_LOCATION_CHANGED_BY, DROP INDEX IDX_WMS_LOCATION_AREA, DROP INDEX IDX_WMS_LOCATION_AISLE, DROP area_id, DROP aisle_id, DROP level_code, DROP bin_code, DROP location_type, DROP created_by, DROP changed_by, DROP changed_at');
        $this->addSql('DROP TABLE wms_warehouse_aisle');
        $this->addSql('DROP TABLE wms_warehouse_area');
        $this->addSql('ALTER TABLE wms_warehouse DROP FOREIGN KEY FK_WMS_WAREHOUSE_CREATED_BY, DROP FOREIGN KEY FK_WMS_WAREHOUSE_CHANGED_BY, DROP warehouse_type, DROP created_by, DROP changed_by, DROP changed_at');
        $this->addSql('ALTER TABLE wms_site DROP FOREIGN KEY FK_WMS_SITE_CREATED_BY, DROP created_by');
    }
}
