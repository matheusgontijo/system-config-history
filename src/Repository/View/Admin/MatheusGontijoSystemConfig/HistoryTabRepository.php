<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository\View\Admin\MatheusGontijoSystemConfig;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;

class HistoryTabRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getSalesChannelNameCurrentLocale(string $localeId, string $salesChannelId): ?string
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(['sct.name']);
        $qb->from('sales_channel_translation', 'sct');

        $qb->leftJoin('sct', 'language', 'la', 'la.id = sct.language_id');
        $qb->innerJoin('la', 'locale', 'lo', 'lo.id = la.locale_id AND lo.id = :locale_id');

        $qb->where('sct.sales_channel_id = :sales_channel_id');

        $qb->setParameter(':sales_channel_id', Uuid::fromHexToBytes($salesChannelId));
        $qb->setParameter(':locale_id', Uuid::fromHexToBytes($localeId));

        $executeResult = $qb->execute();

        $name = $executeResult->fetchOne();

        return $name;
    }

    public function getSalesChannelNameDefaultLocale(string $salesChannelId): ?string
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(['sct.name']);
        $qb->from('sales_channel_translation', 'sct');
        $qb->where('language_id = :language_id');

        $qb->setParameter(':language_id', Uuid::fromHexToBytes($salesChannelId));

        $executeResult = $qb->execute();

        $name = $executeResult->fetchOne();

        return $name;
    }
}
