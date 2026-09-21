<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260921160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add auditable FIFO, LIFO and FEFO stock selection rules';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_stock_selection_rule (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, warehouse_id CHAR(36) DEFAULT NULL, product_id CHAR(36) DEFAULT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL, strategy VARCHAR(10) NOT NULL, priority INT NOT NULL, enabled TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_SELECTION_RULE_CODE (tenant_id, code), INDEX IDX_WMS_SELECTION_RULE_SCOPE (tenant_id, product_id, warehouse_id, enabled, priority), PRIMARY KEY(id), CONSTRAINT FK_WMS_SELECTION_RULE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_SELECTION_RULE_WAREHOUSE FOREIGN KEY (warehouse_id) REFERENCES wms_warehouse (id), CONSTRAINT FK_WMS_SELECTION_RULE_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_SELECTION_RULE_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_stock_selection_event (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, rule_id CHAR(36) NOT NULL, reservation_id CHAR(36) NOT NULL, product_id CHAR(36) NOT NULL, requested_quantity INT NOT NULL, allocated_quantity INT NOT NULL, candidate_count INT NOT NULL, performed_by CHAR(36) NOT NULL, occurred_at DATETIME(6) NOT NULL, INDEX IDX_WMS_SELECTION_EVENT_RESERVATION (tenant_id, reservation_id, occurred_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_SELECTION_EVENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_SELECTION_EVENT_RULE FOREIGN KEY (rule_id) REFERENCES wms_stock_selection_rule (id), CONSTRAINT FK_WMS_SELECTION_EVENT_RESERVATION FOREIGN KEY (reservation_id) REFERENCES wms_stock_reservation (id), CONSTRAINT FK_WMS_SELECTION_EVENT_PRODUCT FOREIGN KEY (product_id) REFERENCES wms_product_reference (id), CONSTRAINT FK_WMS_SELECTION_EVENT_USER FOREIGN KEY (performed_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_stock_selection_event');
        $this->addSql('DROP TABLE wms_stock_selection_rule');
    }
}
