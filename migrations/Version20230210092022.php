<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230210092022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE templates');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE customer_orders ADD CONSTRAINT FK_54EA21BF9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_54EA21BF9395C3F3 ON customer_orders (customer_id)');
        $this->addSql('ALTER TABLE roles DROP description, CHANGE role role VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE supplier CHANGE supplier_address_addition supplier_address_addition VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE supplier_orders ADD CONSTRAINT FK_3E4D2F3A2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (supplier_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3E4D2F3A2ADD6D8C ON supplier_orders (supplier_id)');
        $this->addSql('ALTER TABLE transport_history CHANGE doc_id doc_id INT NOT NULL');
        $this->addSql('ALTER TABLE transport_request CHANGE tr_blocked tr_blocked TINYINT(1) DEFAULT 0, CHANGE tr_edited tr_edited TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_1483A5E9D60322AC');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX fk_1483a5e9d60322ac ON user');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON user (role_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE templates (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, text LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, params VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_default TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE roles ADD description VARCHAR(255) DEFAULT NULL, CHANGE role role VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE transport_history CHANGE doc_id doc_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_orders DROP FOREIGN KEY FK_54EA21BF9395C3F3');
        $this->addSql('DROP INDEX UNIQ_54EA21BF9395C3F3 ON customer_orders');
        $this->addSql('ALTER TABLE supplier_orders DROP FOREIGN KEY FK_3E4D2F3A2ADD6D8C');
        $this->addSql('DROP INDEX UNIQ_3E4D2F3A2ADD6D8C ON supplier_orders');
        $this->addSql('ALTER TABLE transport_request CHANGE tr_blocked tr_blocked TINYINT(1) DEFAULT NULL, CHANGE tr_edited tr_edited TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX idx_8d93d649d60322ac ON user');
        $this->addSql('CREATE INDEX FK_1483A5E9D60322AC ON user (role_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE supplier CHANGE supplier_address_addition supplier_address_addition VARCHAR(255) DEFAULT NULL');
    }
}
