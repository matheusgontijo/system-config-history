<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MatheusGontijoSystemConfigHistoryEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $configurationKey = null;

    /**
     * @var array<mixed>|null
     */
    protected ?array $configurationValueOld = null;

    /**
     * @var array<mixed>|null
     */
    protected ?array $configurationValueNew = null;

    protected ?string $salesChannelId = null;

    protected ?string $username = null;

    /**
     * @var array<mixed>|null
     */
    protected ?array $userData = null;

    public function getConfigurationKey(): ?string
    {
        return $this->configurationKey;
    }

    public function setConfigurationKey(?string $configurationKey): void
    {
        $this->configurationKey = $configurationKey;
    }

    /**
     * @return array<mixed>|null
     */
    public function getConfigurationValueOld(): ?array
    {
        return $this->configurationValueOld;
    }

    /**
     * @param array<mixed>|null $configurationValueOld
     */
    public function setConfigurationValueOld(?array $configurationValueOld): void
    {
        $this->configurationValueOld = $configurationValueOld;
    }

    /**
     * @return array<mixed>|null
     */
    public function getConfigurationValueNew(): ?array
    {
        return $this->configurationValueNew;
    }

    /**
     * @param array<mixed>|null $configurationValueNew
     */
    public function setConfigurationValueNew(?array $configurationValueNew): void
    {
        $this->configurationValueNew = $configurationValueNew;
    }

    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(?string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return array<mixed>|null
     */
    public function getUserData(): ?array
    {
        return $this->userData;
    }

    /**
     * @param array<mixed>|null $userData
     */
    public function setUserData(?array $userData): void
    {
        $this->userData = $userData;
    }
}
