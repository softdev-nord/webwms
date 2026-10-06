<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261006130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Extend product references to manageable article master data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE wms_product_reference ADD description LONGTEXT DEFAULT NULL, ADD description_en LONGTEXT DEFAULT NULL, ADD short_description VARCHAR(255) DEFAULT NULL, ADD gtin VARCHAR(14) DEFAULT NULL, ADD category VARCHAR(100) DEFAULT NULL, ADD base_unit VARCHAR(20) NOT NULL DEFAULT 'PCS', ADD abc_classification CHAR(1) DEFAULT NULL, ADD crash_class TINYINT DEFAULT NULL, ADD bulk_material TINYINT(1) NOT NULL DEFAULT 0, ADD hazardous_material TINYINT(1) NOT NULL DEFAULT 0, ADD serial_number_required TINYINT(1) DEFAULT NULL, ADD batch_required TINYINT(1) DEFAULT NULL, ADD expiry_required TINYINT(1) DEFAULT NULL, ADD shelf_life_days INT DEFAULT NULL, ADD expiry_warning_days INT DEFAULT NULL, ADD max_expiry_mix_days INT DEFAULT NULL, ADD net_weight_grams INT DEFAULT NULL, ADD volume_cm3 INT DEFAULT NULL, ADD nesting_factor NUMERIC(6, 5) DEFAULT NULL, ADD goods_value NUMERIC(15, 3) DEFAULT NULL, ADD goods_value_currency CHAR(3) DEFAULT NULL, ADD customs_tariff_number VARCHAR(30) DEFAULT NULL, ADD pharmaceutical_number VARCHAR(30) DEFAULT NULL, ADD customer_material_number VARCHAR(80) DEFAULT NULL, ADD supplier_material_number VARCHAR(80) DEFAULT NULL, ADD product_area_code VARCHAR(40) DEFAULT NULL, ADD inbound_note LONGTEXT DEFAULT NULL, ADD picking_note LONGTEXT DEFAULT NULL, ADD transport_note LONGTEXT DEFAULT NULL, ADD packing_note LONGTEXT DEFAULT NULL, ADD loading_note LONGTEXT DEFAULT NULL, ADD overdelivery_percent NUMERIC(6, 2) DEFAULT NULL, ADD active TINYINT(1) NOT NULL DEFAULT 1, ADD updated_at DATETIME(6) DEFAULT NULL");
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_PRODUCT_TENANT_GTIN ON wms_product_reference (tenant_id, gtin)');
        $this->addSql('CREATE INDEX IDX_WMS_PRODUCT_FILTER ON wms_product_reference (tenant_id, active, category)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_WMS_PRODUCT_TENANT_GTIN ON wms_product_reference');
        $this->addSql('DROP INDEX IDX_WMS_PRODUCT_FILTER ON wms_product_reference');
        $this->addSql('ALTER TABLE wms_product_reference DROP description, DROP description_en, DROP short_description, DROP gtin, DROP category, DROP base_unit, DROP abc_classification, DROP crash_class, DROP bulk_material, DROP hazardous_material, DROP serial_number_required, DROP batch_required, DROP expiry_required, DROP shelf_life_days, DROP expiry_warning_days, DROP max_expiry_mix_days, DROP net_weight_grams, DROP volume_cm3, DROP nesting_factor, DROP goods_value, DROP goods_value_currency, DROP customs_tariff_number, DROP pharmaceutical_number, DROP customer_material_number, DROP supplier_material_number, DROP product_area_code, DROP inbound_note, DROP picking_note, DROP transport_note, DROP packing_note, DROP loading_note, DROP overdelivery_percent, DROP active, DROP updated_at');
    }
}
