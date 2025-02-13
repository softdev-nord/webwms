<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240429034521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change and add entity fields for transport_request and transport_history';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transport_history ADD from_stock_nr INT NOT NULL, ADD from_stock_level1 INT NOT NULL, ADD from_stock_level2 INT NOT NULL, ADD from_stock_level3 INT NOT NULL, ADD from_stock_level4 INT NOT NULL, ADD to_stock_coordinate VARCHAR(25) NOT NULL, ADD to_stock_nr INT NOT NULL, ADD to_stock_level1 INT NOT NULL, ADD to_stock_level2 INT NOT NULL, ADD to_stock_level3 INT NOT NULL, ADD to_stock_level4 INT NOT NULL, DROP stock_nr, DROP stock_level1, DROP stock_level2, DROP stock_level3, DROP stock_level4, CHANGE stock_coordinate from_stock_coordinate VARCHAR(25) NOT NULL');
        $this->addSql('ALTER TABLE transport_request ADD from_stock_nr INT NOT NULL, ADD from_stock_level1 INT NOT NULL, ADD from_stock_level2 INT NOT NULL, ADD from_stock_level3 INT NOT NULL, ADD from_stock_level4 INT NOT NULL, ADD to_stock_coordinate VARCHAR(25) NOT NULL, ADD to_stock_nr INT NOT NULL, ADD to_stock_level1 INT NOT NULL, ADD to_stock_level2 INT NOT NULL, ADD to_stock_level3 INT NOT NULL, ADD to_stock_level4 INT NOT NULL, DROP stock_nr, DROP stock_level1, DROP stock_level2, DROP stock_level3, DROP stock_level4, CHANGE stock_coordinate from_stock_coordinate VARCHAR(25) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transport_history ADD stock_coordinate VARCHAR(25) NOT NULL, ADD stock_nr INT NOT NULL, ADD stock_level1 INT NOT NULL, ADD stock_level2 INT NOT NULL, ADD stock_level3 INT NOT NULL, ADD stock_level4 INT NOT NULL, DROP from_stock_coordinate, DROP from_stock_nr, DROP from_stock_level1, DROP from_stock_level2, DROP from_stock_level3, DROP from_stock_level4, DROP to_stock_coordinate, DROP to_stock_nr, DROP to_stock_level1, DROP to_stock_level2, DROP to_stock_level3, DROP to_stock_level4');
        $this->addSql('ALTER TABLE transport_request ADD stock_coordinate VARCHAR(25) NOT NULL, ADD stock_nr INT NOT NULL, ADD stock_level1 INT NOT NULL, ADD stock_level2 INT NOT NULL, ADD stock_level3 INT NOT NULL, ADD stock_level4 INT NOT NULL, DROP from_stock_coordinate, DROP from_stock_nr, DROP from_stock_level1, DROP from_stock_level2, DROP from_stock_level3, DROP from_stock_level4, DROP to_stock_coordinate, DROP to_stock_nr, DROP to_stock_level1, DROP to_stock_level2, DROP to_stock_level3, DROP to_stock_level4');
    }
}
