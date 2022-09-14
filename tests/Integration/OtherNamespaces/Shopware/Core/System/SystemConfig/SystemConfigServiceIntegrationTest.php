<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\OtherNamespaces\Shopware\Core\System\SystemConfig;

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

    public function testHistoryWithoutSalesChannel(): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);
        \assert($systemConfigService instanceof SystemConfigService);

        $systemConfigService->set('my.configuration.key', 'aaa');
        $systemConfigService->set('my.configuration.key', 'aaa');
        $systemConfigService->set('my.configuration.key', 'aaa');
        $systemConfigService->set('my.configuration.key', 'aaa');
        $systemConfigService->set('my.configuration.key', 'bbb');
        $systemConfigService->set('my.configuration.key', 'bbb');
        $systemConfigService->set('my.configuration.key', 'bbb');
        $systemConfigService->set('my.configuration.key', 'bbb');
        $systemConfigService->set('my.configuration.key', null);
        $systemConfigService->set('my.configuration.key', null);
        $systemConfigService->set('my.configuration.key', null);
        $systemConfigService->set('my.configuration.key', null);
        $systemConfigService->set('my.configuration.key', 'ccc');
        $systemConfigService->set('my.configuration.key', 'ccc');
        $systemConfigService->set('my.configuration.key', 'ccc');
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

    public function testHistoryWithDifferentSalesChannels(): void
    {
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);
        \assert($systemConfigService instanceof SystemConfigService);

        $systemConfigService->set('my.configuration.key', 'default1');
        $systemConfigService->set('my.configuration.key', 'default1');
        $systemConfigService->set('my.configuration.key', 'default1');
        $systemConfigService->set('my.configuration.key', 'default2');
        $systemConfigService->set('my.configuration.key', 'default3');

        $systemConfigService->set('my.configuration.key', 'english1', TestDefaults::SALES_CHANNEL_ID_ENGLISH);
        $systemConfigService->set('my.configuration.key', 'english1', TestDefaults::SALES_CHANNEL_ID_ENGLISH);
        $systemConfigService->set('my.configuration.key', 'english1', TestDefaults::SALES_CHANNEL_ID_ENGLISH);
        $systemConfigService->set('my.configuration.key', 'english2', TestDefaults::SALES_CHANNEL_ID_ENGLISH);
        $systemConfigService->set('my.configuration.key', 'english3', TestDefaults::SALES_CHANNEL_ID_ENGLISH);

        $systemConfigService->set('my.configuration.key', 'german1', TestDefaults::SALES_CHANNEL_ID_GERMAN);
        $systemConfigService->set('my.configuration.key', 'german1', TestDefaults::SALES_CHANNEL_ID_GERMAN);
        $systemConfigService->set('my.configuration.key', 'german1', TestDefaults::SALES_CHANNEL_ID_GERMAN);
        $systemConfigService->set('my.configuration.key', 'german2', TestDefaults::SALES_CHANNEL_ID_GERMAN);
        $systemConfigService->set('my.configuration.key', 'german3', TestDefaults::SALES_CHANNEL_ID_GERMAN);

        $matheusGontijoSystemConfigHistoryRepository = $this->getContainer()->get(
            'matheus_gontijo_system_config_history.repository'
        );
        \assert($matheusGontijoSystemConfigHistoryRepository instanceof EntityRepository);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('configurationKey', 'my.configuration.key'));
        $criteria->addFilter(new EqualsFilter('salesChannelId', null));
        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::ASCENDING));

        $searchResult = $matheusGontijoSystemConfigHistoryRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        static::assertSame(3, $searchResult->getTotal());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[0]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertNull($matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'default1'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[1]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'default1'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'default2'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[2]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'default2'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'default3'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertNull($matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('configurationKey', 'my.configuration.key'));
        $criteria->addFilter(new EqualsFilter('salesChannelId', TestDefaults::SALES_CHANNEL_ID_ENGLISH));
        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::ASCENDING));

        $searchResult = $matheusGontijoSystemConfigHistoryRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        static::assertSame(3, $searchResult->getTotal());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[0]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertNull($matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'english1'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame(
            TestDefaults::SALES_CHANNEL_ID_ENGLISH,
            $matheusGontijoSystemConfigHistory->getSalesChannelId()
        );
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[1]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'english1'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'english2'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame(
            TestDefaults::SALES_CHANNEL_ID_ENGLISH,
            $matheusGontijoSystemConfigHistory->getSalesChannelId()
        );
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[2]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'english2'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'english3'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame(
            TestDefaults::SALES_CHANNEL_ID_ENGLISH,
            $matheusGontijoSystemConfigHistory->getSalesChannelId()
        );
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('configurationKey', 'my.configuration.key'));
        $criteria->addFilter(new EqualsFilter('salesChannelId', TestDefaults::SALES_CHANNEL_ID_GERMAN));
        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::ASCENDING));

        $searchResult = $matheusGontijoSystemConfigHistoryRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        static::assertSame(3, $searchResult->getTotal());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[0]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertNull($matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'german1'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame(
            TestDefaults::SALES_CHANNEL_ID_GERMAN,
            $matheusGontijoSystemConfigHistory->getSalesChannelId()
        );
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[1]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'german1'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'german2'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame(
            TestDefaults::SALES_CHANNEL_ID_GERMAN,
            $matheusGontijoSystemConfigHistory->getSalesChannelId()
        );
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[2]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame('my.configuration.key', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => 'german2'], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => 'german3'], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame(
            TestDefaults::SALES_CHANNEL_ID_GERMAN,
            $matheusGontijoSystemConfigHistory->getSalesChannelId()
        );
        static::assertNull($matheusGontijoSystemConfigHistory->getUsername());
    }
}
