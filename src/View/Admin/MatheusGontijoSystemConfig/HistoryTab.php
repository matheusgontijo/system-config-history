<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\View\Admin\MatheusGontijoSystemConfig;

use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use Shopware\Core\Defaults;

class HistoryTab
{
    private ?MatheusGontijoSystemConfigHistoryEntity $matheusGontijoSystemConfigHistory = null;

    /**
     * @return array<string, mixed>
     */
    public function formatModalData(
        string $defaultSalesChannelName,
        MatheusGontijoSystemConfigHistoryEntity $matheusGontijoSystemConfigHistory
    ): array {
        $this->matheusGontijoSystemConfigHistory = $matheusGontijoSystemConfigHistory;

        $data = [];

        $data = $this->formatGeneralSection($defaultSalesChannelName, $data);

        return $this->formatUserSection($data);
    }

    /**
     * @param array<string, mixed> $rootData
     *
     * @return array<string, mixed>
     */
    private function formatGeneralSection(string $defaultSalesChannelName, array $rootData): array
    {
        assert($this->matheusGontijoSystemConfigHistory !== null);

        $data = [];

        $configurationKey = $this->matheusGontijoSystemConfigHistory->getConfigurationKey();
        assert($configurationKey !== null);

        $data['configurationKey'] = $configurationKey;

        $configurationValueOld = null;

        if ($this->matheusGontijoSystemConfigHistory->getConfigurationValueOld() !== null) {
            $configurationValueOld = $this->matheusGontijoSystemConfigHistory->getConfigurationValueOld()['_value'];
        }

        $data['configurationValueOld'] = $configurationValueOld;

        $data['configurationValueOldType'] = $this->typeOf($configurationValueOld);

        $configurationValueNew = null;

        if ($this->matheusGontijoSystemConfigHistory->getConfigurationValueNew() !== null) {
            $configurationValueNew = $this->matheusGontijoSystemConfigHistory->getConfigurationValueNew()['_value'];
        }

        $data['configurationValueNew'] = $configurationValueNew;

        $data['configurationValueNewType'] = $this->typeOf($configurationValueNew);

        $data['salesChannelName'] = $defaultSalesChannelName;

        $createdAt = $this->matheusGontijoSystemConfigHistory->getCreatedAt();
        assert($createdAt instanceof \DateTimeInterface);

        $data['modifiedAt'] = $createdAt->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $rootData['general'] = $data;

        return $rootData;
    }

    /**
     * @param array<string, mixed> $rootData
     *
     * @return array<string, mixed>
     */
    private function formatUserSection(array $rootData): array
    {
        assert($this->matheusGontijoSystemConfigHistory !== null);

        if ($this->matheusGontijoSystemConfigHistory->getUserData() === null) {
            return $rootData;
        }

        $rootData['user'] = $this->matheusGontijoSystemConfigHistory->getUserData()['user'];
        $rootData['request'] = $this->matheusGontijoSystemConfigHistory->getUserData()['request'];

        return $rootData;
    }

    /**
     * @param array<string, mixed>|bool|float|int|string|null $value
     */
    private function typeOf($value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (\is_string($value)) {
            return 'string';
        }

        if (\is_int($value)) {
            return 'integer';
        }

        if (\is_float($value)) {
            return 'float';
        }

        if (\is_bool($value)) {
            return 'boolean';
        }

        if (\is_array($value)) {
            return 'array';
        }
    }
}
