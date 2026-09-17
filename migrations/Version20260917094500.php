<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260917094500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the WebWMS 3.0 tenant table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE wms_tenant ('
            . 'id CHAR(36) NOT NULL, '
            . 'name VARCHAR(255) NOT NULL, '
            . 'status VARCHAR(20) NOT NULL, '
            . 'created_at DATETIME(6) NOT NULL, '
            . 'updated_at DATETIME(6) NOT NULL, '
            . 'PRIMARY KEY(id)'
            . ') DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_tenant');
    }
}
