<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Model;

use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationRepository;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Shopware\Core\Framework\DataAbstractionLayer\VersionManager;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Aaa
{
    /**
     * @var array<string, UserEntity>
     */
    private array $users = [];

    private SystemConfigRepositoryDecorationRepository $systemConfigRepositoryDecorationRepository;

    private RequestStateRegistry $requestStateRegistry;

    public function __construct(
        SystemConfigRepositoryDecorationRepository $systemConfigRepositoryDecorationRepository,
        RequestStateRegistry $requestStateRegistry
    ) {
        $this->systemConfigRepositoryDecorationRepository = $systemConfigRepositoryDecorationRepository;
        $this->requestStateRegistry = $requestStateRegistry;
    }

    public function process(\Closure $call, array $data, Context $context): EntityWrittenContainerEvent
    {
        // @TODO: add test passing empty array... make sure it throws an exception
        // @TODO: add enabled/disabled

        $oldSystemConfigs = $this->getFreshSystemConfigData($data);

        $result = $call($data, $context);

        $newSystemConfigs = $this->getFreshSystemConfigData($data);

        if ($oldSystemConfigs === $newSystemConfigs) {
            return $result;
        }

        $this->insertHistoryData($oldSystemConfigs, $newSystemConfigs);

        return $result;
    }

    private function insertHistoryData(array $oldSystemConfigs, array $newSystemConfigs): void
    {
        $data = [];

        foreach ($oldSystemConfigs as $key => $oldSystemConfig) {
            if (!isset($newSystemConfigs[$key])) {
                continue;
            }

            if ($oldSystemConfig === $newSystemConfigs[$key]) {
                continue;
            }

            $historyData = [
                'id' => $this->systemConfigRepositoryDecorationRepository->generateId(),
                'configurationKey' => $oldSystemConfig['configurationKey'],
                'salesChannelId' => $oldSystemConfig['salesChannelId'],
                'configurationValueOld' => $oldSystemConfig['configurationValue'],
                'configurationValueNew' => $newSystemConfigs[$key]['configurationValue'],
            ];

            if (!$this->hasAdminRequest()) {
                $data[] = $historyData;
                continue;
            }

            $historyData = $this->addUserDataUserData($historyData);
            $historyData = $this->addUserDataRequestData($historyData);

            $data[] = $historyData;
        }

        $this->systemConfigRepositoryDecorationRepository->insert($data);
    }

    private function getFreshSystemConfigData(array $data): array
    {
        $systemConfigs = [];

        foreach ($data as $key => $element) {
            if (!isset($element['configurationKey'])) {
                continue;
            }

            $salesChannelId = $element['salesChannelId'] ?? null;
            $configurationValue = $this->systemConfigRepositoryDecorationRepository->getValue(
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

    private function hasAdminRequest(): bool {
        $request = $this->requestStateRegistry->getRequest();

        if ($request === null) {
            return false;
        }

        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);

        if (!$context instanceof Context) {
            return false;
        }

        $source = $context->getSource();

        return $source instanceof AdminApiSource;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function addUserDataUserData(array $data): array
    {
        $request = $this->requestStateRegistry->getRequest();

        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);
        \assert($context instanceof Context);

        $source = $context->getSource();
        \assert($source instanceof AdminApiSource);

        $userId = $source->getUserId();
        \assert(\is_string($userId));

        $user = $this->loadUser($userId);

        $data['username'] = $user->getUsername();

        $data['userData']['user'] = [
            'username' => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'active' => $user->getActive(),
        ];

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function addUserDataRequestData(array $data): array
    {
        $request = $this->requestStateRegistry->getRequest();

        $data['userData']['request'] = [
            'HTTP_USER_AGENT' => $request->server->get('HTTP_USER_AGENT'),
            'SERVER_ADDR' => $request->server->get('SERVER_ADDR'),
        ];

        return $data;
    }

    private function loadUser(string $id): UserEntity
    {
        $userIds = array_keys($this->users);

        if (\in_array($id, $userIds, true)) {
            return $this->users[$id];
        }

        $this->users[$id] = $this->systemConfigRepositoryDecorationRepository->loadUser($id);

        return $this->users[$id];
    }
}
