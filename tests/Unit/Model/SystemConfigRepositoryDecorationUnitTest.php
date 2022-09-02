<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Model;

use Doctrine\DBAL\Connection;
use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
use MatheusGontijo\SystemConfigHistory\Model\SystemConfigRepositoryDecoration;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationRepository;
// phpcs:ignore
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SystemConfig\SystemConfigLoader;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\System\SystemConfig\Util\ConfigReader;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\System\SystemConfig\SystemConfigDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\VersionManager;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @TODO: TEST ENABLED/DISABLED
 */
class SystemConfigRepositoryDecorationUnitTest extends TestCase
{
//    public function testIsDisabled(): void
//    {
//        $systemConfigServiceMock = $this->createMock(SystemConfigService::class);
//        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
//            SystemConfigRepositoryDecorationRepository::class
//        );
//
//        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
//
//        $systemConfigServiceMock->expects(static::exactly(1))
//            ->method('get')
//            ->withConsecutive(['matheusGontijo.systemConfigHistory.enabled'])
//            ->willReturnOnConsecutiveCalls(false);
//
//        $systemConfigServiceMock->expects(static::exactly(1))
//            ->method('set')
//            ->withConsecutive(['my.custom.systemConfig', 'aaa', null]);
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::never())
//            ->method(static::anything());
//
//        $requestStateRegistryMock->expects(static::never())
//            ->method(static::anything());
//
//        $systemConfigServiceDecoration = $this->createSystemConfigRepositoryDecoration(
//            $systemConfigServiceMock,
//            $systemConfigRepositoryDecorationRepositoryMock,
//            $requestStateRegistryMock
//        );
//
//        $systemConfigServiceDecoration->set('my.custom.systemConfig', 'aaa', null);
//    }

    public function testCreate(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

//        $systemConfigServiceMock->expects(static::exactly(1))
//            ->method('get')
//            ->withConsecutive(['matheusGontijo.systemConfigHistory.enabled'])
//            ->willReturnOnConsecutiveCalls(true);
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(2))
//            ->method('getValue')
//            ->withConsecutive(
//                ['my.custom.systemConfig', null],
//                ['my.custom.systemConfig', null]
//            )
//            ->willReturnOnConsecutiveCalls(
//                ['_value' => 'aaa'],
//                ['_value' => 'aaa']
//            );
//
//        $systemConfigServiceMock->expects(static::exactly(1))
//            ->method('set')
//            ->withConsecutive(['my.custom.systemConfig', 'aaa']);
//
//        $requestStateRegistryMock->expects(static::never())
//            ->method(static::anything());

        $systemConfigServiceDecoration = $this->createSystemConfigRepositoryDecoration(
            $systemConfigServiceMock,
            $systemConfigRepositoryDecorationRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->create([
            [
                'id' => '78aed5229823419986df8fc25554fc9b',
                'configurationKey' => 'my.custom.key1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '16b2b59393ee40f1b4657102c59ed3f4',
                'configurationKey' => 'my.custom.key2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
            [
                'id' => '67a6a8c859484202a9c56e2ed8e487d6',
                'configurationKey' => 'my.custom.key2',
                'configurationValue' => ['_value' => 'ccc'],
                'salesChannelId' => null,
            ],
        ], Context::createDefaultContext());



//        $systemConfigServiceDecoration->create('my.custom.systemConfig', 'aaa', null);
    }

//    public function testSetDifferentValueWithoutAdminRequest(): void
//    {
//        $systemConfigServiceMock = $this->createMock(SystemConfigService::class);
//        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
//            SystemConfigRepositoryDecorationRepository::class
//        );
//        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
//
//        $systemConfigServiceMock->expects(static::exactly(1))
//            ->method('get')
//            ->withConsecutive(['matheusGontijo.systemConfigHistory.enabled'])
//            ->willReturnOnConsecutiveCalls(true);
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(2))
//            ->method('getValue')
//            ->withConsecutive(
//                ['my.custom.systemConfig', null],
//                ['my.custom.systemConfig', null]
//            )
//            ->willReturnOnConsecutiveCalls(
//                ['_value' => 'aaa'],
//                ['_value' => 'bbb']
//            );
//
//        $systemConfigServiceMock->expects(static::exactly(1))
//            ->method('set')
//            ->withConsecutive(['my.custom.systemConfig', 'bbb']);
//
//        $requestStateRegistryMock->method('getRequest')
//            ->willReturn(null);
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(1))
//            ->method('generateId')
//            ->willReturn('c6316df22e754fe1af0eae305fd3a495');
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(1))
//            ->method('insert')
//            ->withConsecutive(
//                [
//                    [
//                        'id' => 'c6316df22e754fe1af0eae305fd3a495',
//                        'configurationKey' => 'my.custom.systemConfig',
//                        'configurationValueOld' => ['_value' => 'aaa'],
//                        'configurationValueNew' => ['_value' => 'bbb'],
//                        'salesChannelId' => null,
//                    ],
//                ]
//            );
//
//        $systemConfigServiceDecoration = $this->createSystemConfigRepositoryDecoration(
//            $systemConfigServiceMock,
//            $systemConfigRepositoryDecorationRepositoryMock,
//            $requestStateRegistryMock
//        );
//
//        $systemConfigServiceDecoration->set('my.custom.systemConfig', 'bbb');
//    }
//
//    public function testSetDifferentValueWithAdminRequest(): void
//    {
//        $systemConfigServiceMock = $this->createMock(SystemConfigService::class);
//        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
//            SystemConfigRepositoryDecorationRepository::class
//        );
//        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
//
//        $systemConfigServiceMock->expects(static::exactly(2))
//            ->method('get')
//            ->withConsecutive(
//                ['matheusGontijo.systemConfigHistory.enabled'],
//                ['matheusGontijo.systemConfigHistory.enabled']
//            )
//            ->willReturnOnConsecutiveCalls(true, true);
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(4))
//            ->method('getValue')
//            ->withConsecutive(
//                ['my.custom.systemConfig', null],
//                ['my.custom.systemConfig', null],
//                ['my.custom.systemConfig', null],
//                ['my.custom.systemConfig', null]
//            )
//            ->willReturnOnConsecutiveCalls(
//                ['_value' => 'aaa'],
//                ['_value' => 'bbb'],
//                ['_value' => 'bbb'],
//                ['_value' => 'ccc']
//            );
//
//        $systemConfigServiceMock->expects(static::exactly(2))
//            ->method('set')
//            ->withConsecutive(
//                ['my.custom.systemConfig', 'bbb'],
//                ['my.custom.systemConfig', 'ccc']
//            );
//
//        $context = new Context(new AdminApiSource('72e7593c3a374ddc9c864abdf31dc766'));
//
//        $serverAddr = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36'
//            . ' (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36';
//
//        $request = Request::create(
//            'http://localhost',
//            'POST',
//            [],
//            [],
//            [],
//            [
//                'HTTP_USER_AGENT' => '192.168.0.99',
//                'SERVER_ADDR' => $serverAddr,
//            ]
//        );
//
//        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, $context);
//
//        $requestStateRegistryMock->method('getRequest')
//            ->willReturn($request);
//
//        $userEntity = new UserEntity();
//        $userEntity->setUsername('johndoe');
//        $userEntity->setFirstName('John');
//        $userEntity->setLastName('Doe');
//        $userEntity->setEmail('johndoe@example.com');
//        $userEntity->setActive(true);
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(1))
//            ->method('loadUser')
//            ->willReturn($userEntity);
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(2))
//            ->method('generateId')
//            ->willReturnOnConsecutiveCalls(
//                '57da17dcc2b74e43a7ed4e570d756658',
//                'a6eb533c801b4e4abdba4428c28bcce0'
//            );
//
//        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(2))
//            ->method('insert')
//            ->withConsecutive(
//                [
//                    [
//                        'id' => '57da17dcc2b74e43a7ed4e570d756658',
//                        'configurationKey' => 'my.custom.systemConfig',
//                        'configurationValueOld' => ['_value' => 'aaa'],
//                        'configurationValueNew' => ['_value' => 'bbb'],
//                        'salesChannelId' => null,
//                        'username' => 'johndoe',
//                        'userData' => [
//                            'user' => [
//                                'username' => 'johndoe',
//                                'first_name' => 'John',
//                                'last_name' => 'Doe',
//                                'email' => 'johndoe@example.com',
//                                'active' => true,
//                            ],
//                            'request' => [
//                                'HTTP_USER_AGENT' => '192.168.0.99',
//                                'SERVER_ADDR' => $serverAddr,
//                            ],
//                        ],
//                    ],
//                ],
//                [
//                    [
//                        'id' => 'a6eb533c801b4e4abdba4428c28bcce0',
//                        'configurationKey' => 'my.custom.systemConfig',
//                        'configurationValueOld' => ['_value' => 'bbb'],
//                        'configurationValueNew' => ['_value' => 'ccc'],
//                        'salesChannelId' => null,
//                        'username' => 'johndoe',
//                        'userData' => [
//                            'user' => [
//                                'username' => 'johndoe',
//                                'first_name' => 'John',
//                                'last_name' => 'Doe',
//                                'email' => 'johndoe@example.com',
//                                'active' => true,
//                            ],
//                            'request' => [
//                                'HTTP_USER_AGENT' => '192.168.0.99',
//                                'SERVER_ADDR' => $serverAddr,
//                            ],
//                        ],
//                    ],
//                ]
//            );
//
//        $systemConfigServiceDecoration = $this->createSystemConfigRepositoryDecoration(
//            $systemConfigServiceMock,
//            $systemConfigRepositoryDecorationRepositoryMock,
//            $requestStateRegistryMock
//        );
//
//        $systemConfigServiceDecoration->set('my.custom.systemConfig', 'bbb');
//        $systemConfigServiceDecoration->set('my.custom.systemConfig', 'ccc');
//    }

    private function createSystemConfigRepositoryDecoration(
        EntityRepository $entityRepository,
        SystemConfigRepositoryDecorationRepository $systemConfigRepositoryDecorationRepository,
        RequestStateRegistry $requestStateRegistry
    ): SystemConfigRepositoryDecoration {
        return new SystemConfigRepositoryDecoration(
            $entityRepository,
            $systemConfigRepositoryDecorationRepository,
            $requestStateRegistry,
            $this->createStub(SystemConfigDefinition::class),
            $this->createStub(EntityReaderInterface::class),
            $this->createStub(VersionManager::class),
            $this->createStub(EntitySearcherInterface::class),
            $this->createStub(EntityAggregatorInterface::class),
            $this->createStub(EventDispatcherInterface::class),
            $this->createStub(EntityLoadedEventFactory::class),
        );
    }
}
