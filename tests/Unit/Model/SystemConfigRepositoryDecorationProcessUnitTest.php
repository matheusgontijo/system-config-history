<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Model;

use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
use MatheusGontijo\SystemConfigHistory\Model\SystemConfigRepositoryDecorationProcess;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationProcessRepository;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;

/**
 * @TODO: TEST ENABLED/DISABLED
 */
class SystemConfigRepositoryDecorationProcessUnitTest extends TestCase
{
//    public function testIsDisabled(): void
//    {
//        $systemConfigServiceMock = $this->createMock(SystemConfigService::class);
//        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
//            SystemConfigRepositoryDecorationProcessRepository::class
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
//        $systemConfigServiceDecoration = $this->createSystemConfigRepositoryDecorationProcess(
//            $systemConfigServiceMock,
//            $systemConfigRepositoryDecorationRepositoryMock,
//            $requestStateRegistryMock
//        );
//
//        $systemConfigServiceDecoration->set('my.custom.systemConfig', 'aaa', null);
//    }

    public function testEqualValues(): void
    {
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(4))
            ->method('getValue')
            ->withConsecutive(
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null]
            )
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb'],
                ['_value' => 'aaa'],
                ['_value' => 'bbb']
            );

        $requestStateRegistryMock->expects(static::never())
            ->method(static::anything());

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testMixedDifferentAndEqualValues(): void
    {
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(6))
            ->method('getValue')
            ->withConsecutive(
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig3', null],
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig3', null]
            )
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb'],
                ['_value' => 'ccc'],
                ['_value' => 'bbb'],
                ['_value' => 'bbb'],
                ['_value' => 'zzz']
            );

        $requestStateRegistryMock->expects(static::exactly(4))
            ->method('getRequest')
            ->willReturn(null);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
            [
                'id' => '4191798f32f045b78e116097e1ac6ed3',
                'configurationKey' => 'my.custom.systemConfig3',
                'configurationValue' => ['_value' => 'ccc'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testDifferentValuesWithoutAdminRequest(): void
    {
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(4))
            ->method('getValue')
            ->withConsecutive(
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null]
            )
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb'],
                ['_value' => 'bbb'],
                ['_value' => 'aaa']
            );

        $requestStateRegistryMock->expects(static::exactly(4))
            ->method('getRequest')
            ->willReturn(null);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(2))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(1))
            ->method('insert')
            ->withConsecutive([
                [
                    [
                        'id' => 'c6316df22e754fe1af0eae305fd3a495',
                        'configurationKey' => 'my.custom.systemConfig1',
                        'configurationValueOld' => ['_value' => 'aaa'],
                        'configurationValueNew' => ['_value' => 'bbb'],
                        'salesChannelId' => null,
                    ],
                    [
                        'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                        'configurationKey' => 'my.custom.systemConfig2',
                        'configurationValueOld' => ['_value' => 'bbb'],
                        'configurationValueNew' => ['_value' => 'aaa'],
                        'salesChannelId' => null,
                    ],
                ],
            ]);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testDifferentValuesWithAdminRequest(): void
    {
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(4))
            ->method('getValue')
            ->withConsecutive(
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null]
            )
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb'],
                ['_value' => 'bbb'],
                ['_value' => 'aaa']
            );

        $serverAddr = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36'
            . ' (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36';

        $request = Request::create(
            'http://localhost',
            'POST',
            [],
            [],
            [],
            [
                'HTTP_USER_AGENT' => '192.168.0.99',
                'SERVER_ADDR' => $serverAddr,
            ]
        );

        $context = new Context(new AdminApiSource('72e7593c3a374ddc9c864abdf31dc766'));

        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, $context);

        $requestStateRegistryMock->method('getRequest')
            ->willReturn($request);

        $userEntity = new UserEntity();
        $userEntity->setUsername('johndoe');
        $userEntity->setFirstName('John');
        $userEntity->setLastName('Doe');
        $userEntity->setEmail('johndoe@example.com');
        $userEntity->setActive(true);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(1))
            ->method('loadUser')
            ->willReturn($userEntity);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(2))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(1))
            ->method('insert')
            ->withConsecutive([
                [
                    [
                        'id' => 'c6316df22e754fe1af0eae305fd3a495',
                        'configurationKey' => 'my.custom.systemConfig1',
                        'configurationValueOld' => ['_value' => 'aaa'],
                        'configurationValueNew' => ['_value' => 'bbb'],
                        'salesChannelId' => null,
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
                        'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                        'configurationKey' => 'my.custom.systemConfig2',
                        'configurationValueOld' => ['_value' => 'bbb'],
                        'configurationValueNew' => ['_value' => 'aaa'],
                        'salesChannelId' => null,
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
                ],
            ]);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testWithoutRequestAttributeContextObject(): void
    {
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(2))
            ->method('getValue')
            ->withConsecutive(['my.custom.systemConfig1', null])
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb']
            );

        $serverAddr = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36'
            . ' (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36';

        $request = Request::create(
            'http://localhost',
            'POST',
            [],
            [],
            [],
            [
                'HTTP_USER_AGENT' => '192.168.0.99',
                'SERVER_ADDR' => $serverAddr,
            ]
        );

        $requestStateRegistryMock->method('getRequest')
            ->willReturn($request);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testWithoutRequestWithoutAdminApiSource(): void
    {
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(2))
            ->method('getValue')
            ->withConsecutive(['my.custom.systemConfig1', null])
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb']
            );

        $serverAddr = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36'
            . ' (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36';

        $request = Request::create(
            'http://localhost',
            'POST',
            [],
            [],
            [],
            [
                'HTTP_USER_AGENT' => '192.168.0.99',
                'SERVER_ADDR' => $serverAddr,
            ]
        );

        $context = new Context(new SalesChannelApiSource('72e7593c3a374ddc9c864abdf31dc766'));

        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, $context);

        $requestStateRegistryMock->method('getRequest')
            ->willReturn($request);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testInvalidValues(): void
    {
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::never())
            ->method(static::anything());

        $requestStateRegistryMock->expects(static::never())
            ->method(static::anything());

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::never())
            ->method(static::anything());

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::never())
            ->method(static::anything());

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [[], []]);
    }
}
