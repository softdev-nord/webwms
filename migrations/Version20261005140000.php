<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261005140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make login identifiers globally unique after removing tenant selection';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_WMS_USER_EMAIL ON wms_user_account (email)');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_WMS_USER_TENANT_EMAIL ON wms_user_account');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_WMS_IDP_TENANT ON wms_identity_provider (tenant_id)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_WMS_IDP_GLOBAL_CODE ON wms_identity_provider (code)');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_WMS_IDP_CODE ON wms_identity_provider');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_WMS_USER_TENANT_EMAIL ON wms_user_account (tenant_id, email)');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_WMS_USER_EMAIL ON wms_user_account');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_WMS_IDP_CODE ON wms_identity_provider (tenant_id, code)');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_WMS_IDP_GLOBAL_CODE ON wms_identity_provider');
        $this->addSql('DROP INDEX IF EXISTS IDX_WMS_IDP_TENANT ON wms_identity_provider');
    }
}
