<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\System\MatheusGontijoSystemConfigHistory;

use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryCollection; // phpcs:ignore
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryDefinition; // phpcs:ignore
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryHydrator; // phpcs:ignore
use PHPUnit\Framework\TestCase;

class MatheusGontijoSystemConfigHistoryDefinitionUnitTest extends TestCase
{
    public function testGetEntityName(): void
    {
        $matheusGontijoSystemConfigHistoryDefinition = new MatheusGontijoSystemConfigHistoryDefinition();

        static::assertSame(
            'matheus_gontijo_system_config_history',
            $matheusGontijoSystemConfigHistoryDefinition->getEntityName()
        );
    }

    public function testGetEntityClass(): void
    {
        $matheusGontijoSystemConfigHistoryDefinition = new MatheusGontijoSystemConfigHistoryDefinition();

        static::assertSame(
            MatheusGontijoSystemConfigHistoryEntity::class,
            $matheusGontijoSystemConfigHistoryDefinition->getEntityClass()
        );
    }

    public function testGetCollectionClass(): void
    {
        $matheusGontijoSystemConfigHistoryDefinition = new MatheusGontijoSystemConfigHistoryDefinition();

        static::assertSame(
            MatheusGontijoSystemConfigHistoryCollection::class,
            $matheusGontijoSystemConfigHistoryDefinition->getCollectionClass()
        );
    }

    public function testSince(): void
    {
        $matheusGontijoSystemConfigHistoryDefinition = new MatheusGontijoSystemConfigHistoryDefinition();

        static::assertSame('6.4.0.0', $matheusGontijoSystemConfigHistoryDefinition->since());
    }

    public function testGetHydratorClass(): void
    {
        $matheusGontijoSystemConfigHistoryDefinition = new MatheusGontijoSystemConfigHistoryDefinition();

        static::assertSame(
            MatheusGontijoSystemConfigHistoryHydrator::class,
            $matheusGontijoSystemConfigHistoryDefinition->getHydratorClass()
        );
    }
}
