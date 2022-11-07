<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221102084841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_feature (group_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_99D7216AFE54D947 (group_id), INDEX IDX_99D7216A60E4B879 (feature_id), PRIMARY KEY(group_id, feature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permission (role_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_6F7DF886D60322AC (role_id), INDEX IDX_6F7DF886FED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_8F02BF9DA76ED395 (user_id), INDEX IDX_8F02BF9DFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_feature ADD CONSTRAINT FK_99D7216AFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE group_feature ADD CONSTRAINT FK_99D7216A60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        //$this->addSql('ALTER TABLE transport_history CHANGE charge charge VARCHAR(20) NOT NULL, CHANGE confirmation_state confirmation_state INT NOT NULL, CHANGE tr_blocked tr_blocked INT NOT NULL, CHANGE tr_start_date tr_start_date DATETIME NOT NULL');
        //$this->addSql('ALTER TABLE transport_request CHANGE doc_id doc_id INT NOT NULL, CHANGE charge charge VARCHAR(20) NOT NULL, CHANGE confirmation_state confirmation_state INT NOT NULL, CHANGE tr_blocked tr_blocked INT NOT NULL, CHANGE tr_start_date tr_start_date DATETIME NOT NULL');
        //$this->addSql('ALTER TABLE user ADD email VARCHAR(255) NOT NULL, ADD last_login DATETIME DEFAULT NULL, ADD enabled TINYINT(1) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, CHANGE username username VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_feature DROP FOREIGN KEY FK_99D7216AFE54D947');
        $this->addSql('ALTER TABLE group_feature DROP FOREIGN KEY FK_99D7216A60E4B879');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886D60322AC');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886FED90CCA');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('DROP TABLE group_feature');
        $this->addSql('DROP TABLE role_permission');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('ALTER TABLE transport_history CHANGE charge charge VARCHAR(20) DEFAULT NULL, CHANGE confirmation_state confirmation_state INT DEFAULT NULL, CHANGE tr_blocked tr_blocked INT DEFAULT NULL, CHANGE tr_start_date tr_start_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE transport_request CHANGE doc_id doc_id INT DEFAULT NULL, CHANGE charge charge VARCHAR(20) DEFAULT NULL, CHANGE confirmation_state confirmation_state INT DEFAULT NULL, CHANGE tr_blocked tr_blocked INT DEFAULT NULL, CHANGE tr_start_date tr_start_date DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP email, DROP last_login, DROP enabled, DROP created_at, DROP updated_at, CHANGE username username VARCHAR(180) NOT NULL');
    }
}
