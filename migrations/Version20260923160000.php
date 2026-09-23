<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260923160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Completes outbound planning, quality, shipping rules, documents, tracking, tours and weight controls.';
    }

    public function up(Schema $schema): void
    {
        $options = ' DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql('ALTER TABLE wms_outbound_order ADD cancelled_by CHAR(36) DEFAULT NULL, ADD cancelled_at DATETIME(6) DEFAULT NULL, ADD cancellation_reason VARCHAR(255) DEFAULT NULL, ADD CONSTRAINT FK_WMS_OUTBOUND_CANCELLED_BY FOREIGN KEY (cancelled_by) REFERENCES wms_user_account (id)');
        $this->addSql('CREATE TABLE wms_outbound_quality_check (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, pick_list_id CHAR(36) NOT NULL, completeness_passed TINYINT(1) NOT NULL, condition_passed TINYINT(1) NOT NULL, customer_check_passed TINYINT(1) NOT NULL, note VARCHAR(500) NOT NULL, decision VARCHAR(20) NOT NULL, checked_by CHAR(36) NOT NULL, checked_at DATETIME(6) NOT NULL, INDEX IDX_WMS_OUTBOUND_QUALITY_PICK_LIST (pick_list_id, checked_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_OUTBOUND_QUALITY_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_OUTBOUND_QUALITY_PICK_LIST FOREIGN KEY (pick_list_id) REFERENCES wms_pick_list (id), CONSTRAINT FK_WMS_OUTBOUND_QUALITY_USER FOREIGN KEY (checked_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_shipping_rule (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(120) NOT NULL, carrier VARCHAR(80) NOT NULL, service VARCHAR(80) NOT NULL, min_weight_grams INT UNSIGNED NOT NULL, max_weight_grams INT UNSIGNED NOT NULL, priority INT NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_SHIPPING_RULE_CODE (tenant_id, code), INDEX IDX_WMS_SHIPPING_RULE_MATCH (tenant_id, active, priority), PRIMARY KEY(id), CONSTRAINT FK_WMS_SHIPPING_RULE_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_SHIPPING_RULE_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_tracking_event (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, shipment_id CHAR(36) NOT NULL, status VARCHAR(40) NOT NULL, location VARCHAR(120) DEFAULT NULL, description VARCHAR(500) NOT NULL, source VARCHAR(30) NOT NULL, occurred_at DATETIME(6) NOT NULL, recorded_by CHAR(36) NOT NULL, recorded_at DATETIME(6) NOT NULL, INDEX IDX_WMS_TRACKING_TIMELINE (shipment_id, occurred_at), PRIMARY KEY(id), CONSTRAINT FK_WMS_TRACKING_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_TRACKING_SHIPMENT FOREIGN KEY (shipment_id) REFERENCES wms_shipment (id), CONSTRAINT FK_WMS_TRACKING_USER FOREIGN KEY (recorded_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_shipping_document (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, aggregate_type VARCHAR(30) NOT NULL, aggregate_id CHAR(36) NOT NULL, document_type VARCHAR(30) NOT NULL, document_number VARCHAR(80) NOT NULL, content LONGTEXT NOT NULL, content_type VARCHAR(80) NOT NULL, checksum CHAR(64) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_SHIPPING_DOCUMENT (tenant_id, document_type, document_number), INDEX IDX_WMS_SHIPPING_DOCUMENT_AGGREGATE (tenant_id, aggregate_type, aggregate_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_SHIPPING_DOCUMENT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_SHIPPING_DOCUMENT_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_transport_tour (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, code VARCHAR(80) NOT NULL, carrier VARCHAR(80) NOT NULL, vehicle_reference VARCHAR(80) NOT NULL, max_weight_grams INT UNSIGNED NOT NULL, departure_at DATETIME(6) NOT NULL, status VARCHAR(20) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_TRANSPORT_TOUR_CODE (tenant_id, code), PRIMARY KEY(id), CONSTRAINT FK_WMS_TRANSPORT_TOUR_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_TRANSPORT_TOUR_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
        $this->addSql('CREATE TABLE wms_tour_stop (id CHAR(36) NOT NULL, tour_id CHAR(36) NOT NULL, sequence_number INT UNSIGNED NOT NULL, destination_name VARCHAR(120) NOT NULL, destination_address VARCHAR(500) NOT NULL, shipment_id CHAR(36) DEFAULT NULL, status VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_WMS_TOUR_STOP_SEQUENCE (tour_id, sequence_number), UNIQUE INDEX UNIQ_WMS_TOUR_STOP_SHIPMENT (shipment_id), PRIMARY KEY(id), CONSTRAINT FK_WMS_TOUR_STOP_TOUR FOREIGN KEY (tour_id) REFERENCES wms_transport_tour (id), CONSTRAINT FK_WMS_TOUR_STOP_SHIPMENT FOREIGN KEY (shipment_id) REFERENCES wms_shipment (id))' . $options);
        $this->addSql('CREATE TABLE wms_weight_constraint (id CHAR(36) NOT NULL, tenant_id CHAR(36) NOT NULL, scope VARCHAR(20) NOT NULL, reference_code VARCHAR(80) DEFAULT NULL, max_weight_grams INT UNSIGNED NOT NULL, active TINYINT(1) NOT NULL, created_by CHAR(36) NOT NULL, created_at DATETIME(6) NOT NULL, UNIQUE INDEX UNIQ_WMS_WEIGHT_CONSTRAINT (tenant_id, scope, reference_code), PRIMARY KEY(id), CONSTRAINT FK_WMS_WEIGHT_CONSTRAINT_TENANT FOREIGN KEY (tenant_id) REFERENCES wms_tenant (id), CONSTRAINT FK_WMS_WEIGHT_CONSTRAINT_USER FOREIGN KEY (created_by) REFERENCES wms_user_account (id))' . $options);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wms_weight_constraint');
        $this->addSql('DROP TABLE wms_tour_stop');
        $this->addSql('DROP TABLE wms_transport_tour');
        $this->addSql('DROP TABLE wms_shipping_document');
        $this->addSql('DROP TABLE wms_tracking_event');
        $this->addSql('DROP TABLE wms_shipping_rule');
        $this->addSql('DROP TABLE wms_outbound_quality_check');
        $this->addSql('ALTER TABLE wms_outbound_order DROP FOREIGN KEY FK_WMS_OUTBOUND_CANCELLED_BY, DROP cancelled_by, DROP cancelled_at, DROP cancellation_reason');
    }
}
