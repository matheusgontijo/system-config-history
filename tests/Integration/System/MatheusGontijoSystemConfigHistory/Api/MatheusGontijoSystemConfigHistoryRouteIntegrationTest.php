<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\External\Shopware\Core\System\SystemConfig;

use Doctrine\DBAL\Connection;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\AdminApiTestBehaviour;
use Shopware\Core\Framework\Test\TestCaseBase\AdminFunctionalTestBehaviour;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class MatheusGontijoSystemConfigHistoryRouteIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;
    use AdminApiTestBehaviour;
    use AdminFunctionalTestBehaviour;

    public function testMatheusGontijoSystemConfigHistoryRowsWithoutResults(): void
    {
        $this->getBrowser()->request(
            'POST',
            '/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/rows',
            [
                'filters' => [
                    'configuration_key' => '',
                    'configuration_value_old' => '',
                    'configuration_value_new' => '',
                    'sales_channel_name' => '',
                    'username' => '',
                    'created_at' => '',
                ],
                'sortBy' => 'created_at',
                'sortDirection' => 'DESC',
                'page' => 1,
                'limit' => 20,
                'defaultSalesChannelName' => 'Default',
                'localeCode' => 'en-GB',
            ]
        );

        $responseContentExpected = [
            'count' => 0,
            'rows' => [],
        ];

        $responseContentActual = json_decode($this->getBrowser()->getResponse()->getContent(), true);

        static::assertEquals($responseContentExpected, $responseContentActual);
    }

    public function testMatheusGontijoSystemConfigHistoryRowsWithResults(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $rows = [
            [
                'id' => Uuid::fromHexToBytes('4d2871498e7e4a4e91e13b8280b7d935'),
                'configuration_key' => 'aaa.bbb.ccc',
                'configuration_value_old' => '{"_value":"aaa"}',
                'configuration_value_new' => '{"_value":123}',
                'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
                'username' => 'mgontijo',
                'created_at' => '2022-09-15 19:03:54.162',
                'updated_at' => null,
            ],
            [
                'id' => Uuid::fromHexToBytes('bdea61a00a274440a71801c2eff6a797'),
                'configuration_key' => 'ccc.bbb.aaa',
                'configuration_value_old' => null,
                'configuration_value_new' => '{"_value":["aaa","zzz"]}',
                'sales_channel_id' => null,
                'username' => 'mgontijo',
                'created_at' => '2022-09-10 07:23:14.361',
                'updated_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $connection->insert('matheus_gontijo_system_config_history', $row);
        }

        $this->getBrowser()->request(
            'POST',
            '/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/rows',
            [
                'filters' => [
                    'configuration_key' => '',
                    'configuration_value_old' => '',
                    'configuration_value_new' => '',
                    'sales_channel_name' => '',
                    'username' => '',
                    'created_at' => '',
                ],
                'sortBy' => 'created_at',
                'sortDirection' => 'DESC',
                'page' => 1,
                'limit' => 20,
                'defaultSalesChannelName' => 'Default',
                'localeCode' => 'en-GB',
            ]
        );

        $responseContentExpected = [
            'count' => 2,
            'rows' => [
                [
                    'id' => '4d2871498e7e4a4e91e13b8280b7d935',
                    'configuration_key' => 'aaa.bbb.ccc',
                    'configuration_value_old' => 'aaa',
                    'configuration_value_new' => '123',
                    'sales_channel_name' => 'English Sales Channel',
                    'username' => 'mgontijo',
                    'created_at' => '2022-09-15 19:03:54.162',
                ],
                [
                    'id' => 'bdea61a00a274440a71801c2eff6a797',
                    'configuration_key' => 'ccc.bbb.aaa',
                    'configuration_value_old' => null,
                    'configuration_value_new' => '["aaa", "zzz"]',
                    'sales_channel_name' => 'Default',
                    'username' => 'mgontijo',
                    'created_at' => '2022-09-10 07:23:14.361',
                ],
            ],
        ];

        $responseContentActual = json_decode($this->getBrowser()->getResponse()->getContent(), true);

        static::assertEquals($responseContentExpected, $responseContentActual);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider matheusGontijoSystemConfigHistoryRowsWithResultsWithFiltersDataProvider
     */
    public function testMatheusGontijoSystemConfigHistoryRowsWithResultsWithFilters(
        string $dataProviderFilter,
        string $dataProviderFilterValue
    ): void {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $connection->insert('matheus_gontijo_system_config_history', [
            'id' => Uuid::fromHexToBytes('00000000000000000000000000000000'),
            'configuration_key' => 'zzz.ddd.eee',
            'configuration_value_old' => '{"_value":"bbb"}',
            'configuration_value_new' => '{"_value":456}',
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
            'username' => 'johndoe',
            'created_at' => '2022-01-01 19:03:54.162',
            'updated_at' => null,
        ]);

        $connection->insert('matheus_gontijo_system_config_history', [
            'id' => Uuid::fromHexToBytes('ffffffffffffffffffffffffffffffff'),
            'configuration_key' => 'aaa.bbb.ccc',
            'configuration_value_old' => '{"_value":"aaa"}',
            'configuration_value_new' => '{"_value":123}',
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
            'username' => 'mgontijo',
            'created_at' => '2022-09-14 19:03:54.162',
            'updated_at' => null,
        ]);

        $filters = [
            'configuration_key' => '',
            'configuration_value_old' => '',
            'configuration_value_new' => '',
            'sales_channel_name' => '',
            'username' => '',
            'created_at' => '',
        ];

        $filters[$dataProviderFilter] = $dataProviderFilterValue;

        $this->getBrowser()->request(
            'POST',
            '/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/rows',
            [
                'filters' => $filters,
                'sortBy' => 'created_at',
                'sortDirection' => 'DESC',
                'page' => 1,
                'limit' => 20,
                'defaultSalesChannelName' => 'Default',
                'localeCode' => 'en-GB',
            ]
        );

        $responseContentExpected = [
            'count' => 1,
            'rows' => [
                [
                    'id' => 'ffffffffffffffffffffffffffffffff',
                    'configuration_key' => 'aaa.bbb.ccc',
                    'configuration_value_old' => 'aaa',
                    'configuration_value_new' => '123',
                    'sales_channel_name' => 'English Sales Channel',
                    'username' => 'mgontijo',
                    'created_at' => '2022-09-14 19:03:54.162',
                ],
            ],
        ];

        $responseContentActual = json_decode($this->getBrowser()->getResponse()->getContent(), true);

        static::assertEquals($responseContentExpected, $responseContentActual);
    }

    public function matheusGontijoSystemConfigHistoryRowsWithResultsWithFiltersDataProvider(): array
    {
        return [
            [
                'configuration_key',
                '.bbb.',
            ],
            [
                'configuration_value_old',
                'a',
            ],
            [
                'configuration_value_new',
                '2',
            ],
            [
                'sales_channel_name',
                'glish',
            ],
            [
                'username',
                'tijo',
            ],
            [
                'created_at',
                '09-14',
            ],
        ];
    }

    public function testMatheusGontijoSystemConfigHistoryModalData(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $connection->insert('matheus_gontijo_system_config_history', [
            'id' => Uuid::fromHexToBytes('4d2871498e7e4a4e91e13b8280b7d935'),
            'configuration_key' => 'aaa.bbb.ccc',
            'configuration_value_old' => '{"_value":"aaa"}',
            'configuration_value_new' => '{"_value":123}',
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
            'username' => 'mgontijo',
            'created_at' => '2022-09-15 19:03:54.162',
            'updated_at' => null,
        ]);

        $this->getBrowser()->request(
            'POST',
            '/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/modal-data',
            [
                'modalId' => '4d2871498e7e4a4e91e13b8280b7d935',
                'defaultSalesChannelName' => 'Default',
                'localeCode' => 'en-GB',
            ]
        );

        $responseContentExpected = [
            'configuration_key' => 'aaa.bbb.ccc',
            'configuration_value_old' => 'aaa',
            'configuration_value_old_type' => 'string',
            'configuration_value_new' => 123,
            'configuration_value_new_type' => 'integer',
            'sales_channel_name' => 'English Sales Channel',
            'username' => 'mgontijo',
            'modified_at' => '2022-09-15 19:03:54.162',
        ];

        $responseContentActual = json_decode($this->getBrowser()->getResponse()->getContent(), true);

        static::assertEquals($responseContentExpected, $responseContentActual);
    }

    public function testMatheusGontijoSystemConfigHistoryRevertConfigurationValue(): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);
        \assert($systemConfigService instanceof SystemConfigService);

        $systemConfigService->set('my.systemConfig.aaaBbbCcc', 'foobar', TestDefaults::SALES_CHANNEL_ID_ENGLISH);

        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $connection->insert('matheus_gontijo_system_config_history', [
            'id' => Uuid::fromHexToBytes('4d2871498e7e4a4e91e13b8280b7d935'),
            'configuration_key' => 'my.systemConfig.aaaBbbCcc',
            'configuration_value_old' => null,
            'configuration_value_new' => '{"_value":123456}',
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
            'username' => 'mgontijo',
            'created_at' => '2022-09-15 19:03:54.162',
            'updated_at' => null,
        ]);

        $this->getBrowser()->request(
            'POST',
            '/api/_action/matheus-gontijo/matheus-gontijo-system-config-history/revert-configuration-value',
            [
                'matheusGontijoSystemConfigHistoryId' => '4d2871498e7e4a4e91e13b8280b7d935',
                'configurationValueType' => 'configuration_value_new',
            ]
        );

        $valueActual = $systemConfigService->get('my.systemConfig.aaaBbbCcc', TestDefaults::SALES_CHANNEL_ID_ENGLISH);

        static::assertSame(123456, $valueActual);
    }
}
