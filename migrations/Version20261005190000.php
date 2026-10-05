<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261005190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add V2-compatible warehouse coordinates, physical dimensions and storage zones';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_warehouse_aisle ADD storage_model VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE wms_storage_location ADD warehouse_number INT DEFAULT NULL, ADD level_number INT DEFAULT NULL, ADD slot_number INT DEFAULT NULL, ADD depth_number INT DEFAULT NULL, ADD coordinate CHAR(15) DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL, ADD width_mm DECIMAL(8,2) DEFAULT NULL, ADD physical_depth_mm DECIMAL(8,2) DEFAULT NULL, ADD height_mm DECIMAL(8,2) DEFAULT NULL, ADD zone_code VARCHAR(30) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_LOCATION_COORDINATE ON wms_storage_location (tenant_id, coordinate)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_LOCATION_GRID ON wms_storage_location (tenant_id, warehouse_number, level_number, slot_number, depth_number)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_WMS_LOCATION_COORDINATE ON wms_storage_location');
        $this->addSql('DROP INDEX UNIQ_WMS_LOCATION_GRID ON wms_storage_location');
        $this->addSql('ALTER TABLE wms_storage_location DROP warehouse_number, DROP level_number, DROP slot_number, DROP depth_number, DROP coordinate, DROP description, DROP width_mm, DROP physical_depth_mm, DROP height_mm, DROP zone_code');
        $this->addSql('ALTER TABLE wms_warehouse_aisle DROP storage_model');
    }
}
