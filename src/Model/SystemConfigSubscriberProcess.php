<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Model;

use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigSubscriberProcessRepository;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\ChangeSet;
use Shopware\Core\Framework\Uuid\Uuid;
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
                $inserts[] = $this->processInsert($payload);

                continue;
            }

            $historyData = $this->processUpdate($changeSet);

            if ($historyData === null) {
                continue;
            }

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
            \assert($changeSet instanceof ChangeSet);

            $inserts[] = $this->processDelete($changeSet);
        }

        $this->systemConfigSubscriberProcessRepository->insert($inserts);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function processInsert(array $payload): array
    {
        $historyData = [
            'id' => $this->systemConfigSubscriberProcessRepository->generateId(),
            'configurationKey' => $payload['configurationKey'],
            'salesChannelId' => $payload['salesChannelId'],
            'configurationValueOld' => null,
            'configurationValueNew' => ['_value' => $payload['configurationValue']],
        ];

        return $this->addUser($historyData);
    }

    /**
     * @return array<string, mixed>
     */
    private function processUpdate(ChangeSet $changeSet): ?array
    {
        $afterData = $changeSet->getAfter(null);
        assert(is_array($afterData));

        if (!\array_key_exists('configuration_value', $afterData)) {
            return null;
        }

        $configurationValueBefore = null;

        $configurationValueBeforeRawValue = $changeSet->getBefore('configuration_value');

        if (\is_string($configurationValueBeforeRawValue)) {
            $configurationValueBefore = json_decode($configurationValueBeforeRawValue, true);
        }

        $configurationValueAfter = null;

        $configurationValueAfterRawValue = $changeSet->getAfter('configuration_value');

        if (\is_string($configurationValueAfterRawValue)) {
            $configurationValueAfter = json_decode($configurationValueAfterRawValue, true);
        }

        if ($configurationValueBefore === $configurationValueAfter) {
            return null;
        }

        $salesChannelId = $changeSet->getBefore('sales_channel_id');

        if (is_string($salesChannelId)) {
            $salesChannelId = Uuid::fromBytesToHex($salesChannelId);
        }

        $historyData = [
            'id' => $this->systemConfigSubscriberProcessRepository->generateId(),
            'configurationKey' => $changeSet->getBefore('configuration_key'),
            'salesChannelId' => $salesChannelId,
            'configurationValueOld' => $configurationValueBefore,
            'configurationValueNew' => $configurationValueAfter,
        ];

        return $this->addUser($historyData);
    }

    /**
     * @return array<string, mixed>
     */
    private function processDelete(ChangeSet $changeSet): array
    {
        $configurationValueBefore = null;

        $configurationValueBeforeRawValue = $changeSet->getBefore('configuration_value');

        if (\is_string($configurationValueBeforeRawValue)) {
            $configurationValueBefore = json_decode($configurationValueBeforeRawValue, true);
        }

        $salesChannelId = $changeSet->getBefore('sales_channel_id');

        if (is_string($salesChannelId)) {
            $salesChannelId = Uuid::fromBytesToHex($salesChannelId);
        }

        $historyData = [
            'id' => $this->systemConfigSubscriberProcessRepository->generateId(),
            'configurationKey' => $changeSet->getBefore('configuration_key'),
            'salesChannelId' => $salesChannelId,
            'configurationValueOld' => $configurationValueBefore,
            'configurationValueNew' => null,
        ];

        return $this->addUser($historyData);
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
        if (\array_key_exists($id, $this->users)) {
            return $this->users[$id];
        }

        $this->users[$id] = $this->systemConfigSubscriberProcessRepository->loadUser($id);

        return $this->users[$id];
    }
}
