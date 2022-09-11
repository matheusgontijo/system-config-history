<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\External\Shopware\Core\System\SystemConfig;

use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryCollection; // phpcs:ignore
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryDefinition; // phpcs:ignore
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryHydrator; // phpcs:ignore
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

class MatheusGontijoSystemConfigHistoryHydratorIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testMatheusGontijoSystemConfigHistoryHydrator(): void
    {
        $matheusGontijoSystemConfigHistoryCollection = new MatheusGontijoSystemConfigHistoryCollection();
        \assert($matheusGontijoSystemConfigHistoryCollection instanceof MatheusGontijoSystemConfigHistoryCollection);

        $entityClass = 'MatheusGontijo\SystemConfigHistory\System\\'
            . 'MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity';

        $matheusGontijoSystemConfigHistoryDefinition = $this->getContainer()->get(
            MatheusGontijoSystemConfigHistoryDefinition::class
        );
        \assert($matheusGontijoSystemConfigHistoryDefinition instanceof MatheusGontijoSystemConfigHistoryDefinition);

        $rows = [
            [
                'matheus_gontijo_system_config_history.id' => Uuid::fromHexToBytes('136f2285f9e742ac85369726bb90c93f'),
                'matheus_gontijo_system_config_history.configurationKey' => 'my.configuration.key',
                'matheus_gontijo_system_config_history.configurationValueOld' => '{"_value": "aaa"}',
                'matheus_gontijo_system_config_history.configurationValueNew' => '{"_value": "bbb"}',
                'matheus_gontijo_system_config_history.salesChannelId' => Uuid::fromHexToBytes(
                    TestDefaults::SALES_CHANNEL_ID_ENGLISH
                ),
                'matheus_gontijo_system_config_history.username' => 'mgontijo',
                'matheus_gontijo_system_config_history.createdAt' => '2022-09-05 01:52:06.743',
                'matheus_gontijo_system_config_history.updatedAt' => '2022-09-05 01:57:08.119',
            ],
        ];

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
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertEquals($matheusGontijoSystemConfigHistory->getId(), '136f2285f9e742ac85369726bb90c93f');
        static::assertEquals($matheusGontijoSystemConfigHistory->getConfigurationKey(), 'my.configuration.key');
        static::assertEquals($matheusGontijoSystemConfigHistory->getConfigurationValueOld(), ['_value' => 'aaa']);
        static::assertEquals($matheusGontijoSystemConfigHistory->getConfigurationValueNew(), ['_value' => 'bbb']);
        static::assertEquals(
            $matheusGontijoSystemConfigHistory->getSalesChannelId(),
            'd235f6b8ff854574bc4ef7ee5369b6e6'
        );
        static::assertEquals($matheusGontijoSystemConfigHistory->getUsername(), 'mgontijo');
        static::assertEquals(
            $matheusGontijoSystemConfigHistory->getCreatedAt(),
            new \DateTimeImmutable('2022-09-05 01:52:06.743')
        );
        static::assertEquals(
            $matheusGontijoSystemConfigHistory->getUpdatedAt(),
            new \DateTimeImmutable('2022-09-05 01:57:08.119')
        );
    }
}
