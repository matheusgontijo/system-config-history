<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\OtherNamespaces\Shopware\Core\System\SystemConfig;

use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationProcessRepository;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SystemConfigServiceIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testHistory(): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);
        \assert($systemConfigService instanceof SystemConfigService);

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

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[1]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'aaa'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'bbb'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[2]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'bbb'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertNull($matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[3]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertNull($matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'ccc'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());
    }

    public function testGetValuesWithDifferentSalesChannels(): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);
        \assert($systemConfigService instanceof SystemConfigService);

        $systemConfigRepositoryDecorationProcessRepository = $this->getContainer()->get(
            SystemConfigRepositoryDecorationProcessRepository::class
        );

        \assert($systemConfigRepositoryDecorationProcessRepository instanceof SystemConfigRepositoryDecorationProcessRepository); // phpcs:ignore

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
}
