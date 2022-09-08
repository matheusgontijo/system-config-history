<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\View\Admin\MatheusGontijoSystemConfig;

use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use Exception;
use Shopware\Core\Defaults;

class HistoryTab
{
    private ?MatheusGontijoSystemConfigHistoryEntity $matheusGontijoSystemConfigHistory = null;

    public function formatModalData(
        string $defaultSalesChannelName,
        MatheusGontijoSystemConfigHistoryEntity $matheusGontijoSystemConfigHistory
    ): array {
        $this->matheusGontijoSystemConfigHistory = $matheusGontijoSystemConfigHistory;

        $data = [];

        $data = $this->formatGeneralSection($defaultSalesChannelName, $data);
        $data = $this->formatUserSection($data);

        return $data;
    }

    private function formatGeneralSection(string $defaultSalesChannelName, array $rootData): array
    {
        $data = [];

        $data['configurationKey'] = $this->matheusGontijoSystemConfigHistory->getConfigurationKey();

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

        $data['modifiedAt'] = $this->matheusGontijoSystemConfigHistory->getCreatedAt()
            ->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $rootData['general'] = $data;

        return $rootData;
    }

    private function formatUserSection(array $rootData): array
    {
        if ($this->matheusGontijoSystemConfigHistory->getUserData() === null) {
            return $rootData;
        }

        $rootData['user'] = $this->matheusGontijoSystemConfigHistory->getUserData()['user'];
        $rootData['request'] = $this->matheusGontijoSystemConfigHistory->getUserData()['request'];

        return $rootData;
    }

    private function typeOf($value)
    {
        if ($value === null) {
            return 'null';
        }

        if (is_string($value)) {
            return 'string';
        }

        if (is_int($value)) {
            return 'integer';
        }

        if (is_float($value)) {
            return 'float';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_array($value)) {
            return 'array';
        }

        throw new Exception('Unknown type');
    }
}
