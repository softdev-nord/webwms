<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261005180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sender snapshots to planned inbound deliveries and recipient snapshots to outbound orders';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_inbound_delivery ADD sender_name VARCHAR(160) DEFAULT NULL, ADD sender_street VARCHAR(255) DEFAULT NULL, ADD sender_postal_code VARCHAR(32) DEFAULT NULL, ADD sender_city VARCHAR(120) DEFAULT NULL, ADD sender_country_code CHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE wms_outbound_order ADD recipient_name VARCHAR(160) DEFAULT NULL, ADD recipient_street VARCHAR(255) DEFAULT NULL, ADD recipient_postal_code VARCHAR(32) DEFAULT NULL, ADD recipient_city VARCHAR(120) DEFAULT NULL, ADD recipient_country_code CHAR(2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wms_inbound_delivery DROP sender_name, DROP sender_street, DROP sender_postal_code, DROP sender_city, DROP sender_country_code');
        $this->addSql('ALTER TABLE wms_outbound_order DROP recipient_name, DROP recipient_street, DROP recipient_postal_code, DROP recipient_city, DROP recipient_country_code');
    }
}
