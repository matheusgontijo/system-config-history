<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Model;

use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationProcessRepository;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;

class SystemConfigRepositoryDecorationProcess
{
    private ?bool $isEnabled = null;

    /**
     * @var array<string, UserEntity>
     */
    private array $users = [];

    private SystemConfigRepositoryDecorationProcessRepository $systemConfigRepositoryDecorationProcessRepository;

    private RequestStateRegistry $requestStateRegistry;

    public function __construct(
        SystemConfigRepositoryDecorationProcessRepository $systemConfigRepositoryDecorationProcessRepository,
        RequestStateRegistry $requestStateRegistry
    ) {
        $this->systemConfigRepositoryDecorationProcessRepository = $systemConfigRepositoryDecorationProcessRepository;
        $this->requestStateRegistry = $requestStateRegistry;
    }

    /**
     * @param array<mixed> $data
     */
    public function process(\Closure $call, array $data): EntityWrittenContainerEvent
    {
        if (!$this->isEnabled()) {
            return $call();
        }

        $data = $this->cleanUpData($data);

        $oldSystemConfigs = $this->getFreshSystemConfigData($data);

        $result = $call();

        $newSystemConfigs = $this->getFreshSystemConfigData($data);

        if ($oldSystemConfigs === $newSystemConfigs) {
            return $result;
        }

        $this->insertHistoryData($oldSystemConfigs, $newSystemConfigs);

        return $result;
    }

    private function isEnabled(): bool
    {
        if ($this->isEnabled !== null) {
            return $this->isEnabled;
        }

        $this->isEnabled = $this->systemConfigRepositoryDecorationProcessRepository->isEnabled();

        return $this->isEnabled;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    private function cleanUpData(array $data): array
    {
        $newData = [];

        foreach ($data as $element) {
            if (!isset($element['configurationKey'])) {
                continue;
            }

            $newData[] = $element;
        }

        return $newData;
    }

    /**
     * @param array<int, mixed> $oldSystemConfigs
     * @param array<int, mixed> $newSystemConfigs
     */
    private function insertHistoryData(array $oldSystemConfigs, array $newSystemConfigs): void
    {
        $data = [];

        foreach ($oldSystemConfigs as $key => $oldSystemConfig) {
            if ($oldSystemConfig === $newSystemConfigs[$key]) {
                continue;
            }

            $historyData = [
                'id' => $this->systemConfigRepositoryDecorationProcessRepository->generateId(),
                'configurationKey' => $oldSystemConfig['configurationKey'],
                'salesChannelId' => $oldSystemConfig['salesChannelId'],
                'configurationValueOld' => $oldSystemConfig['configurationValue'],
                'configurationValueNew' => $newSystemConfigs[$key]['configurationValue'],
            ];

            $historyData = $this->addUser($historyData);

            $data[] = $historyData;
        }

        $this->systemConfigRepositoryDecorationProcessRepository->insert($data);
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<int, mixed>
     */
    private function getFreshSystemConfigData(array $data): array
    {
        $systemConfigs = [];

        foreach ($data as $key => $element) {
            $salesChannelId = $element['salesChannelId'] ?? null;
            $configurationValue = $this->systemConfigRepositoryDecorationProcessRepository->getValue(
                $element['configurationKey'],
                $salesChannelId
            );

            $systemConfigs[$key] = [
                'configurationKey' => $element['configurationKey'],
                'salesChannelId' => $salesChannelId,
                'configurationValue' => $configurationValue,
            ];
        }

        return $systemConfigs;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function addUser(array $data): array
    {
        $request = $this->requestStateRegistry->getRequest();

        if (!$request instanceof Request) {
            return $data;
        }

        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);

        if (!$context instanceof Context) {
            return $data;
        }

        $source = $context->getSource();

        if (!$source instanceof AdminApiSource) {
            return $data;
        }

        $userId = $source->getUserId();
        \assert(\is_string($userId));

        $user = $this->loadUser($userId);

        $data['username'] = $user->getUsername();

        return $data;
    }

    private function loadUser(string $id): UserEntity
    {
        $userIds = array_keys($this->users);

        if (\in_array($id, $userIds, true)) {
            return $this->users[$id];
        }

        $this->users[$id] = $this->systemConfigRepositoryDecorationProcessRepository->loadUser($id);

        return $this->users[$id];
    }
}
