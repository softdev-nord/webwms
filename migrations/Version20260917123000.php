<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add allocation release and consumption lifecycle';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_stock_reservation ADD fulfilled_quantity INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE wms_stock_allocation ADD transitioned_by CHAR(36) DEFAULT NULL, ADD transitioned_at DATETIME(6) DEFAULT NULL, ADD transition_reason VARCHAR(255) DEFAULT NULL, ADD CONSTRAINT FK_WMS_ALLOCATION_TRANSITION_USER FOREIGN KEY (transitioned_by) REFERENCES wms_user_account (id)');
        $this->addSql('ALTER TABLE wms_stock_ledger ADD reservation_id CHAR(36) DEFAULT NULL, ADD allocation_id CHAR(36) DEFAULT NULL, ADD INDEX IDX_WMS_LEDGER_RESERVATION (reservation_id), ADD INDEX IDX_WMS_LEDGER_ALLOCATION (allocation_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_stock_ledger DROP INDEX IDX_WMS_LEDGER_RESERVATION, DROP INDEX IDX_WMS_LEDGER_ALLOCATION, DROP reservation_id, DROP allocation_id');
        $this->addSql('ALTER TABLE wms_stock_allocation DROP FOREIGN KEY FK_WMS_ALLOCATION_TRANSITION_USER, DROP transitioned_by, DROP transitioned_at, DROP transition_reason');
        $this->addSql('ALTER TABLE wms_stock_reservation DROP fulfilled_quantity');
    }
}
