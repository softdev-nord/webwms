<?php

declare(strict_types=1);

namespace WebWMS\Components\DataAbstractionLayer\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class EntityDefinitionQueryHelper
{
    public static function escape(string $string): string
    {
        if (mb_strpos($string, '`') !== false) {
            throw new \InvalidArgumentException('Backtick not allowed in identifier');
        }

        return '`' . $string . '`';
    }

    /**
     * @throws Exception
     */
    public static function columnExists(Connection $connection, string $table, string $column): bool
    {
        $exists = $connection->fetchOne(
            'SHOW COLUMNS FROM ' . self::escape($table) . ' WHERE `Field` LIKE :column',
            ['column' => $column]
        );

        return !isset($exists);
    }

    /**
     * @throws Exception
     */
    public static function tableExists(Connection $connection, string $table): bool
    {
        return !$connection->fetchOne(
                'SHOW TABLES LIKE :table',
                [
                    'table' => $table,
                ]
            ) != null;
    }
}
