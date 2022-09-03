<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Model;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationProcessRepository;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\System\User\UserEntity;

class SystemConfigRepositoryDecorationProcessRepositoryIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testGetValueWithNonExistingValue(): void
    {
        $systemConfigRepositoryDecorationProcessRepository = $this->getContainer()->get(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        \assert($systemConfigRepositoryDecorationProcessRepository
            instanceof SystemConfigRepositoryDecorationProcessRepository);

        static::assertNull($systemConfigRepositoryDecorationProcessRepository->getValue('my.custom.configKey'));
    }

    /**
     * @param array<mixed>|bool|int|float|string $oldValue
     * @param array<mixed>|bool|int|float|string $newValue
     *
     * @dataProvider insertProvider
     */
    public function testInsert($oldValue, $newValue, ?string $salesChannelId): void
    {
        $systemConfigRepositoryDecorationProcessRepository = $this->getContainer()->get(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        \assert(
            $systemConfigRepositoryDecorationProcessRepository instanceof SystemConfigRepositoryDecorationProcessRepository
        );

        $serverAddr = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36'
            . ' (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36';

        $systemConfigRepositoryDecorationProcessRepository->insert([
            [
                'configurationKey' => 'my.custom.systemConfigTestInsertFull1',
                'configurationValueOld' => ['_value' => $oldValue],
                'configurationValueNew' => ['_value' => $newValue],
                'salesChannelId' => $salesChannelId,
                'username' => 'johndoe',
                'userData' => [
                    'user' => [
                        'username' => 'johndoe',
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'email' => 'johndoe@example.com',
                        'active' => true,
                    ],
                    'request' => [
                        'HTTP_USER_AGENT' => '192.168.0.99',
                        'SERVER_ADDR' => $serverAddr,
                    ],
                ],
            ],
            [
                'configurationKey' => 'my.custom.systemConfigTestInsertFull2',
                'configurationValueOld' => ['_value' => $oldValue],
                'configurationValueNew' => ['_value' => $newValue],
                'salesChannelId' => $salesChannelId,
                'username' => 'johndoe',
                'userData' => [
                    'user' => [
                        'username' => 'johndoe',
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'email' => 'johndoe@example.com',
                        'active' => true,
                    ],
                    'request' => [
                        'HTTP_USER_AGENT' => '192.168.0.99',
                        'SERVER_ADDR' => $serverAddr,
                    ],
                ],
            ],
        ]);

        $matheusGontijoSystemConfigHistoryRepository = $this->getContainer()->get(
            'matheus_gontijo_system_config_history.repository'
        );
        \assert($matheusGontijoSystemConfigHistoryRepository instanceof EntityRepository);

        $configurationKeys = [
            'my.custom.systemConfigTestInsertFull1',
            'my.custom.systemConfigTestInsertFull2',
        ];

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('configurationKey', $configurationKeys));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $criteria->addSorting(new FieldSorting('configurationKey', FieldSorting::ASCENDING));

        $searchResult = $matheusGontijoSystemConfigHistoryRepository
            ->search($criteria, Context::createDefaultContext());

        static::assertSame(2, $searchResult->getTotal());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[0]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame(
            'my.custom.systemConfigTestInsertFull1',
            $matheusGontijoSystemConfigHistory->getConfigurationKey()
        );
        static::assertSame(['_value' => $oldValue], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => $newValue], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame($salesChannelId, $matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertSame('johndoe', $matheusGontijoSystemConfigHistory->getUsername());
        static::assertEquals([
            'user' => [
                'username' => 'johndoe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@example.com',
                'active' => true,
            ],
            'request' => [
                'HTTP_USER_AGENT' => '192.168.0.99',
                'SERVER_ADDR' => $serverAddr,
            ],
        ], $matheusGontijoSystemConfigHistory->getUserData());

        $matheusGontijoSystemConfigHistory = $searchResult->get($searchResult->getKeys()[1]);
        \assert($matheusGontijoSystemConfigHistory instanceof MatheusGontijoSystemConfigHistoryEntity);

        static::assertSame(
            'my.custom.systemConfigTestInsertFull2',
            $matheusGontijoSystemConfigHistory->getConfigurationKey()
        );
        static::assertSame(['_value' => $oldValue], $matheusGontijoSystemConfigHistory->getConfigurationValueOld());
        static::assertSame(['_value' => $newValue], $matheusGontijoSystemConfigHistory->getConfigurationValueNew());
        static::assertSame($salesChannelId, $matheusGontijoSystemConfigHistory->getSalesChannelId());
        static::assertSame('johndoe', $matheusGontijoSystemConfigHistory->getUsername());
        static::assertEquals([
            'user' => [
                'username' => 'johndoe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@example.com',
                'active' => true,
            ],
            'request' => [
                'HTTP_USER_AGENT' => '192.168.0.99',
                'SERVER_ADDR' => $serverAddr,
            ],
        ], $matheusGontijoSystemConfigHistory->getUserData());
    }

    /**
     * @return array<mixed>
     */
    public function insertProvider(): array
    {
        return [
            ['aaa', 'bbb', null],
            [123, 456, null],
            [88.88, 99.99, null],
            [88, 99, null],
            [false, true, null],
            [['foo' => 'bar'], ['bar' => 'foo'], null],
            ['aaa', 'bbb', '3401944de62d41ffb1f686c8ada7870e'],
        ];
    }

    public function testLoadUser(): void
    {
        $connection = $this->getContainer()->get(Connection::class);
        \assert($connection instanceof Connection);

        $qb = $connection->createQueryBuilder();

        $qb->select('locale.id');
        $qb->from('language', 'language');
        $qb->innerJoin('language', 'locale', 'locale', 'language.locale_id = locale.id');
        $qb->where('language.id = :id');
        $qb->setParameter('id', Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM));

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $localeId = Uuid::fromBytesToHex($executeResult->fetchOne());

        $userRepository = $this->getContainer()->get('user.repository');
        \assert($userRepository instanceof EntityRepository);

        $password = password_hash('Shopware@1234', \PASSWORD_BCRYPT);

        $userData = [
            'id' => 'a667b0e4db7241c29bfb67b4520a3b9e',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'johndoe123@example.com',
            'username' => 'johndoe123',
            'password' => $password,
            'localeId' => $localeId,
            'active' => true,
            'admin' => true,
        ];

        $userRepository->create([$userData], Context::createDefaultContext());

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('username', 'johndoe123'));

        $searchResult = $userRepository->search($criteria, Context::createDefaultContext());

        $user = $searchResult->first();
        \assert($user instanceof UserEntity);

        $systemConfigRepositoryDecorationProcessRepository = $this->getContainer()->get(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        \assert(
            $systemConfigRepositoryDecorationProcessRepository instanceof SystemConfigRepositoryDecorationProcessRepository
        );

        static::assertEquals(
            $user,
            $systemConfigRepositoryDecorationProcessRepository->loadUser($user->getId())
        );
    }
}
