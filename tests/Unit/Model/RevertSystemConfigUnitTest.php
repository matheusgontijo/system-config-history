<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Model;

use MatheusGontijo\SystemConfigHistory\Model\RevertSystemConfig;
use MatheusGontijo\SystemConfigHistory\Repository\Model\RevertSystemConfigRepository;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\SystemConfig\SystemConfigEntity;

class RevertSystemConfigUnitTest extends TestCase
{
    public function testRevertNullValueAndNoSystemConfig(): void
    {
        $revertSystemConfigRepositoryMock = $this->createMock(RevertSystemConfigRepository::class);

        $matheusGontijoSystemConfigHistory = new MatheusGontijoSystemConfigHistoryEntity();

        $matheusGontijoSystemConfigHistory->setConfigurationKey('aaa.bbb.ccc');
        $matheusGontijoSystemConfigHistory->setSalesChannelId('07bb9e4fddd048afa7845569b59ea07b');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadMatheusGontijoSystemConfigHistory')
            ->withConsecutive(['b424c12e1a5d405988436037b5a48713'])
            ->willReturnOnConsecutiveCalls($matheusGontijoSystemConfigHistory);

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadSystemConfig')
            ->withConsecutive(['aaa.bbb.ccc', '07bb9e4fddd048afa7845569b59ea07b'])
            ->willReturnOnConsecutiveCalls(null);

        $revertSystemConfigRepositoryMock->expects(static::never())
            ->method('deleteSystemConfig');

        $revertSystemConfigRepositoryMock->expects(static::never())
            ->method('generateId');

        $revertSystemConfigRepositoryMock->expects(static::never())
            ->method('upsertSystemConfig');

        $revertSystemConfig = new RevertSystemConfig($revertSystemConfigRepositoryMock);

        $revertSystemConfig->revert('b424c12e1a5d405988436037b5a48713', 'configuration_value_old');
    }

    public function testRevertNullValueAndWithSystemConfig(): void
    {
        $revertSystemConfigRepositoryMock = $this->createMock(RevertSystemConfigRepository::class);

        $matheusGontijoSystemConfigHistory = new MatheusGontijoSystemConfigHistoryEntity();

        $matheusGontijoSystemConfigHistory->setConfigurationKey('aaa.bbb.ccc');
        $matheusGontijoSystemConfigHistory->setSalesChannelId('07bb9e4fddd048afa7845569b59ea07b');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadMatheusGontijoSystemConfigHistory')
            ->withConsecutive(['b424c12e1a5d405988436037b5a48713'])
            ->willReturnOnConsecutiveCalls($matheusGontijoSystemConfigHistory);

        $systemConfig = new SystemConfigEntity();

        $systemConfig->setId('c559b0847759472dba8cefa85091f6aa');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadSystemConfig')
            ->withConsecutive(['aaa.bbb.ccc', '07bb9e4fddd048afa7845569b59ea07b'])
            ->willReturnOnConsecutiveCalls($systemConfig);

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('deleteSystemConfig')
            ->withConsecutive(['c559b0847759472dba8cefa85091f6aa']);

        $revertSystemConfigRepositoryMock->expects(static::never())
            ->method('generateId');

        $revertSystemConfigRepositoryMock->expects(static::never())
            ->method('upsertSystemConfig');

        $revertSystemConfig = new RevertSystemConfig($revertSystemConfigRepositoryMock);

        $revertSystemConfig->revert('b424c12e1a5d405988436037b5a48713', 'configuration_value_old');
    }

    public function testRevertWithValueAndNoSystemConfig(): void
    {
        $revertSystemConfigRepositoryMock = $this->createMock(RevertSystemConfigRepository::class);

        $matheusGontijoSystemConfigHistory = new MatheusGontijoSystemConfigHistoryEntity();

        $matheusGontijoSystemConfigHistory->setConfigurationKey('aaa.bbb.ccc');
        $matheusGontijoSystemConfigHistory->setConfigurationValueOld(['_value' => 'foobar']);
        $matheusGontijoSystemConfigHistory->setSalesChannelId('07bb9e4fddd048afa7845569b59ea07b');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadMatheusGontijoSystemConfigHistory')
            ->withConsecutive(['b424c12e1a5d405988436037b5a48713'])
            ->willReturnOnConsecutiveCalls($matheusGontijoSystemConfigHistory);

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadSystemConfig')
            ->withConsecutive(['aaa.bbb.ccc', '07bb9e4fddd048afa7845569b59ea07b'])
            ->willReturnOnConsecutiveCalls(null);

        $revertSystemConfigRepositoryMock->expects(static::never())
            ->method('deleteSystemConfig');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('32d2705709e24350aea83d2762dc0ea1');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('upsertSystemConfig')
            ->withConsecutive(
                [
                    [
                        'id' => '32d2705709e24350aea83d2762dc0ea1',
                        'configurationKey' => 'aaa.bbb.ccc',
                        'configurationValue' => 'foobar',
                        'salesChannelId' => '07bb9e4fddd048afa7845569b59ea07b',
                    ],
                ]
            );

        $revertSystemConfig = new RevertSystemConfig($revertSystemConfigRepositoryMock);

        $revertSystemConfig->revert('b424c12e1a5d405988436037b5a48713', 'configuration_value_old');
    }

    public function testRevertWithValueAndSystemConfig(): void
    {
        $revertSystemConfigRepositoryMock = $this->createMock(RevertSystemConfigRepository::class);

        $matheusGontijoSystemConfigHistory = new MatheusGontijoSystemConfigHistoryEntity();

        $matheusGontijoSystemConfigHistory->setConfigurationKey('aaa.bbb.ccc');
        $matheusGontijoSystemConfigHistory->setConfigurationValueOld(['_value' => 'foobar']);
        $matheusGontijoSystemConfigHistory->setSalesChannelId('07bb9e4fddd048afa7845569b59ea07b');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadMatheusGontijoSystemConfigHistory')
            ->withConsecutive(['b424c12e1a5d405988436037b5a48713'])
            ->willReturnOnConsecutiveCalls($matheusGontijoSystemConfigHistory);

        $systemConfig = new SystemConfigEntity();

        $systemConfig->setId('c559b0847759472dba8cefa85091f6aa');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadSystemConfig')
            ->withConsecutive(['aaa.bbb.ccc', '07bb9e4fddd048afa7845569b59ea07b'])
            ->willReturnOnConsecutiveCalls($systemConfig);

        $revertSystemConfigRepositoryMock->expects(static::never())
            ->method('deleteSystemConfig');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('32d2705709e24350aea83d2762dc0ea1');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('upsertSystemConfig')
            ->withConsecutive(
                [
                    [
                        'id' => 'c559b0847759472dba8cefa85091f6aa',
                        'configurationKey' => 'aaa.bbb.ccc',
                        'configurationValue' => 'foobar',
                        'salesChannelId' => '07bb9e4fddd048afa7845569b59ea07b',
                    ],
                ]
            );

        $revertSystemConfig = new RevertSystemConfig($revertSystemConfigRepositoryMock);

        $revertSystemConfig->revert('b424c12e1a5d405988436037b5a48713', 'configuration_value_old');
    }

    public function testRevertConfigurationValueNew(): void
    {
        $revertSystemConfigRepositoryMock = $this->createMock(RevertSystemConfigRepository::class);

        $matheusGontijoSystemConfigHistory = new MatheusGontijoSystemConfigHistoryEntity();

        $matheusGontijoSystemConfigHistory->setConfigurationKey('aaa.bbb.ccc');
        $matheusGontijoSystemConfigHistory->setConfigurationValueNew(['_value' => 'foobar']);
        $matheusGontijoSystemConfigHistory->setSalesChannelId('07bb9e4fddd048afa7845569b59ea07b');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadMatheusGontijoSystemConfigHistory')
            ->withConsecutive(['b424c12e1a5d405988436037b5a48713'])
            ->willReturnOnConsecutiveCalls($matheusGontijoSystemConfigHistory);

        $systemConfig = new SystemConfigEntity();

        $systemConfig->setId('c559b0847759472dba8cefa85091f6aa');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('loadSystemConfig')
            ->withConsecutive(['aaa.bbb.ccc', '07bb9e4fddd048afa7845569b59ea07b'])
            ->willReturnOnConsecutiveCalls($systemConfig);

        $revertSystemConfigRepositoryMock->expects(static::never())
            ->method('deleteSystemConfig');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('32d2705709e24350aea83d2762dc0ea1');

        $revertSystemConfigRepositoryMock->expects(static::exactly(1))
            ->method('upsertSystemConfig')
            ->withConsecutive(
                [
                    [
                        'id' => 'c559b0847759472dba8cefa85091f6aa',
                        'configurationKey' => 'aaa.bbb.ccc',
                        'configurationValue' => 'foobar',
                        'salesChannelId' => '07bb9e4fddd048afa7845569b59ea07b',
                    ],
                ]
            );

        $revertSystemConfig = new RevertSystemConfig($revertSystemConfigRepositoryMock);

        $revertSystemConfig->revert('b424c12e1a5d405988436037b5a48713', 'configuration_value_new');
    }
}
