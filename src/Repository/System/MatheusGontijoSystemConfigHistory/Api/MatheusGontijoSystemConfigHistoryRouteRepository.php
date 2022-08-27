<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository\System\MatheusGontijoSystemConfigHistory\Api;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
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
    public function getCount(array $filters): int
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(['COUNT(*)']);

        $qb->from('matheus_gontijo_system_config_history', 'mgsch');

        $qb = $this->buildQueryFilters($qb, $filters);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        return (int) $executeResult->fetchOne();
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @return array<int, mixed>
     */
    public function getRows(array $filters, string $sortBy, string $sortDirection, int $page, int $limit): array
    {
        $qb = $this->buildQuery($filters, $sortBy, $sortDirection, $page, $limit);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $rows = $executeResult->fetchAllAssociative();

        return $this->normalizeData($rows);
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function buildQuery(
        array $filters,
        string $sortBy,
        string $sortDirection,
        int $page,
        int $limit
    ): QueryBuilder {
        $qb = $this->connection->createQueryBuilder();

        /*
         * @TODO: ADD COLUMN: "TYPE"
         */

        $qb->select([
            'mgsch.id',
            'mgsch.configuration_key',
            'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_old, "$._value")) as configuration_value_old',
            'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_new, "$._value")) as configuration_value_new',
            'mgsch.sales_channel_id',
            'mgsch.username',
            'mgsch.user_data',
            'mgsch.action_type',
            'mgsch.created_at',
        ]);

        $qb->from('matheus_gontijo_system_config_history', 'mgsch');

        $qb = $this->buildQueryFilters($qb, $filters);

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
    private function buildQueryFilters(QueryBuilder $qb, array $filters): QueryBuilder
    {
        if ($filters['configuration_key'] !== null && $filters['configuration_key'] !== '') {
            $qb->andWhere('mgsch.configuration_key LIKE :configuration_key');
            $qb->setParameter(':configuration_key', '%' . $filters['configuration_key'] . '%');
        }

        if ($filters['configuration_value_old'] !== null && $filters['configuration_value_old'] !== '') {
            $qb->andWhere(
                'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_old, "$._value")) LIKE :configuration_value_old'
            );
            $qb->setParameter(':configuration_value_old', '%' . $filters['configuration_value_old'] . '%');
        }

        if ($filters['configuration_value_new'] !== null && $filters['configuration_value_new'] !== '') {
            $qb->andWhere(
                'JSON_UNQUOTE(JSON_EXTRACT(mgsch.configuration_value_new, "$._value")) LIKE :configuration_value_new'
            );
            $qb->setParameter(':configuration_value_new', '%' . $filters['configuration_value_new'] . '%');
        }

        if ($filters['sales_channel_id'] !== null && $filters['sales_channel_id'] !== '') {
            $qb->andWhere('HEX(mgsch.sales_channel_id) LIKE :sales_channel_id');
            $qb->setParameter(':sales_channel_id', '%' . $filters['sales_channel_id'] . '%');
        }

        if ($filters['username'] !== null && $filters['username'] !== '') {
            $qb->andWhere('mgsch.username LIKE :username');
            $qb->setParameter(':username', '%' . $filters['username'] . '%');
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

            if ($row['sales_channel_id'] !== null) {
                $row['sales_channel_id'] = Uuid::fromBytesToHex($row['sales_channel_id']);
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
