<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220321195533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock_transfer_strategy (id INT AUTO_INCREMENT NOT NULL, short_code VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_pos ADD bst_id INT DEFAULT NULL, ADD art_id INT DEFAULT NULL, DROP order_id, DROP article_id, CHANGE order_pos_quantity bst_pos_menge INT NOT NULL');
        $this->addSql('ALTER TABLE stock_rotation CHANGE article_id art_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transport_history CHANGE charge charge VARCHAR(20) NOT NULL, CHANGE confirmation_state confirmation_state INT NOT NULL, CHANGE tr_blocked tr_blocked INT NOT NULL, CHANGE tr_start_date tr_start_date DATETIME NOT NULL, CHANGE article_nr art_nr VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE transport_request CHANGE charge charge VARCHAR(20) NOT NULL, CHANGE confirmation_state confirmation_state INT NOT NULL, CHANGE tr_blocked tr_blocked INT NOT NULL, CHANGE tr_start_date tr_start_date DATETIME NOT NULL, CHANGE article_nr art_nr VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE stock_transfer_strategy');
        $this->addSql('ALTER TABLE order_pos ADD order_id INT DEFAULT NULL, ADD article_id INT DEFAULT NULL, DROP bst_id, DROP art_id, CHANGE bst_pos_menge order_pos_quantity INT NOT NULL');
        $this->addSql('ALTER TABLE stock_rotation CHANGE art_id article_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transport_history CHANGE charge charge VARCHAR(20) DEFAULT NULL, CHANGE confirmation_state confirmation_state INT DEFAULT NULL, CHANGE tr_blocked tr_blocked INT DEFAULT NULL, CHANGE tr_start_date tr_start_date DATETIME DEFAULT NULL, CHANGE art_nr article_nr VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE transport_request CHANGE charge charge VARCHAR(20) DEFAULT NULL, CHANGE confirmation_state confirmation_state INT DEFAULT NULL, CHANGE tr_blocked tr_blocked INT DEFAULT NULL, CHANGE tr_start_date tr_start_date DATETIME DEFAULT NULL, CHANGE art_nr article_nr VARCHAR(20) NOT NULL');
    }
}
