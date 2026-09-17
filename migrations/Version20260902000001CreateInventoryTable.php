<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260902000001CreateInventoryTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create inventory table for inventory management';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE inventory (
                id INT AUTO_INCREMENT NOT NULL,
                inventory_nr VARCHAR(20) NOT NULL UNIQUE,
                status VARCHAR(20) NOT NULL DEFAULT "open",
                scope VARCHAR(50),
                start_date DATETIME NOT NULL,
                end_date DATETIME,
                started_by VARCHAR(30) NOT NULL,
                completed_by VARCHAR(30),
                created_at DATETIME NOT NULL,
                updated_at DATETIME,
                PRIMARY KEY (id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS inventory');
    }
}

