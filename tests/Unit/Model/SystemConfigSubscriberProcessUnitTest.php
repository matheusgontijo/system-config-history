<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Model;

use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
use MatheusGontijo\SystemConfigHistory\Model\SystemConfigSubscriberProcess;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigSubscriberProcessRepository;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\ChangeSet;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;

class SystemConfigSubscriberProcessUnitTest extends TestCase
{
    public function testIsDisabled(): void
    {
        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
            SystemConfigSubscriberProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $entityWrittenEvent = $this->createMock(EntityWrittenEvent::class);
        $entityDeletedEvent = $this->createMock(EntityDeletedEvent::class);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(false);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::never())
            ->method('generateId');

        $requestStateRegistryMock->expects(static::never())
            ->method(static::anything());

        $systemConfigSubscriberProcessRepositoryMock->expects(static::never())
            ->method('insert');

        $systemConfigServiceDecoration = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->processEntityWrittenEvent($entityWrittenEvent);
        $systemConfigServiceDecoration->processEntityDeletedEvent($entityDeletedEvent);
    }

    public function testProcessEntityWrittenEventWithoutRequest(): void
    {
        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
            SystemConfigSubscriberProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $entityWriteResultMock1 = $this->createMock(EntityWriteResult::class);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn(null);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getPayload')
            ->willReturn([
                'configurationKey' => 'my.custom.systemConfig1',
                'salesChannelId' => null,
                'configurationValue' => 'aaa',
            ]);

        $entityWriteResultMock2 = $this->createMock(EntityWriteResult::class);

        $changeSet2 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig2',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"aaa"}',
        ], [
            'configuration_value' => '{"_value":"bbb"}',
        ], false);

        $entityWriteResultMock2->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet2);

        $entityWriteResultMock2->expects(static::exactly(1))
            ->method('getPayload')
            ->willReturn([]);

        $entityWrittenEventMock = $this->createMock(EntityWrittenEvent::class);

        $entityWrittenEventMock->expects(static::exactly(1))
            ->method('getWriteResults')
            ->willReturn([$entityWriteResultMock1, $entityWriteResultMock2]);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(2))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls(
                'c6316df22e754fe1af0eae305fd3a495',
                '1c957ed20cef4410ad1a6150079ab9f7'
            );

        $requestStateRegistryMock->expects(static::exactly(2))
            ->method('getRequest')
            ->willReturn(null);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('insert')
            ->withConsecutive(
                [
                    [
                        [
                            'id' => 'c6316df22e754fe1af0eae305fd3a495',
                            'configurationKey' => 'my.custom.systemConfig1',
                            'salesChannelId' => null,
                            'configurationValueOld' => null,
                            'configurationValueNew' => ['_value' => 'aaa'],
                        ],
                        [
                            'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                            'configurationKey' => 'my.custom.systemConfig2',
                            'salesChannelId' => null,
                            'configurationValueOld' => ['_value' => 'aaa'],
                            'configurationValueNew' => ['_value' => 'bbb'],
                        ]
                    ]
                ]
            );

        $systemConfigServiceDecoration = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->processEntityWrittenEvent($entityWrittenEventMock);
    }

    public function testProcessEntityWrittenEventWithRequest(): void
    {
        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
            SystemConfigSubscriberProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $entityWriteResultMock1 = $this->createMock(EntityWriteResult::class);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn(null);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getPayload')
            ->willReturn([
                'configurationKey' => 'my.custom.systemConfig1',
                'salesChannelId' => null,
                'configurationValue' => 'aaa',
            ]);

        $entityWriteResultMock2 = $this->createMock(EntityWriteResult::class);

        $changeSet2 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig2',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"aaa"}',
        ], [
            'configuration_value' => '{"_value":"bbb"}',
        ], false);

        $entityWriteResultMock2->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet2);

        $entityWriteResultMock2->expects(static::exactly(1))
            ->method('getPayload')
            ->willReturn([]);

        $entityWrittenEventMock = $this->createMock(EntityWrittenEvent::class);

        $entityWrittenEventMock->expects(static::exactly(1))
            ->method('getWriteResults')
            ->willReturn([$entityWriteResultMock1, $entityWriteResultMock2]);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(2))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls(
                'c6316df22e754fe1af0eae305fd3a495',
                '1c957ed20cef4410ad1a6150079ab9f7'
            );

        $request = Request::create('http://localhost', 'POST', [], [], [], []);

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

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('loadUser')
            ->willReturn($userEntity);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('insert')
            ->withConsecutive(
                [
                    [
                        [
                            'id' => 'c6316df22e754fe1af0eae305fd3a495',
                            'configurationKey' => 'my.custom.systemConfig1',
                            'salesChannelId' => null,
                            'configurationValueOld' => null,
                            'configurationValueNew' => ['_value' => 'aaa'],
                            'username' => 'johndoe',
                        ],
                        [
                            'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                            'configurationKey' => 'my.custom.systemConfig2',
                            'salesChannelId' => null,
                            'configurationValueOld' => ['_value' => 'aaa'],
                            'configurationValueNew' => ['_value' => 'bbb'],
                            'username' => 'johndoe',
                        ]
                    ]
                ]
            );

        $systemConfigServiceDecoration = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->processEntityWrittenEvent($entityWrittenEventMock);
    }

//    public function testWithoutRequestAttributeContextObject(): void
//    {
//        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
//            SystemConfigSubscriberProcessRepository::class
//        );
//        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
//
//        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
//        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);
//
//        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
//            ->method('isEnabled')
//            ->willReturn(true);
//
//        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(2))
//            ->method('getValue')
//            ->withConsecutive(['my.custom.systemConfig1', null])
//            ->willReturnOnConsecutiveCalls(
//                ['_value' => 'aaa'],
//                ['_value' => 'bbb']
//            );
//
//        $request = Request::create('http://localhost', 'POST', [], [], [], []);
//
//        $requestStateRegistryMock->method('getRequest')
//            ->willReturn($request);
//
//        $systemConfigServiceDecoration = new SystemConfigSubscriberProcess(
//            $systemConfigSubscriberProcessRepositoryMock,
//            $requestStateRegistryMock
//        );
//
//        $systemConfigServiceDecoration->process($callMock, [
//            [
//                'id' => 'c6316df22e754fe1af0eae305fd3a495',
//                'configurationKey' => 'my.custom.systemConfig1',
//                'configurationValue' => ['_value' => 'aaa'],
//                'salesChannelId' => null,
//            ],
//        ]);
//    }
//
//    public function testWithoutRequestWithoutAdminApiSource(): void
//    {
//        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
//            SystemConfigSubscriberProcessRepository::class
//        );
//        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);
//
//        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
//        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);
//
//        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
//            ->method('isEnabled')
//            ->willReturn(true);
//
//        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(2))
//            ->method('getValue')
//            ->withConsecutive(['my.custom.systemConfig1', null])
//            ->willReturnOnConsecutiveCalls(
//                ['_value' => 'aaa'],
//                ['_value' => 'bbb']
//            );
//
//        $request = Request::create('http://localhost', 'POST', [], [], [], []);
//
//        $context = new Context(new SalesChannelApiSource('72e7593c3a374ddc9c864abdf31dc766'));
//
//        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, $context);
//
//        $requestStateRegistryMock->method('getRequest')
//            ->willReturn($request);
//
//        $systemConfigServiceDecoration = new SystemConfigSubscriberProcess(
//            $systemConfigSubscriberProcessRepositoryMock,
//            $requestStateRegistryMock
//        );
//
//        $systemConfigServiceDecoration->process($callMock, [
//            [
//                'id' => 'c6316df22e754fe1af0eae305fd3a495',
//                'configurationKey' => 'my.custom.systemConfig1',
//                'configurationValue' => ['_value' => 'aaa'],
//                'salesChannelId' => null,
//            ],
//        ]);
//    }
}
