<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create stock reservations and concrete stock allocations';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_stock_reservation (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, order_reference VARCHAR(100) NOT NULL, requested_quantity INT NOT NULL, allocated_quantity INT DEFAULT 0 NOT NULL, status VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_RESERVATION_ORDER_PRODUCT (tenant_id, order_reference, product_id), INDEX IDX_WMS_RESERVATION_PRODUCT_STATUS (tenant_id, product_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_RESERVATION_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_RESERVATION_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_RESERVATION_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_stock_allocation (id CHAR(36) NOT NULL, reservation_id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, location_id CHAR(36) NOT NULL, stock_key VARCHAR(64) NOT NULL, stock_status VARCHAR(30) NOT NULL, batch_number VARCHAR(100) DEFAULT NULL, serial_number VARCHAR(100) DEFAULT NULL, expires_at DATE DEFAULT NULL, quantity INT NOT NULL, status VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, INDEX IDX_WMS_ALLOCATION_STOCK (tenant_id, product_id, location_id, stock_key, status), INDEX IDX_WMS_ALLOCATION_RESERVATION (reservation_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_ALLOCATION_RESERVATION FOREIGN KEY (reservation_id) REFERENCES wms_stock_reservation (id), CONSTRAINT FK_WMS_ALLOCATION_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_ALLOCATION_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_ALLOCATION_LOCATION FOREIGN KEY (location_id) REFERENCES wms_storage_location (id), CONSTRAINT FK_WMS_ALLOCATION_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_stock_allocation');
        $this->addSql('DROP TABLE wms_stock_reservation');
    }
}
