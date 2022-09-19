<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Model;

use MatheusGontijo\SystemConfigHistory\Repository\Model\RevertSystemConfigRepository;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigEntity;

class RevertSystemConfigRepositoryIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testGenerateId(): void
    {
        $revertSystemConfigRepository = $this->getContainer()->get(RevertSystemConfigRepository::class);
        \assert($revertSystemConfigRepository instanceof RevertSystemConfigRepository);

        static::assertTrue(Uuid::isValid($revertSystemConfigRepository->generateId()));
    }

    public function testLoadMatheusGontijoSystemConfigHistory(): void
    {
        $matheusGontijoSystemConfigHistoryRepository = $this->getContainer()->get(
            'matheus_gontijo_system_config_history.repository'
        );

        \assert($matheusGontijoSystemConfigHistoryRepository instanceof EntityRepositoryInterface);

        $data = [
            [
                'id' => 'e6c7d2e5619842298ce4f7a58e99f626',
                'configurationKey' => 'aaa.bbb.ccc1',
                'configurationValueOld' => ['_value' => true],
                'configurationValueNew' => ['_value' => false],
                'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
                'username' => 'mgontijo',
            ],
            [
                'id' => '16b2b59393ee40f1b4657102c59ed3f4',
                'configurationKey' => 'aaa.bbb.ccc2',
                'configurationValueOld' => ['_value' => true],
                'configurationValueNew' => ['_value' => false],
                'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
                'username' => 'mgontijo',
            ],
        ];

        $matheusGontijoSystemConfigHistoryRepository->create($data, Context::createDefaultContext());

        $revertSystemConfigRepository = $this->getContainer()->get(RevertSystemConfigRepository::class);
        \assert($revertSystemConfigRepository instanceof RevertSystemConfigRepository);

        $matheusGontijoSystemConfigHistory = $revertSystemConfigRepository->loadMatheusGontijoSystemConfigHistory(
            'e6c7d2e5619842298ce4f7a58e99f626'
        );

        static::assertSame('e6c7d2e5619842298ce4f7a58e99f626', $matheusGontijoSystemConfigHistory->getId());
        static::assertSame('aaa.bbb.ccc1', $matheusGontijoSystemConfigHistory->getConfigurationKey());
        static::assertSame(['_value' => true], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => false], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame(
            TestDefaults::SALES_CHANNEL_ID_ENGLISH,
            $matheusGontijoSystemConfigHistory->getSalesChannelId()
        );
        static::assertSame('mgontijo', $matheusGontijoSystemConfigHistory->getUsername());
    }

    public function testLoadSystemConfig(): void
    {
        $systemConfigRepository = $this->getContainer()->get('system_config.repository');
        \assert($systemConfigRepository instanceof EntityRepositoryInterface);

        $data = [
            [
                'id' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                'configurationKey' => 'aaa.bbb.ccc',
                'configurationValue' => ['_value' => true],
                'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
            ],
            [
                'id' => 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
                'configurationKey' => 'ddd.eee.fff',
                'configurationValue' => ['_value' => true],
                'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
            ],
            [
                'id' => 'cccccccccccccccccccccccccccccccc',
                'configurationKey' => 'ddd.eee.fff',
                'configurationValue' => ['_value' => true],
                'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_GERMAN,
            ],
            [
                'id' => 'dddddddddddddddddddddddddddddddd',
                'configurationKey' => 'ggg.hhh.iii',
                'configurationValue' => ['_value' => true],
                'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
            ],
        ];

        $systemConfigRepository->create($data, Context::createDefaultContext());

        $revertSystemConfigRepository = $this->getContainer()->get(RevertSystemConfigRepository::class);
        \assert($revertSystemConfigRepository instanceof RevertSystemConfigRepository);

        $systemConfig = $revertSystemConfigRepository->loadSystemConfig(
            'ddd.eee.fff',
            TestDefaults::SALES_CHANNEL_ID_ENGLISH
        );
        \assert($systemConfig instanceof SystemConfigEntity);

        static::assertSame('bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', $systemConfig->getId());
        static::assertSame('ddd.eee.fff', $systemConfig->getConfigurationKey());
        static::assertSame(['_value' => true], $systemConfig->getConfigurationValue());
        static::assertSame(TestDefaults::SALES_CHANNEL_ID_ENGLISH, $systemConfig->getSalesChannelId());
    }

    public function testUpsertSystemConfig(): void
    {
        $matheusGontijoSystemConfigHistoryRepositoryMock = $this->createMock(EntityRepositoryInterface::class);
        $systemConfigRepositoryMock = $this->createMock(EntityRepositoryInterface::class);

        $systemConfigRepositoryMock->expects(static::exactly(1))
            ->method('upsert')
            ->withConsecutive(
                [
                    [
                        [
                            'id' => 'e6c7d2e5619842298ce4f7a58e99f626',
                            'configurationKey' => 'aaa.bbb.ccc',
                            'configurationValue' => ['_value' => true],
                            'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
                        ],
                    ],
                ]
            );

        $revertSystemConfigRepository = new RevertSystemConfigRepository(
            $matheusGontijoSystemConfigHistoryRepositoryMock,
            $systemConfigRepositoryMock
        );

        $revertSystemConfigRepository->upsertSystemConfig([
            'id' => 'e6c7d2e5619842298ce4f7a58e99f626',
            'configurationKey' => 'aaa.bbb.ccc',
            'configurationValue' => ['_value' => true],
            'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
        ]);
    }

    public function testDeleteSystemConfig(): void
    {
        $matheusGontijoSystemConfigHistoryRepositoryMock = $this->createMock(EntityRepositoryInterface::class);
        $systemConfigRepositoryMock = $this->createMock(EntityRepositoryInterface::class);

        $systemConfigRepositoryMock->expects(static::exactly(1))
            ->method('delete')
            ->withConsecutive(
                [
                    [
                        ['id' => 'e6c7d2e5619842298ce4f7a58e99f626'],
                    ],
                ]
            );

        $revertSystemConfigRepository = new RevertSystemConfigRepository(
            $matheusGontijoSystemConfigHistoryRepositoryMock,
            $systemConfigRepositoryMock
        );

        $revertSystemConfigRepository->deleteSystemConfig('e6c7d2e5619842298ce4f7a58e99f626');
    }
}
