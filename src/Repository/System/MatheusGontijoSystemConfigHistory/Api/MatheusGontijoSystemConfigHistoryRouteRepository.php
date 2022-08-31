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
    public function getCount(string $defaultSalesChannelName, array $filters): int
    {
        $subQuery = $this->buildSubQuery($defaultSalesChannelName, $filters);

        $qb = $this->connection->createQueryBuilder();

        $qb->select(['COUNT(*)']);

        $subQueryString = sprintf('(%s)', $subQuery->getSQL());

        $qb->from($subQueryString, 'subquery');

        $qb = $this->setQueryBuilderParameters($qb, $defaultSalesChannelName, $filters);

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
        string $defaultSalesChannelName,
        array $filters,
        string $sortBy,
        string $sortDirection,
        int $page,
        int $limit
    ): array {
        $qb = $this->buildQuery($defaultSalesChannelName, $filters, $sortBy, $sortDirection, $page, $limit);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $rows = $executeResult->fetchAllAssociative();

        return $this->normalizeData($rows);
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function buildQuery(
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

        $qb = $this->setQueryBuilderParameters($qb, $defaultSalesChannelName, $filters);

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
    private function setQueryBuilderParameters(QueryBuilder $qb, string $defaultSalesChannelName, array $filters): QueryBuilder
    {
        $qb->setParameter(':default_sales_channel_name', $defaultSalesChannelName);
        $qb->setParameter(':language_id', Uuid::fromHexToBytes('2fbb5fe2e29a4d70aa5854ce7ce3e20b'));

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

        $qb->leftJoin(
            'mgsch',
            'sales_channel_translation',
            'sct',
            'sct.sales_channel_id = mgsch.sales_channel_id AND sct.language_id = :language_id'
        );

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
