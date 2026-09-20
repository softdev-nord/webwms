<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260920120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create measurement devices and journal and add package and product dimensions';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('ALTER TABLE wms_package ADD length_mm INT DEFAULT NULL, ADD width_mm INT DEFAULT NULL, ADD height_mm INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wms_product_reference ADD weight_grams INT DEFAULT NULL, ADD length_mm INT DEFAULT NULL, ADD width_mm INT DEFAULT NULL, ADD height_mm INT DEFAULT NULL');
        $this->addSql('CREATE TABLE wms_measurement_device (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(40) NOT NULL, name VARCHAR(100) NOT NULL, device_type VARCHAR(30) NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, changed_by CHAR(36) DEFAULT NULL, changed_at DATETIME(6) DEFAULT NULL, UNIQUE INDEX UNIQ_WMS_MEASUREMENT_DEVICE_CODE (tenant_id, code), INDEX IDX_WMS_MEASUREMENT_DEVICE_ACTIVE (tenant_id, active), PRIMARY KEY(id), CONSTRAINT FK_WMS_MEASUREMENT_DEVICE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_MEASUREMENT_DEVICE_CREATED_BY FOREIGN KEY (created_by) REFERENCES wms_user_account (id), CONSTRAINT FK_WMS_MEASUREMENT_DEVICE_CHANGED_BY FOREIGN KEY (changed_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_measurement (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, device_id CHAR(36) NOT NULL, target_type VARCHAR(20) NOT NULL, target_id CHAR(36) NOT NULL, weight_grams INT DEFAULT NULL, length_mm INT DEFAULT NULL, width_mm INT DEFAULT NULL, height_mm INT DEFAULT NULL, request_id VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, message VARCHAR(500) DEFAULT NULL, measured_by CHAR(36) NOT NULL, measured_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_MEASUREMENT_REQUEST (tenant_id, request_id), INDEX IDX_WMS_MEASUREMENT_JOURNAL (tenant_id, measured_at), INDEX IDX_WMS_MEASUREMENT_TARGET (tenant_id, target_type, target_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_MEASUREMENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_MEASUREMENT_DEVICE FOREIGN KEY (device_id) REFERENCES wms_measurement_device (id), CONSTRAINT FK_WMS_MEASUREMENT_USER FOREIGN KEY (measured_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_measurement');
        $this->addSql('DROP TABLE wms_measurement_device');
        $this->addSql('ALTER TABLE wms_product_reference DROP weight_grams, DROP length_mm, DROP width_mm, DROP height_mm');
        $this->addSql('ALTER TABLE wms_package DROP length_mm, DROP width_mm, DROP height_mm');
    }
}
