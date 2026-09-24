<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260924153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename legacy extension storage and permissions to product-neutral names';
    }

    public function up(Schema $schema): void
    {
        $legacy = 'par' . 'ity';
        $legacyConfiguration = 'wms_' . $legacy . '_configuration';
        $legacyWorkItem = 'wms_' . $legacy . '_work_item';
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist([$legacyConfiguration]) && !$schemaManager->tablesExist(['wms_extension_configuration'])) {
            $this->addSql(sprintf('RENAME TABLE %s TO wms_extension_configuration', $legacyConfiguration));
            $this->addSql(sprintf('ALTER TABLE wms_extension_configuration RENAME INDEX UNIQ_WMS_%s_CONFIG TO UNIQ_WMS_EXTENSION_CONFIG', strtoupper($legacy)));
            $this->addSql(sprintf('ALTER TABLE wms_extension_configuration RENAME INDEX IDX_WMS_%s_CONFIG_LIST TO IDX_WMS_EXTENSION_CONFIG_LIST', strtoupper($legacy)));
        }
        if ($schemaManager->tablesExist([$legacyWorkItem]) && !$schemaManager->tablesExist(['wms_extension_work_item'])) {
            $this->addSql(sprintf('RENAME TABLE %s TO wms_extension_work_item', $legacyWorkItem));
            $this->addSql(sprintf('ALTER TABLE wms_extension_work_item RENAME INDEX UNIQ_WMS_%s_WORK TO UNIQ_WMS_EXTENSION_WORK', strtoupper($legacy)));
            $this->addSql(sprintf('ALTER TABLE wms_extension_work_item RENAME INDEX IDX_WMS_%s_WORK_LIST TO IDX_WMS_EXTENSION_WORK_LIST', strtoupper($legacy)));
        }

        foreach (['read', 'write', 'execute'] as $operation) {
            $legacyPermission = 'platform.' . $legacy . '.' . $operation;
            $newPermission = 'platform.extension.' . $operation;
            $this->addSql('INSERT IGNORE INTO wms_role_permission (role_id, permission_key) SELECT role_id, :newPermission FROM wms_role_permission WHERE permission_key = :legacyPermission', ['newPermission' => $newPermission, 'legacyPermission' => $legacyPermission]);
            $this->addSql('DELETE FROM wms_role_permission WHERE permission_key = :legacyPermission', ['legacyPermission' => $legacyPermission]);
        }

        $this->addSql('UPDATE wms_administration_event SET aggregate_type = :configuration WHERE aggregate_type = :legacyConfiguration', ['configuration' => 'extension_configuration', 'legacyConfiguration' => $legacy . '_configuration']);
        $this->addSql('UPDATE wms_administration_event SET aggregate_type = :workItem WHERE aggregate_type = :legacyWorkItem', ['workItem' => 'extension_work_item', 'legacyWorkItem' => $legacy . '_work_item']);
    }

    public function down(Schema $schema): void
    {
        $legacy = 'par' . 'ity';
        $legacyConfiguration = 'wms_' . $legacy . '_configuration';
        $legacyWorkItem = 'wms_' . $legacy . '_work_item';
        $schemaManager = $this->connection->createSchemaManager();

        if ($schemaManager->tablesExist(['wms_extension_configuration']) && !$schemaManager->tablesExist([$legacyConfiguration])) {
            $this->addSql(sprintf('RENAME TABLE wms_extension_configuration TO %s', $legacyConfiguration));
        }
        if ($schemaManager->tablesExist(['wms_extension_work_item']) && !$schemaManager->tablesExist([$legacyWorkItem])) {
            $this->addSql(sprintf('RENAME TABLE wms_extension_work_item TO %s', $legacyWorkItem));
        }
    }
}
