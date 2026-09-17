<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917113000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Correlate atomic inventory transfer entries in the stock ledger';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE wms_stock_ledger ADD movement_type VARCHAR(30) DEFAULT 'posting' NOT NULL, ADD transfer_id CHAR(36) DEFAULT NULL, ADD INDEX IDX_WMS_LEDGER_TRANSFER (transfer_id), ADD UNIQUE INDEX UNIQ_WMS_LEDGER_TRANSFER_ENTRY (transfer_id, movement_type)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_stock_ledger DROP INDEX IDX_WMS_LEDGER_TRANSFER, DROP INDEX UNIQ_WMS_LEDGER_TRANSFER_ENTRY, DROP movement_type, DROP transfer_id');
    }
}
