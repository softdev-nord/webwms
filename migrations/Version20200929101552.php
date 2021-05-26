<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200929101552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock_rotation (id INT AUTO_INCREMENT NOT NULL, lpz_id INT DEFAULT NULL, art_id INT DEFAULT NULL, ben_id INT DEFAULT NULL, aft_pos_id INT  DEFAULT NULL, bst_pos_id INT DEFAULT NULL, ba_id INT DEFAULT NULL, lbw_menge INT NOT NULL, lbw_zu_datum DATETIME DEFAULT NULL, lbw_ab_datum DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE stock_rotation');
    }
}
