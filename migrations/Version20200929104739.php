<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200929104739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_rotation CHANGE lpz_id lpz_id INT DEFAULT NULL, CHANGE art_id art_id INT DEFAULT NULL, CHANGE ben_id ben_id INT DEFAULT NULL, CHANGE aft_pos_id aft_pos_id INT DEFAULT NULL, CHANGE bst_pos_id bst_pos_id INT DEFAULT NULL, CHANGE ba_id ba_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_rotation CHANGE lpz_id lpz_id INT NOT NULL, CHANGE art_id art_id INT NOT NULL, CHANGE ben_id ben_id INT NOT NULL, CHANGE aft_pos_id aft_pos_id INT NOT NULL, CHANGE bst_pos_id bst_pos_id INT NOT NULL, CHANGE ba_id ba_id INT NOT NULL');
    }
}
