<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class MatheusGontijoSystemConfigHistoryCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'matheus_gontijo_system_config_history_collection';
    }

    protected function getExpectedClass(): string
    {
        return MatheusGontijoSystemConfigHistoryEntity::class;
    }
}
