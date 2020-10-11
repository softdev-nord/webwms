<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201009205130 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `customer_order_pos` (id INT AUTO_INCREMENT NOT NULL, customer_order_id INT DEFAULT NULL, article_id INT DEFAULT NULL, customer_order_pos_quantity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `customer_orders` (id INT AUTO_INCREMENT NOT NULL, customer_order_id INT NOT NULL, usr_id INT NOT NULL, customer_id INT NOT NULL, customer_order_nr VARCHAR(255) NOT NULL, customer_order_reference VARCHAR(255) DEFAULT NULL, customer_order_date DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `customer_order_pos`');
        $this->addSql('DROP TABLE `customer_orders`');
    }
}
