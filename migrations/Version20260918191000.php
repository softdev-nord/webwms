<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918191000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create WebWMS 3.0 outbound orders linked to stock reservations';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_outbound_order (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, order_number VARCHAR(100) NOT NULL, customer_reference VARCHAR(100) NOT NULL, status VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, released_by CHAR(36) DEFAULT NULL, released_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_OUTBOUND_ORDER_NUMBER (tenant_id, order_number), INDEX IDX_WMS_OUTBOUND_ORDER_STATUS (tenant_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_OUTBOUND_ORDER_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_OUTBOUND_ORDER_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_OUTBOUND_ORDER_RELEASED_BY FOREIGN KEY (released_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_outbound_order_item (id CHAR(36) NOT NULL, outbound_order_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, requested_quantity INT NOT NULL, reservation_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_OUTBOUND_ITEM_RESERVATION (reservation_id), INDEX IDX_WMS_OUTBOUND_ITEM_ORDER (outbound_order_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_OUTBOUND_ITEM_ORDER FOREIGN KEY (outbound_order_id) REFERENCES wms_outbound_order (id), CONSTRAINT FK_WMS_OUTBOUND_ITEM_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_OUTBOUND_ITEM_RESERVATION FOREIGN KEY (reservation_id) REFERENCES wms_stock_reservation (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_outbound_order_item');
        $this->addSql('DROP TABLE wms_outbound_order');
    }
}
