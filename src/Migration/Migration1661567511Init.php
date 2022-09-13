<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1661567511Init extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661567511;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<'SQL'
        CREATE TABLE IF NOT EXISTS `matheus_gontijo_system_config_history` (
            `id` binary(16) NOT NULL,
            `configuration_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `configuration_value_old` json DEFAULT NULL,
            `configuration_value_new` json DEFAULT NULL,
            `sales_channel_id` binary(16) DEFAULT NULL,
            `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` datetime(3) NOT NULL,
            `updated_at` datetime(3) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->executeStatement('DROP TABLE IF EXISTS `matheus_gontijo_system_config_history`');
    }
}
