<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261001110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Grant existing roles access to the integrated user handbook';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO wms_role_permission (role_id, permission_key) SELECT id, 'documentation.handbook.read' FROM wms_role WHERE NOT EXISTS (SELECT 1 FROM wms_role_permission rp WHERE rp.role_id = wms_role.id AND rp.permission_key = 'documentation.handbook.read')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM wms_role_permission WHERE permission_key = 'documentation.handbook.read'");
    }
}
