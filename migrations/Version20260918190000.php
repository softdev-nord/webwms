<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260918190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Bind API clients to an acting WebWMS user for auditable writes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_api_client ADD acting_user_id CHAR(36) DEFAULT NULL AFTER tenant_id');
        $this->addSql('ALTER TABLE wms_api_client ADD CONSTRAINT FK_WMS_API_CLIENT_ACTOR FOREIGN KEY (acting_user_id) REFERENCES wms_user_account (id)');
        $this->addSql('CREATE INDEX IDX_WMS_API_CLIENT_ACTOR ON wms_api_client (acting_user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_api_client DROP FOREIGN KEY FK_WMS_API_CLIENT_ACTOR');
        $this->addSql('DROP INDEX IDX_WMS_API_CLIENT_ACTOR ON wms_api_client');
        $this->addSql('ALTER TABLE wms_api_client DROP acting_user_id');
    }
}
