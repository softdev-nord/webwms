<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260902000003MigrateTransportRequestTrStateToString extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate transport_request.tr_state from INT to STRING for workflow state machine';
    }

    public function up(Schema $schema): void
    {
        // Backup alte int-States in Mapping-Tabelle
        $this->addSql('
            ALTER TABLE transport_request 
            ADD COLUMN tr_state_string VARCHAR(20)
        ');

        // Mapping: 0 -> "open", > 0 -> "done", Negativ -> "cancelled"
        $this->addSql('
            UPDATE transport_request 
            SET tr_state_string = CASE 
                WHEN tr_state = 0 THEN "open"
                WHEN tr_state > 0 THEN "done"
                ELSE "cancelled"
            END
        ');

        // Setze default auf "open" falls NULL
        $this->addSql('
            UPDATE transport_request 
            SET tr_state_string = "open" 
            WHERE tr_state_string IS NULL
        ');

        // Ändere original Spalte zu STRING
        $this->addSql('
            ALTER TABLE transport_request 
            DROP COLUMN tr_state,
            CHANGE COLUMN tr_state_string tr_state VARCHAR(20) NOT NULL DEFAULT "open"
        ');

        // Indizes aktualisieren
        $this->addSql('
            ALTER TABLE transport_request 
            ADD INDEX idx_tr_state (tr_state)
        ');
    }

    public function down(Schema $schema): void
    {
        // Rollback: String → Int
        $this->addSql('
            ALTER TABLE transport_request 
            ADD COLUMN tr_state_int INT
        ');

        $this->addSql('
            UPDATE transport_request 
            SET tr_state_int = CASE 
                WHEN tr_state = "open" THEN 0
                WHEN tr_state = "in_progress" THEN 1
                WHEN tr_state = "done" THEN 2
                WHEN tr_state = "cancelled" THEN -1
                ELSE 0
            END
        ');

        $this->addSql('
            ALTER TABLE transport_request 
            DROP COLUMN tr_state,
            CHANGE COLUMN tr_state_int tr_state INT NOT NULL DEFAULT 0
        ');
    }
}

