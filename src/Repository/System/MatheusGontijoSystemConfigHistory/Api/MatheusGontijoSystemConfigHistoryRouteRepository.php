<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;

class MatheusGontijoSystemConfigHistoryRouteRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function getCount(string $localeId, string $defaultSalesChannelName, array $filters): int
    {
        $subQuery = $this->buildSubQuery($defaultSalesChannelName, $filters);

        $qb = $this->connection->createQueryBuilder();

        $qb->select(['COUNT(*)']);

        $subQueryString = sprintf('(%s)', $subQuery->getSQL());

        $qb->from($subQueryString, 'subquery');

        $qb = $this->setQueryBuilderParameters($qb, $localeId, $defaultSalesChannelName, $filters);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        return (int) $executeResult->fetchOne();
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
        $subQuery = $this->buildSubQuery($defaultSalesChannelName, $filters);

        $qb = $this->connection->createQueryBuilder();

        $qb->select([
            'subquery.id',
            'subquery.configuration_key',
            'subquery.configuration_value_old',
            'subquery.configuration_value_new',
            'subquery.sales_channel_name',
            'subquery.username',
            'subquery.user_data',
            'subquery.action_type',
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
        // @TODO: ADD DEFAULT SALES CHANNEL NAME.. in case missing translation

        $qb->setParameter(':locale_id', Uuid::fromHexToBytes($localeId));
        $qb->setParameter(':default_sales_channel_name', $defaultSalesChannelName);

        if ($filters['configuration_key'] !== null && $filters['configuration_key'] !== '') {
            $qb->setParameter(':configuration_key', '%' . $filters['configuration_key'] . '%');
        }

        if ($filters['configuration_value_old'] !== null && $filters['configuration_value_old'] !== '') {
            $qb->setParameter(':configuration_value_old', '%' . $filters['configuration_value_old'] . '%');
        }

        if ($filters['configuration_value_new'] !== null && $filters['configuration_value_new'] !== '') {
            $qb->setParameter(':configuration_value_new', '%' . $filters['configuration_value_new'] . '%');
        }

        if ($filters['sales_channel_name'] !== null && $filters['sales_channel_name'] !== '') {
            $qb->andWhere('subquery.sales_channel_name LIKE :sales_channel_name');
            $qb->setParameter(':sales_channel_name', '%' . $filters['sales_channel_name'] . '%');
        }

        if ($filters['username'] !== null && $filters['username'] !== '') {
            $qb->setParameter(':username', '%' . $filters['username'] . '%');
        }

        return $qb;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function buildSubQuery(string $defaultSalesChannelName, array $filters): QueryBuilder
    {
        $subQb = $this->connection->createQueryBuilder();

        $subQb->select(['la.id']);
        $subQb->from('language', 'la');
        $subQb->innerJoin('la', 'locale', 'lo', 'lo.id = la.locale_id and lo.id = :locale_id');

        $qb = $this->connection->createQueryBuilder();

        /*
         * @TODO: ADD COLUMN: "TYPE"
         */

        $qb->select([
            'mgsch.id',
            'mgsch.configuration_key',
            'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_old, "$._value")) as configuration_value_old',
            'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_new, "$._value")) as configuration_value_new',
            'IF(sct.name IS NOT NULL, sct.name, :default_sales_channel_name) AS sales_channel_name',
            'mgsch.username',
            'mgsch.user_data',
            'mgsch.action_type',
            'mgsch.created_at',
        ]);

        $qb->from('matheus_gontijo_system_config_history', 'mgsch');

        $leftJoinCondition = sprintf(
            'sct.sales_channel_id = mgsch.sales_channel_id AND sct.language_id = (%s)',
            $subQb->getSQL()
        );

        $qb->leftJoin('mgsch', 'sales_channel_translation', 'sct', $leftJoinCondition);

        if ($filters['configuration_key'] !== null && $filters['configuration_key'] !== '') {
            $qb->andWhere('mgsch.configuration_key LIKE :configuration_key');
        }

        if ($filters['configuration_value_old'] !== null && $filters['configuration_value_old'] !== '') {
            $qb->andWhere(
                'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_old, "$._value")) LIKE :configuration_value_old'
            );
        }

        if ($filters['configuration_value_new'] !== null && $filters['configuration_value_new'] !== '') {
            $qb->andWhere(
                'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_new, "$._value")) LIKE :configuration_value_new'
            );
        }

        if ($filters['username'] !== null && $filters['username'] !== '') {
            $qb->andWhere('mgsch.username LIKE :username');
        }

        return $qb;
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

            $row['id'] = Uuid::fromBytesToHex($row['id']);

            $rows[] = $row;
        }

        return $rows;
    }
}
