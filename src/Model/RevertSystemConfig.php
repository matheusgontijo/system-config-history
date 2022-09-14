<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Model;

use MatheusGontijo\SystemConfigHistory\Repository\Model\RevertSystemConfigRepository;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use Shopware\Core\System\SystemConfig\SystemConfigEntity;

class RevertSystemConfig
{
    private RevertSystemConfigRepository $revertSystemConfigRepository;

    public function __construct(RevertSystemConfigRepository $revertSystemConfigRepository)
    {
        $this->revertSystemConfigRepository = $revertSystemConfigRepository;
    }

    public function revert(string $matheusGontijoSystemConfigHistoryId, string $configurationValueType): void
    {
        $matheusGontijoSystemConfigHistory = $this->revertSystemConfigRepository->loadMatheusGontijoSystemConfigHistory(
            $matheusGontijoSystemConfigHistoryId
        );

        $value = $this->getValue($matheusGontijoSystemConfigHistory, $configurationValueType);

        $configurationKey = $matheusGontijoSystemConfigHistory->getConfigurationKey();
        \assert($configurationKey !== null);

        $salesChannelId = $matheusGontijoSystemConfigHistory->getSalesChannelId();
        \assert($salesChannelId !== null);

        $systemConfig = $this->revertSystemConfigRepository->loadSystemConfig($configurationKey, $salesChannelId);

        if ($value === null && $systemConfig === null) {
            return;
        }

        if ($value === null && $systemConfig instanceof SystemConfigEntity) {
            $this->revertSystemConfigRepository->deleteSystemConfig($systemConfig->getId());

            return;
        }

        $systemConfigId = $this->revertSystemConfigRepository->generateId();

        if ($systemConfig !== null) {
            $systemConfigId = $systemConfig->getId();
        }

        $data = [
            'id' => $systemConfigId,
            'configurationKey' => $matheusGontijoSystemConfigHistory->getConfigurationKey(),
            'configurationValue' => $value,
            'salesChannelId' => $matheusGontijoSystemConfigHistory->getSalesChannelId(),
        ];

        $this->revertSystemConfigRepository->upsertSystemConfig($data);
    }

    /**
     * @return array<string, mixed>|bool|float|int|string|null $value
     */
    private function getValue(
        MatheusGontijoSystemConfigHistoryEntity $matheusGontijoSystemConfigHistory,
        string $configurationValueType
    ) {
        $value = null;

        if ($configurationValueType === 'configuration_value_old') {
            $value = $matheusGontijoSystemConfigHistory->getConfigurationValueOld();
        }

        if ($configurationValueType === 'configuration_value_new') {
            $value = $matheusGontijoSystemConfigHistory->getConfigurationValueNew();
        }

        if (\is_array($value) && \array_key_exists('_value', $value)) {
            return $value['_value'];
        }

        return $value;
    }
}
