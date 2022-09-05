<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\External\Shopware\Core\System\SystemConfig;

use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationProcessRepository;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryCollection;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryDefinition;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryHydrator;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use DateTimeImmutable;

class SystemConfigServiceIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testHistory(): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);
        \assert($systemConfigService instanceof SystemConfigService);

//        $systemConfigService->set('matheusGontijo.systemConfigHistory.enabled', true);

        $systemConfigService->set('my.configuration.key', 'aaa');
        $systemConfigService->set('my.configuration.key', 'bbb');
        $systemConfigService->set('my.configuration.key', null);
        $systemConfigService->set('my.configuration.key', 'ccc');

        $matheusGontijoSystemConfigHistoryRepository = $this->getContainer()->get(
            'matheus_gontijo_system_config_history.repository'
        );
        \assert($matheusGontijoSystemConfigHistoryRepository instanceof EntityRepository);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('configurationKey', 'my.configuration.key'));
        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::ASCENDING));

        $searchResult = $matheusGontijoSystemConfigHistoryRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        static::assertSame(4, $searchResult->getTotal());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[0]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertNull($matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'aaa'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());
        static::assertNull($matheusGontijoSystemConfigHistory->getUserData());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[1]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'aaa'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'bbb'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());
        static::assertNull($matheusGontijoSystemConfigHistory->getUserData());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[2]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'bbb'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertNull($matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());
        static::assertNull($matheusGontijoSystemConfigHistory->getUserData());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[3]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertNull($matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'ccc'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());
        static::assertNull($matheusGontijoSystemConfigHistory->getUserData());
    }

    public function testGetValuesWithDifferentSalesChannels(): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);
        \assert($systemConfigService instanceof SystemConfigService);

        $systemConfigRepositoryDecorationProcessRepository = $this->getContainer()->get(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        \assert($systemConfigRepositoryDecorationProcessRepository
            instanceof SystemConfigRepositoryDecorationProcessRepository);

        $systemConfigService->set('my.custom.configKey1', 'default');
        $systemConfigService->set('my.custom.configKey1', 'English', TestDefaults::SALES_CHANNEL_ID_ENGLISH);
        $systemConfigService->set('my.custom.configKey1', 'German', TestDefaults::SALES_CHANNEL_ID_GERMAN);

        $systemConfigService->set('my.custom.configKey2', 'default');
        $systemConfigService->set('my.custom.configKey2', 'English', TestDefaults::SALES_CHANNEL_ID_ENGLISH);
        $systemConfigService->set('my.custom.configKey2', 'German', TestDefaults::SALES_CHANNEL_ID_GERMAN);

        $defaultValue = $systemConfigRepositoryDecorationProcessRepository->getValue('my.custom.configKey1');

        static::assertSame(['_value' => 'default'], $defaultValue);

        $englishValue = $systemConfigRepositoryDecorationProcessRepository->getValue(
            'my.custom.configKey1',
            TestDefaults::SALES_CHANNEL_ID_ENGLISH
        );

        static::assertSame(['_value' => 'English'], $englishValue);

        $germanValue = $systemConfigRepositoryDecorationProcessRepository->getValue(
            'my.custom.configKey1',
            TestDefaults::SALES_CHANNEL_ID_GERMAN
        );

        static::assertSame(['_value' => 'German'], $germanValue);

        $defaultValue = $systemConfigRepositoryDecorationProcessRepository->getValue('my.custom.configKey2');

        static::assertSame(['_value' => 'default'], $defaultValue);

        $englishValue = $systemConfigRepositoryDecorationProcessRepository->getValue(
            'my.custom.configKey2',
            TestDefaults::SALES_CHANNEL_ID_ENGLISH
        );

        static::assertSame(['_value' => 'English'], $englishValue);

        $germanValue = $systemConfigRepositoryDecorationProcessRepository->getValue(
            'my.custom.configKey2',
            TestDefaults::SALES_CHANNEL_ID_GERMAN
        );

        static::assertSame(['_value' => 'German'], $germanValue);
    }

    public function testMatheusGontijoSystemConfigHistoryHydrator(): void
    {
        $matheusGontijoSystemConfigHistoryCollection = new MatheusGontijoSystemConfigHistoryCollection();
        \assert($matheusGontijoSystemConfigHistoryCollection instanceof MatheusGontijoSystemConfigHistoryCollection);

        $entityClass = 'MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity';

        $matheusGontijoSystemConfigHistoryDefinition = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryDefinition::class
        );
        \assert($matheusGontijoSystemConfigHistoryDefinition instanceof MatheusGontijoSystemConfigHistoryDefinition);

        $rows = [[
            'matheus_gontijo_system_config_history.id' => Uuid::fromHexToBytes('136f2285f9e742ac85369726bb90c93f'),
            'matheus_gontijo_system_config_history.configurationKey' => 'my.configuration.key',
            'matheus_gontijo_system_config_history.configurationValueOld' => '{"_value": "aaa"}',
            'matheus_gontijo_system_config_history.configurationValueNew' => '{"_value": "bbb"}',
            'matheus_gontijo_system_config_history.salesChannelId' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
            'matheus_gontijo_system_config_history.username' => 'mgontijo',
            'matheus_gontijo_system_config_history.userData' => '{"foo": "bar"}',
            'matheus_gontijo_system_config_history.createdAt' => '2022-09-05 01:52:06.743',
            'matheus_gontijo_system_config_history.updatedAt' => '2022-09-05 01:57:08.119',
        ]];

        $root = 'matheus_gontijo_system_config_history';

        $context = Context::createDefaultContext();

        $partial = [];

        $matheusGontijoSystemConfigHistoryHydrator = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryHydrator::class
        );
        \assert($matheusGontijoSystemConfigHistoryHydrator instanceof MatheusGontijoSystemConfigHistoryHydrator);

        $matheusGontijoSystemConfigHistoryCollection = $matheusGontijoSystemConfigHistoryHydrator->hydrate(
            $matheusGontijoSystemConfigHistoryCollection,
            $entityClass,
            $matheusGontijoSystemConfigHistoryDefinition,
            $rows,
            $root,
            $context,
            $partial
        );

        \assert($matheusGontijoSystemConfigHistoryCollection instanceof MatheusGontijoSystemConfigHistoryCollection);

        static::assertCount(1, $matheusGontijoSystemConfigHistoryCollection->getElements());

        $matheusGontijoSystemConfigHistory = $matheusGontijoSystemConfigHistoryCollection->first();
        assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertEquals($matheusGontijoSystemConfigHistory->getId(), '136f2285f9e742ac85369726bb90c93f');
        static::assertEquals($matheusGontijoSystemConfigHistory->getConfigurationKey(), 'my.configuration.key');
        static::assertEquals($matheusGontijoSystemConfigHistory->getConfigurationValueOld(), ['_value' => 'aaa']);
        static::assertEquals($matheusGontijoSystemConfigHistory->getConfigurationValueNew(), ['_value' => 'bbb']);
        static::assertEquals($matheusGontijoSystemConfigHistory->getSalesChannelId(), 'd235f6b8ff854574bc4ef7ee5369b6e6');
        static::assertEquals($matheusGontijoSystemConfigHistory->getUsername(), 'mgontijo');
        static::assertEquals($matheusGontijoSystemConfigHistory->getUserData(), ['foo' => 'bar']);
        static::assertEquals($matheusGontijoSystemConfigHistory->getCreatedAt(), new DateTimeImmutable('2022-09-05 01:52:06.743'));
        static::assertEquals($matheusGontijoSystemConfigHistory->getUpdatedAt(), new DateTimeImmutable('2022-09-05 01:57:08.119'));
    }
}
