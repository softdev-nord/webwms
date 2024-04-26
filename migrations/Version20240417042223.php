<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417042223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add stock_coordinate to stock_occupancy';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_occupancy ADD stock_coordinate VARCHAR(25) NOT NULL AFTER `stock_location_id`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_occupancy DROP stock_coordinate');
    }
}
