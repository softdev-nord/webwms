<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261006050000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Scope physical storage-location coordinates to their site-bound warehouse';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_WMS_LOCATION_COORDINATE ON wms_storage_location');
        $this->addSql('DROP INDEX UNIQ_WMS_LOCATION_GRID ON wms_storage_location');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_LOCATION_COORDINATE ON wms_storage_location (warehouse_id, coordinate)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_LOCATION_GRID ON wms_storage_location (warehouse_id, warehouse_number, level_number, slot_number, depth_number)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_WMS_LOCATION_COORDINATE ON wms_storage_location');
        $this->addSql('DROP INDEX UNIQ_WMS_LOCATION_GRID ON wms_storage_location');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_LOCATION_COORDINATE ON wms_storage_location (tenant_id, coordinate)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_LOCATION_GRID ON wms_storage_location (tenant_id, warehouse_number, level_number, slot_number, depth_number)');
    }
}
