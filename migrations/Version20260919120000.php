<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260919120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add reliable outbox publishing, delivery attempts and the integration queue';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('ALTER TABLE wms_integration_outbox ADD attempt_count INT DEFAULT 0 NOT NULL, ADD next_attempt_at DATETIME(6) DEFAULT NULL, ADD claimed_at DATETIME(6) DEFAULT NULL, ADD published_at DATETIME(6) DEFAULT NULL, ADD last_error VARCHAR(1000) DEFAULT NULL, ADD retried_by CHAR(36) DEFAULT NULL, ADD retried_at DATETIME(6) DEFAULT NULL, ADD CONSTRAINT FK_WMS_OUTBOX_RETRIED_BY FOREIGN KEY (retried_by) REFERENCES wms_user_account (id)');
        $this->addSql('CREATE INDEX IDX_WMS_OUTBOX_DUE ON wms_integration_outbox (status, next_attempt_at, claimed_at, id)');
        $this->addSql('CREATE TABLE wms_integration_attempt (id CHAR(36) NOT NULL, message_id CHAR(36) NOT NULL, attempt_number INT NOT NULL, outcome VARCHAR(30) NOT NULL, error VARCHAR(1000) DEFAULT NULL, attempted_at DATETIME(6) NOT NULL, INDEX IDX_WMS_ATTEMPT_MESSAGE (message_id, attempt_number), PRIMARY KEY(id), CONSTRAINT FK_WMS_ATTEMPT_MESSAGE FOREIGN KEY (message_id) REFERENCES wms_integration_outbox (id) ON DELETE CASCADE)' . $options);
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE wms_integration_attempt');
        $this->addSql('ALTER TABLE wms_integration_outbox DROP FOREIGN KEY FK_WMS_OUTBOX_RETRIED_BY');
        $this->addSql('DROP INDEX IDX_WMS_OUTBOX_DUE ON wms_integration_outbox');
        $this->addSql('ALTER TABLE wms_integration_outbox DROP attempt_count, DROP next_attempt_at, DROP claimed_at, DROP published_at, DROP last_error, DROP retried_by, DROP retried_at');
    }
}
