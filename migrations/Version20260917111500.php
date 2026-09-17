<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917111500 extends AbstractMigration
{
    private const DEFAULT_STOCK_KEY = '40f18bc19d0baea518342bc0857bdef95967917c04b4994cef2d9d97fed739b1';

    public function getDescription(): string
    {
        return 'Add stock status, batch, serial number and expiry dimensions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(sprintf("ALTER TABLE wms_stock_balance ADD stock_key VARCHAR(64) DEFAULT '%s' NOT NULL, ADD stock_status VARCHAR(30) DEFAULT 'available' NOT NULL, ADD batch_number VARCHAR(100) DEFAULT NULL, ADD serial_number VARCHAR(100) DEFAULT NULL, ADD expires_at DATE DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (tenant_id, product_id, location_id, stock_key), ADD INDEX IDX_WMS_BALANCE_BATCH (tenant_id, batch_number), ADD INDEX IDX_WMS_BALANCE_EXPIRY (tenant_id, expires_at)", self::DEFAULT_STOCK_KEY));
        $this->addSql(sprintf("ALTER TABLE wms_stock_ledger ADD stock_key VARCHAR(64) DEFAULT '%s' NOT NULL, ADD stock_status VARCHAR(30) DEFAULT 'available' NOT NULL, ADD batch_number VARCHAR(100) DEFAULT NULL, ADD serial_number VARCHAR(100) DEFAULT NULL, ADD expires_at DATE DEFAULT NULL", self::DEFAULT_STOCK_KEY));
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_stock_balance DROP PRIMARY KEY, ADD PRIMARY KEY (tenant_id, product_id, location_id), DROP INDEX IDX_WMS_BALANCE_BATCH, DROP INDEX IDX_WMS_BALANCE_EXPIRY, DROP stock_key, DROP stock_status, DROP batch_number, DROP serial_number, DROP expires_at');
        $this->addSql('ALTER TABLE wms_stock_ledger DROP stock_key, DROP stock_status, DROP batch_number, DROP serial_number, DROP expires_at');
    }
}
