<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260923210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add hazardous goods, bills of material and load-carrier accounts';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_hazard_class (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(40) NOT NULL, name VARCHAR(150) NOT NULL, un_class VARCHAR(20) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_HAZARD_CLASS (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_HAZARD_CLASS_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_HAZARD_CLASS_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_hazardous_material (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, hazard_class_id CHAR(36) NOT NULL, un_number VARCHAR(20) NOT NULL, packing_group VARCHAR(10) DEFAULT NULL, description VARCHAR(255) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_HAZARDOUS_PRODUCT (tenant_id, product_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_HAZARDOUS_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_HAZARDOUS_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_HAZARDOUS_CLASS FOREIGN KEY (hazard_class_id) REFERENCES wms_hazard_class (id), CONSTRAINT FK_WMS_HAZARDOUS_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_storage_restriction (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, hazard_class_id CHAR(36) NOT NULL, location_prefix VARCHAR(50) NOT NULL, allowed TINYINT(1) NOT NULL, max_quantity INT DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_STORAGE_RESTRICTION (tenant_id, hazard_class_id, location_prefix), PRIMARY KEY(id), CONSTRAINT FK_WMS_RESTRICTION_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_RESTRICTION_CLASS FOREIGN KEY (hazard_class_id) REFERENCES wms_hazard_class (id), CONSTRAINT FK_WMS_RESTRICTION_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_bill_of_material (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, version VARCHAR(20) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_BOM_CODE (tenant_id, code, version), PRIMARY KEY(id), CONSTRAINT FK_WMS_BOM_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_BOM_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_BOM_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_bom_item (id CHAR(36) NOT NULL, bill_of_material_id CHAR(36) NOT NULL, component_product_id CHAR(36) NOT NULL, quantity INT NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_WMS_BOM_POSITION (bill_of_material_id, position), PRIMARY KEY(id), CONSTRAINT FK_WMS_BOM_ITEM_HEADER FOREIGN KEY (bill_of_material_id) REFERENCES wms_bill_of_material (id), CONSTRAINT FK_WMS_BOM_ITEM_PRODUCT FOREIGN KEY (component_product_id) REFERENCES wms_product_reference (id))' . $options);
        $this->addSql("CREATE TABLE wms_material_requirement (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, bill_of_material_id CHAR(36) NOT NULL, reference VARCHAR(100) NOT NULL, production_quantity INT NOT NULL, status VARCHAR(30) NOT NULL DEFAULT 'planned', created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_MATERIAL_REQUIREMENT (tenant_id, reference), PRIMARY KEY(id), CONSTRAINT FK_WMS_REQUIREMENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_REQUIREMENT_BOM FOREIGN KEY (bill_of_material_id) REFERENCES wms_bill_of_material (id), CONSTRAINT FK_WMS_REQUIREMENT_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))" . $options);
        $this->addSql('CREATE TABLE wms_load_carrier_account (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, partner_code VARCHAR(50) NOT NULL, carrier_type VARCHAR(50) NOT NULL, balance INT NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_CARRIER_ACCOUNT (tenant_id, partner_code, carrier_type), PRIMARY KEY(id), CONSTRAINT FK_WMS_CARRIER_ACCOUNT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id))' . $options);
        $this->addSql('CREATE TABLE wms_load_carrier_movement (id CHAR(36) NOT NULL, account_id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, quantity INT NOT NULL, reference VARCHAR(100) NOT NULL, note VARCHAR(255) NOT NULL, booked_by CHAR(36) NOT NULL, booked_at DATETIME(6) NOT NULL, INDEX IDX_WMS_CARRIER_MOVEMENT (account_id, booked_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_CARRIER_MOVEMENT_ACCOUNT FOREIGN KEY (account_id) REFERENCES wms_load_carrier_account (id), CONSTRAINT FK_WMS_CARRIER_MOVEMENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_CARRIER_MOVEMENT_USER FOREIGN KEY (booked_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_load_carrier_movement');
        $this->addSql('DROP TABLE wms_load_carrier_account');
        $this->addSql('DROP TABLE wms_material_requirement');
        $this->addSql('DROP TABLE wms_bom_item');
        $this->addSql('DROP TABLE wms_bill_of_material');
        $this->addSql('DROP TABLE wms_storage_restriction');
        $this->addSql('DROP TABLE wms_hazardous_material');
        $this->addSql('DROP TABLE wms_hazard_class');
    }
}
