<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Repository\View\Admin\MatheusGontijoSystemConfig;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use MatheusGontijo\SystemConfigHistory\Repository\View\Admin\MatheusGontijoSystemConfig\HistoryTabRepository;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

class HistoryTabRepositoryIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testGetSalesChannelNameCurrentLocaleWithValue(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['LOWER(HEX(id))']);
        $qb->from('locale');
        $qb->where('code = \'pt-BR\'');

        $executeResult = $qb->execute();
        assert($executeResult instanceof Result);

        $localeId = $executeResult->fetchOne();

        $languageRepository = $this->getContainer()->get('language.repository');
        \assert($languageRepository instanceof EntityRepositoryInterface);

        $languageRepository->create([
            [
                'id' => 'ffffffffffffffffffffffffffffffff',
                'name' => 'Português',
                'localeId' => $localeId,
                'translationCodeId' => $localeId,
            ],
        ], Context::createDefaultContext());

        $salesChannelTranslationRepository = $this->getContainer()->get('sales_channel_translation.repository');
        \assert($salesChannelTranslationRepository instanceof EntityRepositoryInterface);

        $salesChannelTranslationRepository->create([
            [
                'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
                'languageId' => 'ffffffffffffffffffffffffffffffff',
                'name' => 'Canal de Vendas em Inglês',
                'localeId' => $localeId,
                'homeEnabled' => true,
            ],
        ], Context::createDefaultContext());

        $historyTabRepository = $this->getContainer()->get(HistoryTabRepository::class);
        \assert($historyTabRepository instanceof HistoryTabRepository);

        $salesChannelNameActual = $historyTabRepository->getSalesChannelNameCurrentLocale(
            $localeId,
            TestDefaults::SALES_CHANNEL_ID_ENGLISH
        );

        static::assertSame('Canal de Vendas em Inglês', $salesChannelNameActual);
    }

    public function testGetSalesChannelNameCurrentLocaleWithNullValue(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['LOWER(HEX(id))']);
        $qb->from('locale');
        $qb->where('code = \'pt-BR\'');

        $executeResult = $qb->execute();
        assert($executeResult instanceof Result);

        $localeId = $executeResult->fetchOne();

        $historyTabRepository = $this->getContainer()->get(HistoryTabRepository::class);
        \assert($historyTabRepository instanceof HistoryTabRepository);

        $salesChannelNameActual = $historyTabRepository->getSalesChannelNameCurrentLocale(
            $localeId,
            TestDefaults::SALES_CHANNEL_ID_ENGLISH
        );

        static::assertNull($salesChannelNameActual);
    }

    public function testGetSalesChannelNameDefaultLocale(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['id']);
        $qb->from('locale');
        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        assert($executeResult instanceof Result);

        $localeId = $executeResult->fetchOne();

        $connection->insert('language', [
            'id' => Uuid::fromHexToBytes('00000000000000000000000000000000'),
            'name' => 'Foobar',
            'locale_id' => $localeId,
            'translation_code_id' => $localeId,
            'created_at' => '2000-01-01 00:00:00.000',
        ]);

        $connection->insert('sales_channel_translation', [
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
            'language_id' => Uuid::fromHexToBytes('00000000000000000000000000000000'),
            'name' => 'Foobar',
            'home_enabled' => 1,
            'created_at' => '2000-01-01 00:00:00.000',
        ]);

        $connection->insert('sales_channel_translation', [
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_GERMAN),
            'language_id' => Uuid::fromHexToBytes('00000000000000000000000000000000'),
            'name' => 'Foobar',
            'home_enabled' => 1,
            'created_at' => '2000-01-01 00:00:00.000',
        ]);

        $historyTabRepository = $this->getContainer()->get(HistoryTabRepository::class);
        \assert($historyTabRepository instanceof HistoryTabRepository);

        $salesChannelNameActual = $historyTabRepository->getSalesChannelNameDefaultLocale(
            TestDefaults::SALES_CHANNEL_ID_ENGLISH
        );

        static::assertSame('English Sales Channel', $salesChannelNameActual);

        $salesChannelNameActual = $historyTabRepository->getSalesChannelNameDefaultLocale(
            TestDefaults::SALES_CHANNEL_ID_GERMAN
        );

        static::assertSame('German Sales Channel', $salesChannelNameActual);
    }
}
