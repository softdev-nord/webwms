<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Link pick lists to their outbound order';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_pick_list ADD outbound_order_id CHAR(36) DEFAULT NULL, ADD CONSTRAINT FK_WMS_PICK_LIST_ORDER FOREIGN KEY (outbound_order_id) REFERENCES wms_outbound_order (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_PICK_LIST_ORDER ON wms_pick_list (outbound_order_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_pick_list DROP FOREIGN KEY FK_WMS_PICK_LIST_ORDER');
        $this->addSql('DROP INDEX UNIQ_WMS_PICK_LIST_ORDER ON wms_pick_list');
        $this->addSql('ALTER TABLE wms_pick_list DROP outbound_order_id');
    }
}
