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
        $this->addSql('DROP INDEX UNIQ_WMS_USER_TENANT_EMAIL ON wms_user_account');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_USER_EMAIL ON wms_user_account (email)');
        $this->addSql('DROP INDEX UNIQ_WMS_IDP_CODE ON wms_identity_provider');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_IDP_GLOBAL_CODE ON wms_identity_provider (code)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_WMS_USER_EMAIL ON wms_user_account');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_USER_TENANT_EMAIL ON wms_user_account (tenant_id, email)');
        $this->addSql('DROP INDEX UNIQ_WMS_IDP_GLOBAL_CODE ON wms_identity_provider');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WMS_IDP_CODE ON wms_identity_provider (tenant_id, code)');
    }
}
