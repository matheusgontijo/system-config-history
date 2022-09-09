<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\System\MatheusGontijoSystemConfigHistory;

use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryCollection; //phpcs:ignore
use PHPUnit\Framework\TestCase;

class MatheusGontijoSystemConfigHistoryCollectionUnitTest extends TestCase
{
    public function testGetApiAlias(): void
    {
        $matheusGontijoSystemConfigHistoryCollection = new MatheusGontijoSystemConfigHistoryCollection();

        static::assertSame(
            'matheus_gontijo_system_config_history_collection',
            $matheusGontijoSystemConfigHistoryCollection->getApiAlias()
        );
    }
}
