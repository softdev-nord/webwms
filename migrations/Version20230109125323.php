<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109125323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE `acl_role` (
                `id` INT(11) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        $this->addSql('
            CREATE TABLE `acl_resource` (
                `id` INT(11) NOT NULL,
                `privilege` VARCHAR(255) NOT NULL,
                `acl_role_id` INT(11) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`privilege`, `acl_role_id`),
                CONSTRAINT `fk.acl_resource.acl_role_id` FOREIGN KEY (`acl_role_id`)
                    REFERENCES `acl_role` (`id`) on DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        $this->addSql('
            CREATE TABLE `acl_user_role` (
                `user_id` INT(11) NOT NULL,
                `acl_role_id` INT(11) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`user_id`, `acl_role_id`),
                CONSTRAINT `fk.acl_user_role.user_id` FOREIGN KEY (`user_id`)
                    REFERENCES `user` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk.acl_user_role.acl_role_id` FOREIGN KEY (`acl_role_id`)
                    REFERENCES `acl_role` (`id`) on DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
