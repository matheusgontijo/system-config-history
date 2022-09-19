<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Repository\System\MatheusGontijoSystemConfigHistory\Api;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api\MatheusGontijoSystemConfigHistoryRouteRepository; // phpcs:ignore
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

class MatheusGontijoSystemConfigHistoryRouteRepositoryIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function setUp(): void
    {
        parent::setUp();

        $this->populateTableWithData();
    }

    public function testGetRowsLimitAndOffset(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('c21900bab54c4c3a8cd92e6fb0a09be6'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('0299bd94a4b44570b7f49654da0c6c29'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('136f2285f9e742ac85369726bb90c93f'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $matheusGontijoSystemConfigHistoryRouteRepository = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryRouteRepository::class
        );

        \assert($matheusGontijoSystemConfigHistoryRouteRepository instanceof MatheusGontijoSystemConfigHistoryRouteRepository); // phpcs:ignore

        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['LOWER(HEX(id)) AS id']);
        $qb->from('locale');
        $qb->where('code = \'en-GB\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $defaultEnGbLocaleId = $executeResult->fetchOne();

        $filters = [
            'configuration_key' => 'foo.bar.enabled',
            'configuration_value_old' => '',
            'configuration_value_new' => '',
            'sales_channel_name' => '',
            'username' => '',
            'created_at' => '',
        ];

        $rows = $matheusGontijoSystemConfigHistoryRouteRepository->getRows(
            $defaultEnGbLocaleId,
            'Default',
            $filters,
            'configuration_key',
            'DESC',
            2,
            1
        );

        static::assertCount(1, $rows);

        static::assertSame([
            'id' => '0299bd94a4b44570b7f49654da0c6c29',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => 'false',
            'configuration_value_new' => 'true',
            'sales_channel_name' => 'English Sales Channel',
            'username' => null,
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[0]);
    }

    public function testSecondOrderBy(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa1'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa3'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa2'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows(['configuration_key' => 'foo.bar.enabled'], 'configuration_key');

        static::assertCount(3, $rows);

        static::assertSame([
            'id' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa3',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => 'false',
            'configuration_value_new' => 'true',
            'sales_channel_name' => 'Default',
            'username' => null,
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa2',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => 'false',
            'configuration_value_new' => 'true',
            'sales_channel_name' => 'English Sales Channel',
            'username' => null,
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[1]);

        static::assertSame([
            'id' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa1',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => 'false',
            'configuration_value_new' => 'true',
            'sales_channel_name' => 'German Sales Channel',
            'username' => null,
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[2]);
    }

    public function testConfigurationKeyColumnFilterAndSort(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": true}',
                'configuration_value_new' => '{"_value": false}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": false}',
                'configuration_value_new' => '{"_value": true}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows(['configuration_key' => 'oo.bar.enable'], 'configuration_key');

        static::assertCount(3, $rows);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => 'false',
            'configuration_value_new' => 'true',
            'sales_channel_name' => 'Default',
            'username' => null,
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => 'true',
            'configuration_value_new' => 'false',
            'sales_channel_name' => 'English Sales Channel',
            'username' => null,
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[1]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => 'false',
            'configuration_value_new' => 'true',
            'sales_channel_name' => 'German Sales Channel',
            'username' => null,
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[2]);
    }

    public function testConfigurationValueOldColumnFilterAndSort(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": "mycustomvalue_789"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_111"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": "mycustomvalue_456"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_111"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": "mycustomvalue_123"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_111"}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows(['configuration_value_old' => 'ycustomvalue_'], 'configuration_value_old');

        static::assertCount(3, $rows);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => 'mycustomvalue_123',
            'configuration_value_new' => 'mycustomvalue_111',
            'sales_channel_name' => 'Default',
            'username' => null,
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => 'mycustomvalue_456',
            'configuration_value_new' => 'mycustomvalue_111',
            'sales_channel_name' => 'English Sales Channel',
            'username' => null,
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[1]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => 'mycustomvalue_789',
            'configuration_value_new' => 'mycustomvalue_111',
            'sales_channel_name' => 'German Sales Channel',
            'username' => null,
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[2]);
    }

    public function testSalesChannelIdColumnFilterAndSort(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": "mycustomvalue_111"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_789"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": "mycustomvalue_111"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_456"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": "mycustomvalue_111"}',
                'configuration_value_new' => '{"_value": "mycustomvalue_123"}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows(['configuration_value_new' => 'ycustomvalue_'], 'configuration_value_new');

        static::assertCount(3, $rows);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => 'mycustomvalue_111',
            'configuration_value_new' => 'mycustomvalue_123',
            'sales_channel_name' => 'Default',
            'username' => null,
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => 'mycustomvalue_111',
            'configuration_value_new' => 'mycustomvalue_456',
            'sales_channel_name' => 'English Sales Channel',
            'username' => null,
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[1]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => 'mycustomvalue_111',
            'configuration_value_new' => 'mycustomvalue_789',
            'sales_channel_name' => 'German Sales Channel',
            'username' => null,
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[2]);
    }

    public function testConfigurationValueNewColumnFilterAndSort(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => null,
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows([
            'configuration_key' => 'foo.bar.enabled',
            'sales_channel_name' => 'efaul',
        ], 'sales_channel_name');

        static::assertCount(2, $rows);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'Default',
            'username' => null,
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'Default',
            'username' => null,
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[1]);
    }

    public function testUsernameColumnFilterAndSort(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'username' => 'zzz.gontijo',
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'username' => 'zzz.gontijo',
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => null,
                'username' => 'aaa.john',
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows(['username' => 'z.gontij'], 'username');

        static::assertCount(2, $rows);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'English Sales Channel',
            'username' => 'zzz.gontijo',
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'German Sales Channel',
            'username' => 'zzz.gontijo',
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[1]);
    }

    public function testCreatedAtColumnFilterAndSort(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'username' => 'matheus.gontijo',
                'created_at' => '1992-11-20 23:59:59.999',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'username' => 'matheus.gontijo',
                'created_at' => '1992-11-20 23:59:59.999',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => null,
                'username' => 'aaa.john',
                'created_at' => '2022-09-01 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $rows = $this->getRows(['created_at' => '992-11-20 23:59:5'], 'created_at');

        static::assertCount(2, $rows);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'English Sales Channel',
            'username' => 'matheus.gontijo',
            'created_at' => '1992-11-20 23:59:59.999',
        ], $rows[0]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'German Sales Channel',
            'username' => 'matheus.gontijo',
            'created_at' => '1992-11-20 23:59:59.999',
        ], $rows[1]);
    }

    public function testNonEnGbDefaultSalesName(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => null,
                'username' => 'aaa.john',
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => null,
                'username' => 'matheus.gontijo',
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => null,
                'username' => 'matheus.gontijo',
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $qb = $connection->createQueryBuilder();
        $qb->select(['LOWER(HEX(id)) AS id']);
        $qb->from('locale');
        $qb->where('code = \'de-DE\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $deDeLocaleId = $executeResult->fetchOne();

        $filters = [
            'configuration_key' => 'foo.bar.enabled',
            'configuration_value_old' => null,
            'configuration_value_new' => null,
            'sales_channel_name' => 'tandar',
            'username' => null,
            'created_at' => null,
        ];

        $matheusGontijoSystemConfigHistoryRouteRepository = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryRouteRepository::class
        );

        \assert($matheusGontijoSystemConfigHistoryRouteRepository instanceof MatheusGontijoSystemConfigHistoryRouteRepository);  // phpcs:ignore

        $rows = $matheusGontijoSystemConfigHistoryRouteRepository->getRows(
            $deDeLocaleId,
            'Standard',
            $filters,
            'created_at',
            'ASC',
            1,
            100
        );

        static::assertCount(3, $rows);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'Standard',
            'username' => 'matheus.gontijo',
            'created_at' => '2022-01-01 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'Standard',
            'username' => 'matheus.gontijo',
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[1]);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'Standard',
            'username' => 'aaa.john',
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[2]);
    }

    public function testCountWithoutFilters(): void
    {
        $matheusGontijoSystemConfigHistoryRouteRepository = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryRouteRepository::class
        );

        \assert($matheusGontijoSystemConfigHistoryRouteRepository instanceof MatheusGontijoSystemConfigHistoryRouteRepository);  // phpcs:ignore

        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['LOWER(HEX(id)) AS id']);
        $qb->from('locale');
        $qb->where('code = \'en-GB\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $defaultEnGbLocaleId = $executeResult->fetchOne();

        $count = $matheusGontijoSystemConfigHistoryRouteRepository->getCount(
            $defaultEnGbLocaleId,
            'Default',
            [
                'configuration_key' => '',
                'configuration_value_old' => '',
                'configuration_value_new' => '',
                'sales_channel_name' => '',
                'username' => '',
                'created_at' => '',
            ]
        );

        static::assertSame(100, $count);
    }

    public function testCountWithFilters(): void
    {
        $matheusGontijoSystemConfigHistoryRouteRepository = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryRouteRepository::class
        );

        \assert($matheusGontijoSystemConfigHistoryRouteRepository instanceof MatheusGontijoSystemConfigHistoryRouteRepository);  // phpcs:ignore

        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['LOWER(HEX(id)) AS id']);
        $qb->from('locale');
        $qb->where('code = \'en-GB\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $defaultEnGbLocaleId = $executeResult->fetchOne();

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
                'configuration_key' => 'foo.bar.enabled3',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
                'username' => 'matheus.gontijo',
                'created_at' => '2022-01-01 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('ce8942b6a5da4d04a43f8f9c1acf8629'),
                'configuration_key' => 'foo.bar.enabled2',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'username' => 'matheus.gontijo',
                'created_at' => '2022-01-02 00:00:00.000',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
                'configuration_key' => 'foo.bar.enabled1',
                'configuration_value_old' => '{"_value": "123"}',
                'configuration_value_new' => '{"_value": "456"}',
                'sales_channel_id' => null,
                'username' => 'aaa.john',
                'created_at' => '2022-01-03 00:00:00.000',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $count = $matheusGontijoSystemConfigHistoryRouteRepository->getCount(
            $defaultEnGbLocaleId,
            'Default',
            [
                'configuration_key' => 'foo.bar.enabled',
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'sales_channel_name' => null,
                'username' => null,
                'created_at' => null,
            ]
        );

        static::assertSame(3, $count);
    }

    public function testLongConfigurationValues(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $longString = <<<'TEXT'
        What is Lorem Ipsum? Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lore
        m Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer to
        ok a galley of type and scrambled it to make a type specimen book. It has survived not only five cen
        turies, but also the leap into electronic typesetting, remaining essentially unchanged. It was popul
        arised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more re
        cently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. Why 
        do we use it? It is a long established fact that a reader will be distracted by the readable content
         of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less 
        normal distribution of letters, as opposed to using 'Content here, content here', making it look lik
        e readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as the
        ir default model text, and a search for 'lorem ipsum' will uncover many web sites still in their inf
        ancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (inj
        ected humour and the like).
        TEXT;

        $longString = str_replace(\PHP_EOL, '', $longString);

        $row = [
            'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => sprintf('{"_value": "%s"}', $longString),
            'configuration_value_new' => sprintf('{"_value": "%s"}', $longString),
            'sales_channel_id' => null,
            'username' => 'aaa.john',
            'created_at' => '2022-01-03 00:00:00.000',
            'updated_at' => null,
        ];

        $connection->insert('matheus_gontijo_system_config_history', $row);

        $rows = $this->getRows(['configuration_key' => 'foo.bar.enabled1'], 'configuration_key');

        static::assertCount(1, $rows);

        $longString = <<<'TEXT'
        What is Lorem Ipsum? Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lore
        m Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer to
        ok a galley of type and scrambled it to make a type specimen book. It has survived not only five cen
        turies, but also the leap into electronic typesetting, remaining essentially unchanged. It was popul
        arised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more re
        cently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. Why 
         (...)
        TEXT;

        $longString = str_replace(\PHP_EOL, '', $longString);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => $longString,
            'configuration_value_new' => $longString,
            'sales_channel_name' => 'Default',
            'username' => 'aaa.john',
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);
    }

    public function testShortConfigurationValues(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $longString = <<<'TEXT'
        What is Lorem Ipsum? Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lore
        m Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer to
        ok a galley of type and scrambled it to make a type specimen book. It has survived not only five cen
        turies, but also the leap into electronic typesetting, remaining essentially unchanged. It was popul
        arised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more re
        cently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. Why 
        TEXT;

        $longString = str_replace(\PHP_EOL, '', $longString);

        $row = [
            'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => sprintf('{"_value": "%s"}', $longString),
            'configuration_value_new' => sprintf('{"_value": "%s"}', $longString),
            'sales_channel_id' => null,
            'username' => 'aaa.john',
            'created_at' => '2022-01-03 00:00:00.000',
            'updated_at' => null,
        ];

        $connection->insert('matheus_gontijo_system_config_history', $row);

        $rows = $this->getRows(['configuration_key' => 'foo.bar.enabled1'], 'configuration_key');

        static::assertCount(1, $rows);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => $longString,
            'configuration_value_new' => $longString,
            'sales_channel_name' => 'Default',
            'username' => 'aaa.john',
            'created_at' => '2022-01-03 00:00:00.000',
        ], $rows[0]);
    }

    public function testGetMatheusGontijoSystemConfigHistory(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $connection->insert('matheus_gontijo_system_config_history', [
            'id' => Uuid::fromHexToBytes('fc162568816f4c2c8940d24d66d9c305'),
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => '{"_value": 123}',
            'configuration_value_new' => '{"_value": 456}',
            'sales_channel_id' => null,
            'created_at' => '2022-01-01 00:00:00.000',
            'updated_at' => null,
        ]);

        $matheusGontijoSystemConfigHistoryRouteRepository = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryRouteRepository::class
        );

        \assert($matheusGontijoSystemConfigHistoryRouteRepository instanceof MatheusGontijoSystemConfigHistoryRouteRepository); // phpcs:ignore

        $user = $matheusGontijoSystemConfigHistoryRouteRepository->getMatheusGontijoSystemConfigHistory(
            'fc162568816f4c2c8940d24d66d9c305'
        );

        $createdAt = \DateTimeImmutable::createFromFormat(
            Defaults::STORAGE_DATE_TIME_FORMAT,
            '2022-01-01 00:00:00.000'
        );

        static::assertSame('fc162568816f4c2c8940d24d66d9c305', $user->getId());
        static::assertSame('foo.bar.enabled3', $user->getConfigurationKey());
        static::assertSame(['_value' => 123], $user->getConfigurationValueOld());
        static::assertSame(['_value' => 456], $user->getConfigurationValueNew());
        static::assertNull($user->getSalesChannelId());
        static::assertEquals($createdAt, $user->getCreatedAt());
        static::assertNull($user->getUpdatedAt());
    }

    private function populateTableWithData(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = include __DIR__ . '/fixture/matheus-gontijo-system-config-history-records.php';

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return array<int, mixed>
     */
    private function getRows(array $filters, string $sortBy): array
    {
        $matheusGontijoSystemConfigHistoryRouteRepository = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryRouteRepository::class
        );

        \assert($matheusGontijoSystemConfigHistoryRouteRepository instanceof MatheusGontijoSystemConfigHistoryRouteRepository); // phpcs:ignore

        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['LOWER(HEX(id)) AS id']);
        $qb->from('locale');
        $qb->where('code = \'en-GB\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $defaultEnGbLocaleId = $executeResult->fetchOne();

        $defaultFilters = [
            'configuration_key' => '',
            'configuration_value_old' => '',
            'configuration_value_new' => '',
            'sales_channel_name' => '',
            'username' => '',
            'created_at' => '',
        ];

        foreach ($defaultFilters as $defaultFilterKey => $defaultFilter) {
            if (\array_key_exists($defaultFilterKey, $filters)) {
                continue;
            }

            $filters[$defaultFilterKey] = $defaultFilter;
        }

        return $matheusGontijoSystemConfigHistoryRouteRepository->getRows(
            $defaultEnGbLocaleId,
            'Default',
            $filters,
            $sortBy,
            'ASC',
            1,
            100
        );
    }

    // @TODO: ADD TEST CURRENTLOCALE VS DEFAULTLOCALE... ADD PORTUGUESE LOCALE INSTEAD OF ENGLISH AND GERMAN
}
