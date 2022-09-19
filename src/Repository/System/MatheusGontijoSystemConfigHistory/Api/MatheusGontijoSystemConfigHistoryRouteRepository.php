<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class MatheusGontijoSystemConfigHistoryRouteRepository
{
    private const MAX_CHARACTERS_PER_COLUMN = 600;

    private Connection $connection;

    private EntityRepositoryInterface $matheusGontijoSystemConfigHistoryRepository;

    public function __construct(
        Connection $connection,
        EntityRepositoryInterface $matheusGontijoSystemConfigHistoryRepository
    ) {
        $this->connection = $connection;
        $this->matheusGontijoSystemConfigHistoryRepository = $matheusGontijoSystemConfigHistoryRepository;
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function getCount(string $localeId, string $defaultSalesChannelName, array $filters): int
    {
        $subQuery = $this->buildSubQuery($filters);

        $qb = $this->connection->createQueryBuilder();

        $qb->select(['COUNT(*)']);

        $subQueryString = sprintf('(%s)', $subQuery->getSQL());

        $qb->from($subQueryString, 'subquery');

        $qb = $this->setQueryBuilderParameters($qb, $localeId, $defaultSalesChannelName, $filters);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $count = $executeResult->fetchOne();
        \assert(is_string($count));

        return (int) $count;
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return array<int, mixed>
     */
    public function getRows(
        string $localeId,
        string $defaultSalesChannelName,
        array $filters,
        string $sortBy,
        string $sortDirection,
        int $page,
        int $limit
    ): array {
        $qb = $this->buildQuery($localeId, $defaultSalesChannelName, $filters, $sortBy, $sortDirection, $page, $limit);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $rows = $executeResult->fetchAllAssociative();

        return $this->normalizeData($rows);
    }

    public function getMatheusGontijoSystemConfigHistory(string $modalId): MatheusGontijoSystemConfigHistoryEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $modalId));

        $matheusGontijoSystemConfigHistory = $this->matheusGontijoSystemConfigHistoryRepository->search(
            $criteria,
            Context::createDefaultContext()
        )->first();

        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        return $matheusGontijoSystemConfigHistory;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function buildQuery(
        string $localeId,
        string $defaultSalesChannelName,
        array $filters,
        string $sortBy,
        string $sortDirection,
        int $page,
        int $limit
    ): QueryBuilder {
        $subQuery = $this->buildSubQuery($filters);

        $qb = $this->connection->createQueryBuilder();

        $qb->select([
            'subquery.id',
            'subquery.configuration_key',
            'subquery.configuration_value_old',
            'subquery.configuration_value_new',
            'subquery.sales_channel_name',
            'subquery.username',
            'subquery.created_at',
        ]);

        $subQueryString = sprintf('(%s)', $subQuery->getSQL());

        $qb->from($subQueryString, 'subquery');

        $qb = $this->setQueryBuilderParameters($qb, $localeId, $defaultSalesChannelName, $filters);

        $qb->orderBy($sortBy, $sortDirection);
        $qb->addOrderBy('created_at', 'DESC');

        $offset = $limit * ($page - 1);

        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

        return $qb;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function setQueryBuilderParameters(
        QueryBuilder $qb,
        string $localeId,
        string $defaultSalesChannelName,
        array $filters
    ): QueryBuilder {
        $qb->setParameter(':current_locale_id', Uuid::fromHexToBytes($localeId));
        $qb->setParameter(':default_language_id', Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM));
        $qb->setParameter(':default_sales_channel_name', $defaultSalesChannelName);

        if ($filters['configuration_key'] !== '') {
            $qb->setParameter(':configuration_key', '%' . $filters['configuration_key'] . '%');
        }

        if ($filters['configuration_value_old'] !== '') {
            $qb->setParameter(':configuration_value_old', '%' . $filters['configuration_value_old'] . '%');
        }

        if ($filters['configuration_value_new'] !== '') {
            $qb->setParameter(':configuration_value_new', '%' . $filters['configuration_value_new'] . '%');
        }

        if ($filters['sales_channel_name'] !== '') {
            $qb->andWhere('subquery.sales_channel_name LIKE :sales_channel_name');
            $qb->setParameter(':sales_channel_name', '%' . $filters['sales_channel_name'] . '%');
        }

        if ($filters['username'] !== '') {
            $qb->setParameter(':username', '%' . $filters['username'] . '%');
        }

        if ($filters['created_at'] !== '') {
            $qb->setParameter(':created_at', '%' . $filters['created_at'] . '%');
        }

        return $qb;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function buildSubQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();

        $salesChannelNameAlias = sprintf(
            'IF(%s IS NOT NULL, COALESCE(%s, %s), %s) AS sales_channel_name',
            'mgsch.sales_channel_id',
            'sct_current_locale.name',
            'sct_default_language_system.name',
            ':default_sales_channel_name',
        );

        $qb->select([
            'LOWER(HEX(mgsch.id)) AS id',
            'mgsch.configuration_key',
            'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_old, "$._value")) AS configuration_value_old',
            'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_new, "$._value")) AS configuration_value_new',
            $salesChannelNameAlias,
            'mgsch.username',
            'mgsch.created_at',
        ]);

        $qb->from('matheus_gontijo_system_config_history', 'mgsch');

        $qb = $this->addLeftJoinSalesChannelNameCurrentLocale($qb);
        $qb = $this->addLeftJoinSalesChannelNameDefaultLocale($qb);

        if ($filters['configuration_key'] !== '') {
            $qb->andWhere('mgsch.configuration_key LIKE :configuration_key');
        }

        if ($filters['configuration_value_old'] !== '') {
            $like = 'CAST(JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_old, "$._value")) AS CHAR) '
                . 'LIKE :configuration_value_old';

            $qb->andWhere($like);
        }

        if ($filters['configuration_value_new'] !== '') {
            $like = 'CAST(JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_new, "$._value")) AS CHAR)'
                . 'LIKE :configuration_value_new';

            $qb->andWhere($like);
        }

        if ($filters['username'] !== '') {
            $qb->andWhere('mgsch.username LIKE :username');
        }

        if ($filters['created_at'] !== '') {
            $qb->andWhere('mgsch.created_at LIKE :created_at');
        }

        return $qb;
    }

    private function addLeftJoinSalesChannelNameCurrentLocale(QueryBuilder $rootQb): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(['la.id']);
        $qb->from('language', 'la');
        $qb->innerJoin('la', 'locale', 'lo', 'lo.id = la.locale_id AND lo.id = :current_locale_id');

        $leftJoinCondition = sprintf(
            'sct_current_locale.sales_channel_id = mgsch.sales_channel_id AND sct_current_locale.language_id = (%s)',
            $qb->getSQL()
        );

        $rootQb->leftJoin('mgsch', 'sales_channel_translation', 'sct_current_locale', $leftJoinCondition);

        return $rootQb;
    }

    private function addLeftJoinSalesChannelNameDefaultLocale(QueryBuilder $rootQb): QueryBuilder
    {
        $leftJoinCondition = 'sct_default_language_system.sales_channel_id = mgsch.sales_channel_id ' .
            'AND sct_default_language_system.language_id = :default_language_id';

        $rootQb->leftJoin('mgsch', 'sales_channel_translation', 'sct_default_language_system', $leftJoinCondition);

        return $rootQb;
    }

    /**
     * @param array<int, mixed> $unnormalizedRows
     *
     * @return array<int, mixed>
     */
    private function normalizeData(array $unnormalizedRows): array
    {
        $rows = [];

        foreach ($unnormalizedRows as $unnormalizedRow) {
            $row = $unnormalizedRow;

            if (
                \is_string($row['configuration_value_old'])
                && \strlen($row['configuration_value_old']) > self::MAX_CHARACTERS_PER_COLUMN
            ) {
                $shorterValue = substr($row['configuration_value_old'], 0, self::MAX_CHARACTERS_PER_COLUMN);
                $row['configuration_value_old'] = $shorterValue . ' (...)';
            }

            if (
                \is_string($row['configuration_value_new'])
                && \strlen($row['configuration_value_new']) > self::MAX_CHARACTERS_PER_COLUMN
            ) {
                $shorterValue = substr($row['configuration_value_new'], 0, self::MAX_CHARACTERS_PER_COLUMN);

                $row['configuration_value_new'] = $shorterValue . ' (...)';
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
