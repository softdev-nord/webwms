<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221102075855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE transport_history CHANGE charge charge VARCHAR(20) NOT NULL, CHANGE confirmation_state confirmation_state INT NOT NULL, CHANGE tr_blocked tr_blocked INT NOT NULL, CHANGE tr_start_date tr_start_date DATETIME NOT NULL');
        // $this->addSql('ALTER TABLE transport_request CHANGE doc_id doc_id INT NOT NULL, CHANGE charge charge VARCHAR(20) NOT NULL, CHANGE confirmation_state confirmation_state INT NOT NULL, CHANGE tr_blocked tr_blocked INT NOT NULL, CHANGE tr_start_date tr_start_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transport_history CHANGE charge charge VARCHAR(20) DEFAULT NULL, CHANGE confirmation_state confirmation_state INT DEFAULT NULL, CHANGE tr_blocked tr_blocked INT DEFAULT NULL, CHANGE tr_start_date tr_start_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE transport_request CHANGE doc_id doc_id INT DEFAULT NULL, CHANGE charge charge VARCHAR(20) DEFAULT NULL, CHANGE confirmation_state confirmation_state INT DEFAULT NULL, CHANGE tr_blocked tr_blocked INT DEFAULT NULL, CHANGE tr_start_date tr_start_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
