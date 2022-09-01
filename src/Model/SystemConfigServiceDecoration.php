<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Model;

use Doctrine\DBAL\Connection;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigServiceDecorationRepository;
// phpcs:ignore
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SystemConfig\AbstractSystemConfigLoader;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\System\SystemConfig\Util\ConfigReader;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SystemConfigServiceDecoration extends SystemConfigService
{
    /**
     * @deprecated kept only for backwards compatibility during testings. It should be removed on future versions.
     *
     * @var array<string, mixed>
     */
    private array $configs = [];

    /**
     * @var array<string, UserEntity>
     */
    private array $users = [];

    private SystemConfigService $systemConfigService;

    private SystemConfigServiceDecorationRepository $systemConfigServiceDecorationRepository;

    private RequestStateRegistry $requestStateRegistry;

    public function __construct(
        Connection $connection,
        EntityRepositoryInterface $systemConfigRepository,
        ConfigReader $configReader,
        AbstractSystemConfigLoader $loader,
        EventDispatcherInterface $eventDispatcher,
        SystemConfigService $systemConfigService,
        SystemConfigServiceDecorationRepository $systemConfigServiceDecorationRepository,
        RequestStateRegistry $requestStateRegistry
    ) {
        parent::__construct($connection, $systemConfigRepository, $configReader, $loader, $eventDispatcher);

        $this->systemConfigService = $systemConfigService;
        $this->systemConfigServiceDecorationRepository = $systemConfigServiceDecorationRepository;
        $this->requestStateRegistry = $requestStateRegistry;
    }

    /**
     * @param array<mixed>|bool|float|int|string|null $value
     */
    public function set(string $key, $value, ?string $salesChannelId = null): void
    {
        // @TODO: UNCOMMENT THIS
//        if ($this->systemConfigService->get('') === false) {
//            parent::set($key, $value, $salesChannelId);
//            return;
//        }

        $oldValue = $this->systemConfigServiceDecorationRepository->getValue($key, $salesChannelId);

        $this->systemConfigService->set($key, $value, $salesChannelId);

        $newValue = $this->systemConfigServiceDecorationRepository->getValue($key, $salesChannelId);

        if ($oldValue === $newValue) {
            return;
        }

        $request = $this->requestStateRegistry->getRequest();

        if ($this->hasAdminRequest()) {
            $this->insertWithAdminRequest($request, $key, $oldValue, $newValue, $salesChannelId);

            return;
        }

        $this->insertWithoutAdminRequest($key, $oldValue, $newValue, $salesChannelId);
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
     * @param array<mixed>|null $oldValue
     * @param array<mixed>|null $newValue
     */
    private function insertWithoutAdminRequest(
        string $key,
        ?array $oldValue,
        ?array $newValue,
        ?string $salesChannelId = null
    ): void {
        $data = [
            'configurationKey' => $key,
            'configurationValueOld' => $oldValue,
            'configurationValueNew' => $newValue,
            'salesChannelId' => $salesChannelId,
        ];

        $this->systemConfigServiceDecorationRepository->insert($data);
    }

    /**
     * @param array<mixed>|null $oldValue
     * @param array<mixed>|null $newValue
     */
    private function insertWithAdminRequest(
        Request $request,
        string $key,
        ?array $oldValue,
        ?array $newValue,
        ?string $salesChannelId = null
    ): void {
        $data = [
            'configurationKey' => $key,
            'configurationValueOld' => $oldValue,
            'configurationValueNew' => $newValue,
            'salesChannelId' => $salesChannelId,
        ];

        $data = $this->addUserDataUserData($data, $request);
        $data = $this->addUserDataRequestData($data, $request);

        $this->systemConfigServiceDecorationRepository->insert($data);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function addUserDataUserData(array $data, Request $request): array
    {
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
    private function addUserDataRequestData(array $data, Request $request): array
    {
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

        $this->users[$id] = $this->systemConfigServiceDecorationRepository->loadUser($id);

        return $this->users[$id];
    }
}
