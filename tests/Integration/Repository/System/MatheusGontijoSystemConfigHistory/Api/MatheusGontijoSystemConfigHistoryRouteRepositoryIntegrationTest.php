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

        $rows = $this->getRows(['configuration_key' => 'foo.bar.enabled'], 'configuration_key');

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

        $rows = $this->getRows(['configuration_value_old' => 'mycustomvalue_'], 'configuration_value_old');

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

        $rows = $this->getRows(['configuration_value_new' => 'mycustomvalue_'], 'configuration_value_new');

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
            'sales_channel_name' => 'Default',
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

        $rows = $this->getRows(['username' => 'gontijo'], 'username');

        static::assertCount(2, $rows);

        static::assertSame([
            'id' => 'ce8942b6a5da4d04a43f8f9c1acf8629',
            'configuration_key' => 'foo.bar.enabled2',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'English Sales Channel',
            'username' => 'matheus.gontijo',
            'created_at' => '2022-01-02 00:00:00.000',
        ], $rows[0]);

        static::assertSame([
            'id' => 'fc162568816f4c2c8940d24d66d9c305',
            'configuration_key' => 'foo.bar.enabled3',
            'configuration_value_old' => '123',
            'configuration_value_new' => '456',
            'sales_channel_name' => 'German Sales Channel',
            'username' => 'matheus.gontijo',
            'created_at' => '2022-01-01 00:00:00.000',
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
        $qb->select(['id']);
        $qb->from('locale');
        $qb->where('code = \'de-DE\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $defaultEnGbLocaleIdBin = $executeResult->fetchOne();

        $deDeLocaleId = Uuid::fromBytesToHex($defaultEnGbLocaleIdBin);

        $filters = [
            'configuration_key' => 'foo.bar.enabled',
            'configuration_value_old' => null,
            'configuration_value_new' => null,
            'username' => null,
            'sales_channel_name' => 'standard',
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
        $qb->select(['id']);
        $qb->from('locale');
        $qb->where('code = \'en-GB\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $defaultEnGbLocaleIdBin = $executeResult->fetchOne();

        $defaultEnGbLocaleId = Uuid::fromBytesToHex($defaultEnGbLocaleIdBin);

        $count = $matheusGontijoSystemConfigHistoryRouteRepository->getCount(
            $defaultEnGbLocaleId,
            'Default',
            [
                'configuration_key' => null,
                'configuration_value_old' => null,
                'configuration_value_new' => null,
                'username' => null,
                'sales_channel_name' => null,
            ]
        );

        static::assertSame(101, $count);
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
        $qb->select(['id']);
        $qb->from('locale');
        $qb->where('code = \'en-GB\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $defaultEnGbLocaleIdBin = $executeResult->fetchOne();

        $defaultEnGbLocaleId = Uuid::fromBytesToHex($defaultEnGbLocaleIdBin);

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
                'username' => null,
                'sales_channel_name' => null,
            ]
        );

        static::assertSame(3, $count);
    }

    public function testLongConfigurationValues(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $longsString = <<<'TEXT'
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys st
        andard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a
         type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, rem
        aining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lor
        em Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of
        Lorem Ipsum. 
        TEXT;

        $longsString = str_replace(\PHP_EOL, '', $longsString);

        $longsString .= $longsString . $longsString . $longsString . $longsString;

        $row = [
            'id' => Uuid::fromHexToBytes('b424c12e1a5d405988436037b5a48713'),
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => sprintf('{"_value": "%s"}', $longsString),
            'configuration_value_new' => sprintf('{"_value": "%s"}', $longsString),
            'sales_channel_id' => null,
            'username' => 'aaa.john',
            'created_at' => '2022-01-03 00:00:00.000',
            'updated_at' => null,
        ];

        $connection->insert('matheus_gontijo_system_config_history', $row);

        $rows = $this->getRows(['configuration_key' => 'foo.bar.enabled1'], 'configuration_key');

        static::assertCount(1, $rows);

        $longsString = <<<'TEXT'
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys st
        andard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a
         type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, rem
        aining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lor
        em Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of
        Lorem Ipsum. Lorem Ipsum is simply dummy (...)
        TEXT;

        $longsString = str_replace(\PHP_EOL, '', $longsString);

        static::assertSame([
            'id' => 'b424c12e1a5d405988436037b5a48713',
            'configuration_key' => 'foo.bar.enabled1',
            'configuration_value_old' => $longsString,
            'configuration_value_new' => $longsString,
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
        static::assertSame(null, $user->getSalesChannelId());
        static::assertEquals($createdAt, $user->getCreatedAt());
        static::assertSame(null, $user->getUpdatedAt());
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
        $qb->select(['id']);
        $qb->from('locale');
        $qb->where('code = \'en-GB\'');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $defaultEnGbLocaleIdBin = $executeResult->fetchOne();

        $defaultEnGbLocaleId = Uuid::fromBytesToHex($defaultEnGbLocaleIdBin);

        $defaultFilters = [
            'configuration_key' => null,
            'configuration_value_old' => null,
            'configuration_value_new' => null,
            'username' => null,
            'sales_channel_name' => null,
        ];

        $filtersKeys = array_keys($filters);

        foreach ($defaultFilters as $defaultFilterKey => $defaultFilter) {
            if (\in_array($defaultFilterKey, $filtersKeys, true)) {
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
}
