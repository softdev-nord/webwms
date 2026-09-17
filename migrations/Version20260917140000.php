<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create shipments with label and carrier handover audit data';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_shipment (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, packing_order_id CHAR(36) NOT NULL, shipment_number VARCHAR(80) NOT NULL, carrier VARCHAR(80) NOT NULL, service VARCHAR(80) NOT NULL, status VARCHAR(30) NOT NULL, tracking_number VARCHAR(100) DEFAULT NULL, label_reference VARCHAR(255) DEFAULT NULL, handover_reference VARCHAR(100) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, label_registered_by CHAR(36) DEFAULT NULL, label_registered_at DATETIME(6) DEFAULT NULL, dispatched_by CHAR(36) DEFAULT NULL, dispatched_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_SHIPMENT_NUMBER (tenant_id, shipment_number), UNIQUE INDEX UNIQ_WMS_SHIPMENT_PACKING_ORDER (packing_order_id), UNIQUE INDEX UNIQ_WMS_SHIPMENT_TRACKING (tenant_id, carrier, tracking_number), PRIMARY KEY(id), CONSTRAINT FK_WMS_SHIPMENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_SHIPMENT_PACKING_ORDER FOREIGN KEY (packing_order_id) REFERENCES wms_packing_order (id), CONSTRAINT FK_WMS_SHIPMENT_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_SHIPMENT_LABELLED_BY FOREIGN KEY (label_registered_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_SHIPMENT_DISPATCHED_BY FOREIGN KEY (dispatched_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_shipment');
    }
}
