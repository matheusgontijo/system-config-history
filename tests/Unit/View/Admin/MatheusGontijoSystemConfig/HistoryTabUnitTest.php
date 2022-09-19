<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\View\Admin\MatheusGontijoSystemConfig;

use MatheusGontijo\SystemConfigHistory\Repository\View\Admin\MatheusGontijoSystemConfig\HistoryTabRepository;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use MatheusGontijo\SystemConfigHistory\View\Admin\MatheusGontijoSystemConfig\HistoryTab;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;

class HistoryTabUnitTest extends TestCase
{
    public function testFormatModalDataWithDefaultSalesChannelName(): void
    {
        $historyTabRepository = $this->createMock(HistoryTabRepository::class);

        $historyTabRepository->expects(static::never())
            ->method('getSalesChannelNameCurrentLocale');

        $historyTabRepository->expects(static::never())
            ->method('getSalesChannelNameDefaultLocale');

        $entity = new MatheusGontijoSystemConfigHistoryEntity();

        $createdAt = \DateTime::createFromFormat(Defaults::STORAGE_DATE_TIME_FORMAT, '2017-08-31 00:00:00.000');
        \assert($createdAt instanceof \DateTime);

        $entity->setId('5b22b58b37e04199b5219c752bc316fb');
        $entity->setConfigurationKey('aaa.bbb.ccc');
        $entity->setConfigurationValueOld(['_value' => false]);
        $entity->setConfigurationValueNew(null);
        $entity->setUsername('mgontijo');
        $entity->setCreatedAt($createdAt);

        $historyTab = new HistoryTab($historyTabRepository);

        $formatModalDataExpected = [
            'configuration_key' => 'aaa.bbb.ccc',
            'configuration_value_old' => false,
            'configuration_value_old_type' => 'boolean',
            'configuration_value_new' => null,
            'configuration_value_new_type' => 'null',
            'sales_channel_name' => 'Default',
            'username' => 'mgontijo',
            'modified_at' => '2017-08-31 00:00:00.000',
        ];

        $formatModalDataActual = $historyTab->formatModalData(
            'd5ba7fefd64643078eee81535a01c3bb',
            'Default',
            $entity
        );

        static::assertSame($formatModalDataExpected, $formatModalDataActual);
    }

    public function testFormatModalDataWithSalesChannelNameCurrentLocale(): void
    {
        $historyTabRepository = $this->createMock(HistoryTabRepository::class);

        $historyTabRepository->expects(static::once())
            ->method('getSalesChannelNameCurrentLocale')
            ->withConsecutive(['d5ba7fefd64643078eee81535a01c3bb', TestDefaults::SALES_CHANNEL_ID_ENGLISH])
            ->willReturn('Canal de Vendas Inglês');

        $historyTabRepository->expects(static::never())
            ->method('getSalesChannelNameDefaultLocale');

        $entity = new MatheusGontijoSystemConfigHistoryEntity();

        $createdAt = \DateTime::createFromFormat(Defaults::STORAGE_DATE_TIME_FORMAT, '2017-08-31 00:00:00.000');
        \assert($createdAt instanceof \DateTime);

        $entity->setId('5b22b58b37e04199b5219c752bc316fb');
        $entity->setConfigurationKey('aaa.bbb.ccc');
        $entity->setConfigurationValueOld(['_value' => false]);
        $entity->setConfigurationValueNew(null);
        $entity->setSalesChannelId(TestDefaults::SALES_CHANNEL_ID_ENGLISH);
        $entity->setUsername('mgontijo');
        $entity->setCreatedAt($createdAt);

        $historyTab = new HistoryTab($historyTabRepository);

        $formatModalDataExpected = [
            'configuration_key' => 'aaa.bbb.ccc',
            'configuration_value_old' => false,
            'configuration_value_old_type' => 'boolean',
            'configuration_value_new' => null,
            'configuration_value_new_type' => 'null',
            'sales_channel_name' => 'Canal de Vendas Inglês',
            'username' => 'mgontijo',
            'modified_at' => '2017-08-31 00:00:00.000',
        ];

        $formatModalDataActual = $historyTab->formatModalData(
            'd5ba7fefd64643078eee81535a01c3bb',
            'Default',
            $entity
        );

        static::assertSame($formatModalDataExpected, $formatModalDataActual);
    }

    public function testFormatModalDataWithSalesChannelNameDefaultLocale(): void
    {
        $historyTabRepository = $this->createMock(HistoryTabRepository::class);

        $historyTabRepository->expects(static::once())
            ->method('getSalesChannelNameCurrentLocale')
            ->withConsecutive(['d5ba7fefd64643078eee81535a01c3bb', TestDefaults::SALES_CHANNEL_ID_ENGLISH])
            ->willReturn(null);

        $historyTabRepository->expects(static::once())
            ->method('getSalesChannelNameDefaultLocale')
            ->willReturn('English Sales Channel');

        $entity = new MatheusGontijoSystemConfigHistoryEntity();

        $createdAt = \DateTime::createFromFormat(Defaults::STORAGE_DATE_TIME_FORMAT, '2017-08-31 00:00:00.000');
        \assert($createdAt instanceof \DateTime);

        $entity->setId('5b22b58b37e04199b5219c752bc316fb');
        $entity->setConfigurationKey('aaa.bbb.ccc');
        $entity->setConfigurationValueOld(['_value' => false]);
        $entity->setConfigurationValueNew(null);
        $entity->setSalesChannelId(TestDefaults::SALES_CHANNEL_ID_ENGLISH);
        $entity->setUsername('mgontijo');
        $entity->setCreatedAt($createdAt);

        $historyTab = new HistoryTab($historyTabRepository);

        $formatModalDataExpected = [
            'configuration_key' => 'aaa.bbb.ccc',
            'configuration_value_old' => false,
            'configuration_value_old_type' => 'boolean',
            'configuration_value_new' => null,
            'configuration_value_new_type' => 'null',
            'sales_channel_name' => 'English Sales Channel',
            'username' => 'mgontijo',
            'modified_at' => '2017-08-31 00:00:00.000',
        ];

        $formatModalDataActual = $historyTab->formatModalData(
            'd5ba7fefd64643078eee81535a01c3bb',
            'Default',
            $entity
        );

        static::assertSame($formatModalDataExpected, $formatModalDataActual);
    }

//    /**
//     * @param array<string, mixed> $data
//     *
//     * @dataProvider setGetDataProvider
//     */
//    public function testFormatModalDataDifferentTypes(array $data): void
//    {
//        // @TODO: TEST DIFFERENT TYPES
//    }
//
//    /**
//     * @return array<int, mixed>
//     */
//    public function setGetDataProvider(): array
//    {
//        $data = [];
//
//        $types = [
//            'null' => [
//                'type' => null,
//                'label' => 'null',
//            ],
//            'array' => [
//                'type' => ['aaa'],
//                'label' => 'array',
//            ],
//            'int' => [
//                'type' => 99,
//                'label' => 'integer',
//            ],
//            'float' => [
//                'type' => 77.77,
//                'label' => 'float',
//            ],
//            'bool' => [
//                'type' => true,
//                'label' => 'boolean',
//            ],
//            'string' => [
//                'type' => 'foo bar',
//                'label' => 'string',
//            ],
//        ];
//
//        foreach ($types as $type) {
//            $data[] = [
//                [
//                    'id' => '5b22b58b37e04199b5219c752bc316fb',
//                    'configuration_key' => 'aaa.bbb.ccc',
//                    'configuration_value_old' => $type['type'],
//                    'configuration_value_old_type' => $type['label'],
//                    'configuration_value_new' => $type['type'],
//                    'configuration_value_new_type' => $type['label'],
//                    'sales_channel_name' => 'Default',
//                    'username' => 'mgontijo',
//                    'modified_at' => '2017-08-31 00:00:00.000',
//                ],
//            ];
//        }
//
//        return $data;
//    }
}
