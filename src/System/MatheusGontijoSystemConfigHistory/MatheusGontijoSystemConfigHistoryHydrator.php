<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityHydrator;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @psalm-suppress DeprecatedClass
 */
// phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
class MatheusGontijoSystemConfigHistoryHydrator extends EntityHydrator
{
    /**
     * @param array<mixed> $row
     */
    protected function assign(
        EntityDefinition $definition,
        Entity $entity,
        string $root,
        array $row,
        Context $context
    ): Entity {
        \assert($entity instanceof MatheusGontijoSystemConfigHistoryEntity);

        if (isset($row[$root . '.id'])) {
            $entity->setId(Uuid::fromBytesToHex($row[$root . '.id']));
        }

        if (isset($row[$root . '.configurationKey'])) {
            $entity->setConfigurationKey($row[$root . '.configurationKey']);
        }

        if (\array_key_exists($root . '.configurationValueOld', $row)) {
            $configurationValueOld = $definition->decode(
                'configurationValueOld',
                self::value($row, $root, 'configurationValueOld')
            );

            $entity->setConfigurationValueOld($configurationValueOld);
        }

        if (\array_key_exists($root . '.configurationValueNew', $row)) {
            $configurationValueNew = $definition->decode(
                'configurationValueNew',
                self::value($row, $root, 'configurationValueNew')
            );

            $entity->setConfigurationValueNew($configurationValueNew);
        }

        if (isset($row[$root . '.salesChannelId'])) {
            $entity->setSalesChannelId(Uuid::fromBytesToHex($row[$root . '.salesChannelId']));
        }

        if (isset($row[$root . '.username'])) {
            $entity->setUsername($row[$root . '.username']);
        }

        if (\array_key_exists($root . '.userData', $row)) {
            $userData = $definition->decode('userData', self::value($row, $root, 'userData'));
            $entity->setUserData($userData);
        }

        if (isset($row[$root . '.updatedAt'])) {
            $entity->setUpdatedAt(new \DateTimeImmutable($row[$root . '.updatedAt']));
        }

        if (isset($row[$root . '.createdAt'])) {
            $entity->setCreatedAt(new \DateTimeImmutable($row[$root . '.createdAt']));
        }

        return $entity;
    }
}
// phpcs:enable
