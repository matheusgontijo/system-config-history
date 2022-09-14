<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository\Model;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigEntity;
use Shopware\Core\System\User\UserEntity;

class RevertSystemConfigRepository
{
    private EntityRepositoryInterface $matheusGontijoSystemConfigHistoryRepository;

    private EntityRepositoryInterface $systemConfigRepository;

    public function __construct(
        EntityRepositoryInterface $matheusGontijoSystemConfigHistoryRepository,
        EntityRepositoryInterface $systemConfigRepository
    ) {
        $this->matheusGontijoSystemConfigHistoryRepository = $matheusGontijoSystemConfigHistoryRepository;
        $this->systemConfigRepository = $systemConfigRepository;
    }

    public function generateId(): string
    {
        return Uuid::randomHex();
    }

    public function loadMatheusGontijoSystemConfigHistory(
        string $matheusGontijoSystemConfigHistoryId
    ): MatheusGontijoSystemConfigHistoryEntity {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $matheusGontijoSystemConfigHistoryId));

        $matheusGontijoSystemConfigHistory = $this->matheusGontijoSystemConfigHistoryRepository->search(
            $criteria,
            Context::createDefaultContext()
        )->first();

        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        return $matheusGontijoSystemConfigHistory;
    }

    public function loadSystemConfig(string $configurationKey, ?string $salesChannelId = null): ?SystemConfigEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('configurationKey', $configurationKey));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));

        $systemConfig = $this->systemConfigRepository->search($criteria, Context::createDefaultContext())->first();
        \assert($systemConfig === null || $systemConfig instanceof SystemConfigEntity);

        return $systemConfig;
    }

    public function upsertSystemConfig(array $data): void
    {
        $this->systemConfigRepository->upsert([$data], Context::createDefaultContext());
    }

    public function deleteSystemConfig(string $systemConfigId): void
    {
        $this->systemConfigRepository->delete([['id' => $systemConfigId]], Context::createDefaultContext());
    }
}
