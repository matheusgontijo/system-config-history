<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\System\MatheusGontijoSystemConfigHistory;

use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryCollection; //phpcs:ignore
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use PHPUnit\Framework\TestCase;

class MatheusGontijoSystemConfigHistoryEntityUnitTest extends TestCase
{
    public function testEntity(): void
    {
        $entity = new MatheusGontijoSystemConfigHistoryEntity();

        $entity->setId('5b22b58b37e04199b5219c752bc316fb');
        static::assertSame('5b22b58b37e04199b5219c752bc316fb', $entity->getId());

        $entity->setConfigurationKey('aaa.bbb.ccc');
        static::assertSame('aaa.bbb.ccc', $entity->getConfigurationKey());

        $entity->setConfigurationValueOld(['_value' => 'aaa']);
        static::assertSame(['_value' => 'aaa'], $entity->getConfigurationValueOld());

        $entity->setConfigurationValueNew(['_value' => 'bbb']);
        static::assertSame(['_value' => 'bbb'], $entity->getConfigurationValueNew());

        $entity->setSalesChannelId('8cf509b1facb4aaab9659b8ffb0ce47f');
        static::assertSame('8cf509b1facb4aaab9659b8ffb0ce47f', $entity->getSalesChannelId());

        $entity->setUsername('mgontijo');
        static::assertSame('mgontijo', $entity->getUsername());
    }
}
