<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240405085134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_occupancy CHANGE article_id article_id INT DEFAULT NULL, CHANGE in_stock in_stock NUMERIC(10, 2) DEFAULT NULL, CHANGE incoming_stock incoming_stock NUMERIC(10, 2) DEFAULT NULL, CHANGE reserved_stock reserved_stock NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_occupancy CHANGE article_id article_id INT NOT NULL, CHANGE in_stock in_stock INT NOT NULL, CHANGE incoming_stock incoming_stock INT NOT NULL, CHANGE reserved_stock reserved_stock INT NOT NULL');
    }
}
