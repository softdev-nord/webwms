<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917143000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create loading manifests and shipment loading list';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_loading_manifest (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(80) NOT NULL, tour_reference VARCHAR(80) NOT NULL, vehicle_reference VARCHAR(80) NOT NULL, status VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, completed_by CHAR(36) DEFAULT NULL, completed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_LOADING_MANIFEST_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_LOADING_MANIFEST_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_LOADING_MANIFEST_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_LOADING_MANIFEST_COMPLETED_BY FOREIGN KEY (completed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_loading_manifest_shipment (manifest_id CHAR(36) NOT NULL, shipment_id CHAR(36) NOT NULL, status VARCHAR(30) NOT NULL, loaded_by CHAR(36) DEFAULT NULL, loaded_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_LOADING_SHIPMENT (shipment_id), PRIMARY KEY(manifest_id, shipment_id), CONSTRAINT FK_WMS_LOADING_LIST_MANIFEST FOREIGN KEY (manifest_id) REFERENCES wms_loading_manifest (id), CONSTRAINT FK_WMS_LOADING_LIST_SHIPMENT FOREIGN KEY (shipment_id) REFERENCES wms_shipment (id), CONSTRAINT FK_WMS_LOADING_LIST_LOADED_BY FOREIGN KEY (loaded_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_loading_manifest_shipment');
        $this->addSql('DROP TABLE wms_loading_manifest');
    }
}
