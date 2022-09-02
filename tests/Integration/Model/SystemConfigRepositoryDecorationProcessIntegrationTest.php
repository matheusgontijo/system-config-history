<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Model;

use MatheusGontijo\SystemConfigHistory\Repository\SystemConfigRepositoryDecoration;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SystemConfigRepositoryDecorationProcessIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testDecorationIsWorking(): void
    {
        $systemConfigRepository = $this->getContainer()->get('system_config.repository');
        \assert($systemConfigRepository instanceof EntityRepository);

        static::assertInstanceOf(SystemConfigRepositoryDecoration::class, $systemConfigRepository);
    }

    public function testSystemConfigServiceHistory(): void
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
}
