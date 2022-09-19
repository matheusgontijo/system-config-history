<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Repository\View\Admin\MatheusGontijoSystemConfig;

use Doctrine\DBAL\Connection;
use MatheusGontijo\SystemConfigHistory\Repository\View\Admin\MatheusGontijoSystemConfig\HistoryTabRepository;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class HistoryTabRepositoryIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testGetSalesChannelNameCurrentLocale(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();
        $qb->select(['LOWER(HEX(id))']);
        $qb->from('locale');
        $qb->where('code = \'pt-BR\'');
        $executeResult = $qb->execute();

        $localeId = $executeResult->fetchOne();

        $languageRepository = $this->getContainer()->get('language.repository');
        \assert($languageRepository instanceof EntityRepositoryInterface);

        $languageRepository->create([[
            'id' => '2f7fc1fc898346f497103d5cc083c1ed',
            'name' => 'Português',
            'localeId' => $localeId,
            'translationCodeId' => $localeId,
        ]], Context::createDefaultContext());

        $salesChannelTranslationRepository = $this->getContainer()->get('sales_channel_translation.repository');
        \assert($salesChannelTranslationRepository instanceof EntityRepositoryInterface);

        $salesChannelTranslationRepository->create([[
            'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
            'languageId' => '2f7fc1fc898346f497103d5cc083c1ed',
            'name' => 'Canal de Vendas em Inglês',
            'localeId' => $localeId,
            'homeEnabled' => true,
        ]], Context::createDefaultContext());

        $historyTabRepository = $this->getContainer()->get(HistoryTabRepository::class);
        \assert($historyTabRepository instanceof HistoryTabRepository);

        $salesChannelNameActual = $historyTabRepository->getSalesChannelNameCurrentLocale(
            $localeId,
            TestDefaults::SALES_CHANNEL_ID_ENGLISH
        );

        $this->assertSame('Canal de Vendas em Inglês', $salesChannelNameActual);
    }

    public function testGetSalesChannelNameDefaultLocale(): void
    {
        $historyTabRepository = $this->getContainer()->get(HistoryTabRepository::class);
        \assert($historyTabRepository instanceof HistoryTabRepository);

        $salesChannelNameActual = $historyTabRepository->getSalesChannelNameDefaultLocale(
            TestDefaults::SALES_CHANNEL_ID_ENGLISH
        );

        $this->assertSame('English Sales Channel', $salesChannelNameActual);

        $salesChannelNameActual = $historyTabRepository->getSalesChannelNameDefaultLocale(
            TestDefaults::SALES_CHANNEL_ID_GERMAN
        );

        $this->assertSame('German Sales Channel', $salesChannelNameActual);
    }
}
