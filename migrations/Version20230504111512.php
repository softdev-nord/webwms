<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230504111512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE configuration CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer CHANGE customer_address_addition customer_address_addition VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_orders CHANGE customer_order_reference customer_order_reference VARCHAR(255) DEFAULT NULL, CHANGE customer_order_date customer_order_date DATETIME DEFAULT NULL, CHANGE customer_order_creation_date customer_order_creation_date DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_orders ADD CONSTRAINT FK_54EA21BF9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id)');
        $this->addSql('CREATE INDEX IDX_54EA21BF9395C3F3 ON customer_orders (customer_id)');
        $this->addSql('ALTER TABLE customer_orders_pos CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE roles DROP description, CHANGE role role VARCHAR(20) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE stock_layout CHANGE stock_long_description stock_long_description VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE stock_location CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE stock_occupancy CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE outgoing_stock reserved_stock INT NOT NULL');
        $this->addSql('ALTER TABLE stock_rotation CHANGE access_date access_date DATETIME DEFAULT NULL, CHANGE dispatch_date dispatch_date DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE stock_transfer_strategy CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE stock_zone CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE stock_zone_layout CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE supplier CHANGE supplier_address_addition supplier_address_addition VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE supplier_order_pos CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE supplier_orders CHANGE supplier_id supplier_id INT NOT NULL, CHANGE supplier_order_reference supplier_order_reference VARCHAR(255) DEFAULT NULL, CHANGE supplier_order_date supplier_order_date DATETIME DEFAULT NULL, CHANGE supplier_order_creation_date supplier_order_creation_date DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE supplier_orders ADD CONSTRAINT FK_3E4D2F3A2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (supplier_id)');
        $this->addSql('CREATE INDEX IDX_3E4D2F3A2ADD6D8C ON supplier_orders (supplier_id)');
        $this->addSql('ALTER TABLE transport_history CHANGE tr_access tr_access DATETIME DEFAULT NULL, CHANGE tr_dispatch tr_dispatch DATETIME DEFAULT NULL, CHANGE doc_id doc_id INT NOT NULL, CHANGE charge charge VARCHAR(30) DEFAULT NULL, CHANGE tr_start_date tr_start_date DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE transport_request CHANGE tr_access tr_access DATETIME DEFAULT NULL, CHANGE tr_dispatch tr_dispatch DATETIME DEFAULT NULL, CHANGE order_nr order_nr VARCHAR(30) DEFAULT NULL, CHANGE charge charge VARCHAR(30) DEFAULT NULL, CHANGE tr_username tr_username VARCHAR(30) DEFAULT NULL, CHANGE tr_blocked tr_blocked TINYINT(1) DEFAULT 0, CHANGE tr_start_date tr_start_date DATETIME DEFAULT NULL, CHANGE tr_edited tr_edited TINYINT(1) DEFAULT 0, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON DEFAULT NULL, CHANGE firstname firstname VARCHAR(255) DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE roles ADD description VARCHAR(255) DEFAULT \'NULL\', CHANGE role role VARCHAR(50) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE stock_transfer_strategy CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE transport_history CHANGE tr_access tr_access DATETIME DEFAULT \'NULL\', CHANGE tr_dispatch tr_dispatch DATETIME DEFAULT \'NULL\', CHANGE doc_id doc_id INT DEFAULT NULL, CHANGE charge charge VARCHAR(30) DEFAULT \'NULL\', CHANGE tr_start_date tr_start_date DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE customer_orders DROP FOREIGN KEY FK_54EA21BF9395C3F3');
        $this->addSql('DROP INDEX IDX_54EA21BF9395C3F3 ON customer_orders');
        $this->addSql('ALTER TABLE customer_orders CHANGE customer_order_reference customer_order_reference VARCHAR(255) DEFAULT \'NULL\', CHANGE customer_order_date customer_order_date DATETIME DEFAULT \'NULL\', CHANGE customer_order_creation_date customer_order_creation_date DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE stock_zone CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE supplier_orders DROP FOREIGN KEY FK_3E4D2F3A2ADD6D8C');
        $this->addSql('DROP INDEX IDX_3E4D2F3A2ADD6D8C ON supplier_orders');
        $this->addSql('ALTER TABLE supplier_orders CHANGE supplier_id supplier_id INT DEFAULT NULL, CHANGE supplier_order_reference supplier_order_reference VARCHAR(255) DEFAULT \'NULL\', CHANGE supplier_order_date supplier_order_date DATETIME DEFAULT \'NULL\', CHANGE supplier_order_creation_date supplier_order_creation_date DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE stock_zone_layout CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE customer_orders_pos CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE transport_request CHANGE tr_access tr_access DATETIME DEFAULT \'NULL\', CHANGE tr_dispatch tr_dispatch DATETIME DEFAULT \'NULL\', CHANGE order_nr order_nr VARCHAR(30) DEFAULT \'NULL\', CHANGE charge charge VARCHAR(30) DEFAULT \'NULL\', CHANGE tr_username tr_username VARCHAR(30) DEFAULT \'NULL\', CHANGE tr_blocked tr_blocked TINYINT(1) DEFAULT NULL, CHANGE tr_start_date tr_start_date DATETIME DEFAULT \'NULL\', CHANGE tr_edited tr_edited TINYINT(1) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE firstname firstname VARCHAR(255) DEFAULT \'NULL\', CHANGE lastname lastname VARCHAR(255) DEFAULT \'NULL\', CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE stock_rotation CHANGE access_date access_date DATETIME DEFAULT \'NULL\', CHANGE dispatch_date dispatch_date DATETIME DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE customer CHANGE customer_address_addition customer_address_addition VARCHAR(255) DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE stock_location CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE stock_layout CHANGE stock_long_description stock_long_description VARCHAR(255) DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE configuration CHANGE description description VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE supplier CHANGE supplier_address_addition supplier_address_addition VARCHAR(255) DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE supplier_order_pos CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE stock_occupancy CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\', CHANGE reserved_stock outgoing_stock INT NOT NULL');
    }
}
