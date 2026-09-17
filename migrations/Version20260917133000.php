<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917133000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create packing orders, packages and package items';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_packing_order (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, pick_list_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, status VARCHAR(30) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, completed_by CHAR(36) DEFAULT NULL, completed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_PACKING_CODE (tenant_id, code), UNIQUE INDEX UNIQ_WMS_PACKING_PICK_LIST (pick_list_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_PACKING_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PACKING_PICK_LIST FOREIGN KEY (pick_list_id) REFERENCES wms_pick_list (id), CONSTRAINT FK_WMS_PACKING_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_PACKING_COMPLETED_BY FOREIGN KEY (completed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_package (id CHAR(36) NOT NULL, packing_order_id CHAR(36) NOT NULL, package_number VARCHAR(50) NOT NULL, weight_grams INT NOT NULL, status VARCHAR(30) NOT NULL, packed_by CHAR(36) NOT NULL, packed_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_PACKAGE_NUMBER (packing_order_id, package_number), PRIMARY KEY(id), CONSTRAINT FK_WMS_PACKAGE_ORDER FOREIGN KEY (packing_order_id) REFERENCES wms_packing_order (id), CONSTRAINT FK_WMS_PACKAGE_PACKED_BY FOREIGN KEY (packed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_package_item (package_id CHAR(36) NOT NULL, pick_task_id CHAR(36) NOT NULL, UNIQUE INDEX UNIQ_WMS_PACKAGE_ITEM_TASK (pick_task_id), PRIMARY KEY(package_id, pick_task_id), CONSTRAINT FK_WMS_PACKAGE_ITEM_PACKAGE FOREIGN KEY (package_id) REFERENCES wms_package (id), CONSTRAINT FK_WMS_PACKAGE_ITEM_TASK FOREIGN KEY (pick_task_id) REFERENCES wms_pick_task (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_package_item');
        $this->addSql('DROP TABLE wms_package');
        $this->addSql('DROP TABLE wms_packing_order');
    }
}
