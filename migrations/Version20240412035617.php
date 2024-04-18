<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240412035617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transport_history CHANGE doc_id doc_id INT DEFAULT NULL, CHANGE order_nr order_nr VARCHAR(30) DEFAULT NULL, CHANGE order_pos order_pos INT DEFAULT NULL, CHANGE confirmation_state confirmation_state INT DEFAULT NULL, CHANGE tr_username tr_username VARCHAR(30) DEFAULT NULL, CHANGE tr_blocked tr_blocked TINYINT(1) DEFAULT 0, CHANGE tr_edited tr_edited TINYINT(1) DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transport_history CHANGE doc_id doc_id INT NOT NULL, CHANGE order_nr order_nr VARCHAR(30) NOT NULL, CHANGE order_pos order_pos INT NOT NULL, CHANGE confirmation_state confirmation_state INT DEFAULT 0 NOT NULL, CHANGE tr_username tr_username VARCHAR(30) NOT NULL, CHANGE tr_blocked tr_blocked TINYINT(1) DEFAULT 0 NOT NULL, CHANGE tr_edited tr_edited TINYINT(1) DEFAULT 0 NOT NULL');
    }
}
