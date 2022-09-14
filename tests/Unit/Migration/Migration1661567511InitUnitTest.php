<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Migration;

use Doctrine\DBAL\Connection;
use MatheusGontijo\SystemConfigHistory\Migration\Migration1661567511Init;
use PHPUnit\Framework\TestCase;

class Migration1661567511InitUnitTest extends TestCase
{
    public function testGetCreationTimestamp(): void
    {
        $migration1661567511Init = new Migration1661567511Init();

        static::assertSame(1661567511, $migration1661567511Init->getCreationTimestamp());
    }

    public function testUpdate(): void
    {
        $migration1661567511Init = new Migration1661567511Init();

        $connection = $this->createMock(Connection::class);

        $sql1 = <<<'SQL'
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL;

        $sql2 = <<<'SQL'
        INSERT IGNORE INTO system_config (id, configuration_key, configuration_value, created_at) VALUES (
            x'e1adb08e9f2648dbafb0f2536eea4f23',
            'matheusGontijo.systemConfigHistory.enabled',
            '{"_value": true}',
            NOW()
        )
        SQL;

        $connection->expects(static::exactly(2))
            ->method('executeStatement')
            ->withConsecutive([$sql1], [$sql2]);

        $migration1661567511Init->update($connection);
    }

    public function testUpdateDestructive(): void
    {
        $migration1661567511Init = new Migration1661567511Init();

        $connection = $this->createMock(Connection::class);

        $sql = <<<'SQL'
        DROP TABLE IF EXISTS `matheus_gontijo_system_config_history`
        SQL;

        $connection->expects(static::exactly(1))
            ->method('executeStatement')
            ->withConsecutive([$sql]);

        $migration1661567511Init->updateDestructive($connection);
    }
}
