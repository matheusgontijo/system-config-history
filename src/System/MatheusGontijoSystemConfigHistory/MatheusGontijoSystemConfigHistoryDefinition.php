<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class MatheusGontijoSystemConfigHistoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'matheus_gontijo_system_config_history';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return MatheusGontijoSystemConfigHistoryEntity::class;
    }

    public function getCollectionClass(): string
    {
        return MatheusGontijoSystemConfigHistoryCollection::class;
    }

    public function since(): ?string
    {
        // @TODO: ADJUST IT
        return '6.0.0.0';
    }

    public function getHydratorClass(): string
    {
        return MatheusGontijoSystemConfigHistoryHydrator::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new StringField('configuration_key', 'configurationKey'))->addFlags(new ApiAware(), new Required()),
            (new JsonField('configuration_value_old', 'configurationValueOld'))->addFlags(new ApiAware()),
            (new JsonField('configuration_value_new', 'configurationValueNew'))->addFlags(new ApiAware()),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(
                new ApiAware()
            ),
            (new StringField('username', 'username'))->addFlags(new ApiAware()),
            (new JsonField('user_data', 'userData'))->addFlags(new ApiAware()),
        ]);
    }
}
