<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Decoration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use MatheusGontijo\SystemConfigHistory\Repository\Decoration\SystemConfigRepositoryDecorationRepository;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationProcessRepository;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\System\User\UserEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class SystemConfigRepositoryDecorationRepositoryIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testSearch(): void
    {
        $systemConfigRepositoryDecorationRepository = $this->getContainer()->get(
            SystemConfigRepositoryDecorationRepository::class
        );
        \assert($systemConfigRepositoryDecorationRepository
            instanceof SystemConfigRepositoryDecorationRepository);

        $systemConfigRepository = $this->getContainer()->get('system_config.repository');
        \assert($systemConfigRepository instanceof EntityRepositoryInterface);

        $systemConfigRepository->create([
            [
                'id' => 'd3d2a03c66404c04b938fb0ccc59d1bb',
                'configurationKey' => 'my.custom.configKey1',
                'configurationValue' => 'aaa',
                'sales_channelId' => null,
            ],
            [
                'id' => '1815098dbf814e6885f677b4b2f34e9c',
                'configurationKey' => 'my.custom.configKey2',
                'configurationValue' => 'bbb',
                'sales_channelId' => null,
            ],
            [
                'id' => 'eb7b1d39f0c64874a0d7bb2d157955ea',
                'configurationKey' => 'my.custom.configKey3',
                'configurationValue' => 'ccc',
                'sales_channelId' => null,
            ],
        ], Context::createDefaultContext());

        $ids = [
            'd3d2a03c66404c04b938fb0ccc59d1bb',
            '1815098dbf814e6885f677b4b2f34e9c',
            'eb7b1d39f0c64874a0d7bb2d157955ea'
        ];

        $searchResult = $systemConfigRepositoryDecorationRepository->search($systemConfigRepository, $ids);

        static::assertCount(3, $searchResult);

        $systemConfig = $searchResult[0];
        \assert($systemConfig instanceof SystemConfigEntity);

        static::assertSame('d3d2a03c66404c04b938fb0ccc59d1bb', $systemConfig->getId());
        static::assertSame('my.custom.configKey1', $systemConfig->getConfigurationKey());
        static::assertSame('aaa', $systemConfig->getConfigurationValue());
        static::assertNull($systemConfig->getSalesChannelId());

        $systemConfig = $searchResult[1];
        \assert($systemConfig instanceof SystemConfigEntity);

        static::assertSame('1815098dbf814e6885f677b4b2f34e9c', $systemConfig->getId());
        static::assertSame('my.custom.configKey2', $systemConfig->getConfigurationKey());
        static::assertSame('bbb', $systemConfig->getConfigurationValue());
        static::assertNull($systemConfig->getSalesChannelId());

        $systemConfig = $searchResult[2];
        \assert($systemConfig instanceof SystemConfigEntity);

        static::assertSame('eb7b1d39f0c64874a0d7bb2d157955ea', $systemConfig->getId());
        static::assertSame('my.custom.configKey3', $systemConfig->getConfigurationKey());
        static::assertSame('ccc', $systemConfig->getConfigurationValue());
        static::assertNull($systemConfig->getSalesChannelId());
    }
}
