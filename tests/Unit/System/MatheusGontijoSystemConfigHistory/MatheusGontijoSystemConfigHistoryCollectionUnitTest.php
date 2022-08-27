<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\System\MatheusGontijoSystemConfigHistory;

//phpcs:ignore
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryCollection;
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
