<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Model;

use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigSubscriberProcessRepository;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;

class SystemConfigSubscriberProcess
{
    private ?bool $isEnabled = null;

    /**
     * @var array<string, UserEntity>
     */
    private array $users = [];

    private SystemConfigSubscriberProcessRepository $systemConfigSubscriberProcessRepository;

    private RequestStateRegistry $requestStateRegistry;

    public function __construct(
        SystemConfigSubscriberProcessRepository $systemConfigSubscriberProcessRepository,
        RequestStateRegistry $requestStateRegistry
    ) {
        $this->systemConfigSubscriberProcessRepository = $systemConfigSubscriberProcessRepository;
        $this->requestStateRegistry = $requestStateRegistry;
    }

    public function processEntityWrittenEvent(EntityWrittenEvent $event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $inserts = [];

        foreach ($event->getWriteResults() as $result) {
            $changeSet = $result->getChangeSet();
            $payload = $result->getPayload();

            if ($changeSet === null) {
                $historyData = [
                    'id' => $this->systemConfigSubscriberProcessRepository->generateId(),
                    'configurationKey' => $payload['configurationKey'],
                    'salesChannelId' => $payload['salesChannelId'],
                    'configurationValueOld' => null,
                    'configurationValueNew' => ['_value' => $payload['configurationValue']],
                ];

                $historyData = $this->addUser($historyData);

                $inserts[] = $historyData;

                continue;
            }

            $configurationValueBefore = null;

            if ($changeSet->getBefore('configuration_value') !== null) {
                $configurationValueBefore = json_decode($changeSet->getBefore('configuration_value'), true);
            }

            $configurationValueAfter = null;

            if ($changeSet->getAfter('configuration_value') !== null) {
                $configurationValueAfter = json_decode($changeSet->getAfter('configuration_value'), true);
            }

            $historyData = [
                'id' => $this->systemConfigSubscriberProcessRepository->generateId(),
                'configurationKey' => $changeSet->getBefore('configuration_key'),
                'salesChannelId' => $changeSet->getBefore('sales_channel_id'),
                'configurationValueOld' => $configurationValueBefore,
                'configurationValueNew' => $configurationValueAfter,
            ];

            $historyData = $this->addUser($historyData);

            $inserts[] = $historyData;
        }

        $this->systemConfigSubscriberProcessRepository->insert($inserts);
    }

    public function processEntityDeletedEvent(EntityDeletedEvent $event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $inserts = [];

        foreach ($event->getWriteResults() as $result) {
            $changeSet = $result->getChangeSet();

            $configurationValueBefore = null;

            if ($changeSet->getBefore('configuration_value') !== null) {
                $configurationValueBefore = json_decode($changeSet->getBefore('configuration_value'), true);
            }

            $historyData = [
                'id' => $this->systemConfigSubscriberProcessRepository->generateId(),
                'configurationKey' => $changeSet->getBefore('configuration_key'),
                'salesChannelId' => $changeSet->getBefore('sales_channel_id'),
                'configurationValueOld' => $configurationValueBefore,
                'configurationValueNew' => null,
            ];

            $historyData = $this->addUser($historyData);

            $inserts[] = $historyData;
        }

        $this->systemConfigSubscriberProcessRepository->insert($inserts);
    }

    private function isEnabled(): bool
    {
        if ($this->isEnabled !== null) {
            return $this->isEnabled;
        }

        $this->isEnabled = $this->systemConfigSubscriberProcessRepository->isEnabled();

        return $this->isEnabled;
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

        $this->users[$id] = $this->systemConfigSubscriberProcessRepository->loadUser($id);

        return $this->users[$id];
    }
}
