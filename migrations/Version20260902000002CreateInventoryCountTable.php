<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260902000002CreateInventoryCountTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create inventory_count table for inventory counting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE inventory_count (
                id INT AUTO_INCREMENT NOT NULL,
                inventory_id INT NOT NULL,
                stock_location_id INT NOT NULL,
                article_id INT NOT NULL,
                article_nr VARCHAR(20) NOT NULL,
                expected_quantity DECIMAL(11, 3) NOT NULL,
                counted_quantity DECIMAL(11, 3),
                difference DECIMAL(11, 3),
                counted_by VARCHAR(30),
                counted_at DATETIME,
                created_at DATETIME NOT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_inventory_count_inventory FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE,
                INDEX idx_inventory_id (inventory_id),
                INDEX idx_article_id (article_id),
                INDEX idx_counted_quantity (counted_quantity)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS inventory_count');
    }
}

