<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\View\Admin\MatheusGontijoSystemConfig;

use DateTimeImmutable;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\View\Admin\MatheusGontijoSystemConfig\HistoryTab;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;

class HistoryTabUnitTest extends TestCase
{
    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider setGetDataProvider
     */
    public function testFormatModalData(array $data): void
    {
        $entity = new MatheusGontijoSystemConfigHistoryEntity();

        $createdAt = DateTimeImmutable::createFromFormat(Defaults::STORAGE_DATE_TIME_FORMAT, $data['modified_at']);

        $entity->setId($data['id']);
        $entity->setConfigurationKey($data['configuration_key']);

        if (isset($data['configuration_value_old'])) {
            $entity->setConfigurationValueOld(['_value' => $data['configuration_value_old']]);
        }

        if (isset($data['configuration_value_new'])) {
            $entity->setConfigurationValueNew(['_value' => $data['configuration_value_new']]);
        }

        $entity->setUsername($data['username']);
        $entity->setCreatedAt($createdAt);

        $historyTab = new HistoryTab();

        $formatModalDataExpected = [
            'configuration_key' => $data['configuration_key'],
            'configuration_value_old' => $data['configuration_value_old'],
            'configuration_value_old_type' => $data['configuration_value_old_type'],
            'configuration_value_new' => $data['configuration_value_new'],
            'configuration_value_new_type' => $data['configuration_value_new_type'],
            'sales_channel_name' => 'Default',
            'username' => 'mgontijo',
            'modified_at' => '2017-08-31 00:00:00.000',
        ];

        $formatModalDataActual = $historyTab->formatModalData('Default', $entity);

        static::assertSame($formatModalDataExpected, $formatModalDataActual);
    }

    /**
     * @return array<string, mixed>
     */
    public function setGetDataProvider(): array
    {
        $data = [];

        $types = [
            'null' => [
                'type' => null,
                'label' => 'null',
            ],
            'array' => [
                'type' => ['aaa'],
                'label' => 'array',
            ],
            'int' => [
                'type' => 99,
                'label' => 'integer',
            ],
            'float' => [
                'type' => 77.77,
                'label' => 'float',
            ],
            'bool' => [
                'type' => true,
                'label' => 'boolean',
            ],
            'string' => [
                'type' => 'foo bar',
                'label' => 'string',
            ],
        ];

        foreach ($types as $type) {
            $data[] = [
                [
                    'id' => '5b22b58b37e04199b5219c752bc316fb',
                    'configuration_key' => 'aaa.bbb.ccc',
                    'configuration_value_old' => $type['type'],
                    'configuration_value_old_type' => $type['label'],
                    'configuration_value_new' => $type['type'],
                    'configuration_value_new_type' => $type['label'],
                    'sales_channel_name' => 'Default',
                    'username' => 'mgontijo',
                    'modified_at' => '2017-08-31 00:00:00.000',
                ],
            ];
        }

        return $data;
    }
}
