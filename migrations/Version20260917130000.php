<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create assignable pick lists and pick tasks';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('CREATE TABLE wms_pick_list (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, status VARCHAR(30) NOT NULL, assigned_to CHAR(36) DEFAULT NULL, assigned_by CHAR(36) DEFAULT NULL, assigned_at DATETIME(6) DEFAULT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, updated_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_PICK_LIST_CODE (tenant_id, code), INDEX IDX_WMS_PICK_LIST_STATUS (tenant_id, status), PRIMARY KEY(id), CONSTRAINT FK_WMS_PICK_LIST_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_PICK_LIST_ASSIGNED_TO FOREIGN KEY (assigned_to) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_PICK_LIST_ASSIGNED_BY FOREIGN KEY (assigned_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_PICK_LIST_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_pick_task (id CHAR(36) NOT NULL, pick_list_id CHAR(36) NOT NULL, allocation_id CHAR(36) NOT NULL, sequence_number INT NOT NULL, status VARCHAR(30) NOT NULL, confirmed_by CHAR(36) DEFAULT NULL, confirmed_at DATETIME(6) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_PICK_TASK_ALLOCATION (allocation_id), UNIQUE INDEX UNIQ_WMS_PICK_TASK_SEQUENCE (pick_list_id, sequence_number), PRIMARY KEY(id), CONSTRAINT FK_WMS_PICK_TASK_LIST FOREIGN KEY (pick_list_id) REFERENCES wms_pick_list (id), CONSTRAINT FK_WMS_PICK_TASK_ALLOCATION FOREIGN KEY (allocation_id) REFERENCES wms_stock_allocation (id), CONSTRAINT FK_WMS_PICK_TASK_CONFIRMED_BY FOREIGN KEY (confirmed_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_pick_task');
        $this->addSql('DROP TABLE wms_pick_list');
    }
}
