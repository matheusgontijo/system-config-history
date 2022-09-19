<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Model;

use MatheusGontijo\SystemConfigHistory\Model\SystemConfigSubscriberProcess;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigSubscriberProcessRepository;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\ChangeSet;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityWrittenEvent($entityWrittenEvent);
        $systemConfigSubscriberProcess->processEntityDeletedEvent($entityDeletedEvent);
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
        ], ['configuration_value' => '{"_value":"bbb"}'], false);

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
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

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
                        ],
                    ],
                ]
            );

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityWrittenEvent($entityWrittenEventMock);
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
        ], ['configuration_value' => '{"_value":"bbb"}'], false);

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
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

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
                        ],
                    ],
                ]
            );

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityWrittenEvent($entityWrittenEventMock);
    }

    public function testProcessEntityWrittenEventWithoutRequestAttributeContextObject(): void
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
        ], ['configuration_value' => '{"_value":"bbb"}'], false);

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
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

        $request = Request::create('http://localhost', 'POST', [], [], [], []);

        $requestStateRegistryMock->expects(static::exactly(2))
            ->method('getRequest')
            ->willReturn($request);

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
                        ],
                    ],
                ]
            );

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityWrittenEvent($entityWrittenEventMock);
    }

    public function testProcessEntityWrittenEventWithoutRequestAdminApiSource(): void
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
        ], ['configuration_value' => '{"_value":"bbb"}'], false);

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
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

        $request = Request::create('http://localhost', 'POST', [], [], [], []);

        $context = new Context(new SalesChannelApiSource('72e7593c3a374ddc9c864abdf31dc766'));

        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, $context);

        $requestStateRegistryMock->expects(static::exactly(2))
            ->method('getRequest')
            ->willReturn($request);

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
                        ],
                    ],
                ]
            );

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityWrittenEvent($entityWrittenEventMock);
    }

    public function testProcessEntityDeletedEventWithoutRequest(): void
    {
        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
            SystemConfigSubscriberProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $entityWriteResultMock1 = $this->createMock(EntityWriteResult::class);

        $changeSet1 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig1',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"aaa"}',
        ], [], false);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet1);

        $entityWriteResultMock2 = $this->createMock(EntityWriteResult::class);

        $changeSet2 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig2',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"bbb"}',
        ], [], false);

        $entityWriteResultMock2->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet2);

        $entityWriteResultMock3 = $this->createMock(EntityWriteResult::class);

        $changeSet3 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig3',
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
            'configuration_value' => '{"_value":"ccc"}',
        ], [], false);

        $entityWriteResultMock3->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet3);

        $entityDeletedEventMock = $this->createMock(EntityDeletedEvent::class);

        $entityDeletedEventMock->expects(static::exactly(1))
            ->method('getWriteResults')
            ->willReturn([$entityWriteResultMock1, $entityWriteResultMock2, $entityWriteResultMock3]);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(3))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls(
                'c6316df22e754fe1af0eae305fd3a495',
                '1c957ed20cef4410ad1a6150079ab9f7',
                '6bc87c0ec58d4505b4eb4e54355ff63e'
            );

        $requestStateRegistryMock->expects(static::exactly(3))
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
                            'configurationValueOld' => ['_value' => 'aaa'],
                            'configurationValueNew' => null,
                        ],
                        [
                            'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                            'configurationKey' => 'my.custom.systemConfig2',
                            'salesChannelId' => null,
                            'configurationValueOld' => ['_value' => 'bbb'],
                            'configurationValueNew' => null,
                        ],
                        [
                            'id' => '6bc87c0ec58d4505b4eb4e54355ff63e',
                            'configurationKey' => 'my.custom.systemConfig3',
                            'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
                            'configurationValueOld' => ['_value' => 'ccc'],
                            'configurationValueNew' => null,
                        ],
                    ],
                ]
            );

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityDeletedEvent($entityDeletedEventMock);
    }

    public function testProcessEntityDeletedEventWithRequest(): void
    {
        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
            SystemConfigSubscriberProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $entityWriteResultMock1 = $this->createMock(EntityWriteResult::class);

        $changeSet1 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig1',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"aaa"}',
        ], [], false);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet1);

        $entityWriteResultMock2 = $this->createMock(EntityWriteResult::class);

        $changeSet2 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig2',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"bbb"}',
        ], [], false);

        $entityWriteResultMock2->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet2);

        $entityDeletedEventMock = $this->createMock(EntityDeletedEvent::class);

        $entityDeletedEventMock->expects(static::exactly(1))
            ->method('getWriteResults')
            ->willReturn([$entityWriteResultMock1, $entityWriteResultMock2]);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(2))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

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
                            'configurationValueOld' => ['_value' => 'aaa'],
                            'configurationValueNew' => null,
                            'username' => 'johndoe',
                        ],
                        [
                            'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                            'configurationKey' => 'my.custom.systemConfig2',
                            'salesChannelId' => null,
                            'configurationValueOld' => ['_value' => 'bbb'],
                            'configurationValueNew' => null,
                            'username' => 'johndoe',
                        ],
                    ],
                ]
            );

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityDeletedEvent($entityDeletedEventMock);
    }

    public function testProcessEntityWrittenEventWithEqualValues(): void
    {
        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
            SystemConfigSubscriberProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $entityWriteResultMock1 = $this->createMock(EntityWriteResult::class);

        $changeSet1 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig1',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"aaa"}',
        ], ['configuration_value' => '{"_value":"aaa"}'], false);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet1);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getPayload')
            ->willReturn([]);

        $entityWriteResultMock2 = $this->createMock(EntityWriteResult::class);

        $changeSet2 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig2',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"aaa"}',
        ], ['configuration_value' => '{"_value":"aaa"}'], false);

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

        $systemConfigSubscriberProcessRepositoryMock->expects(static::never())
            ->method('generateId');

        $requestStateRegistryMock->expects(static::never())
            ->method('getRequest');

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('insert')
            ->withConsecutive([]);

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityWrittenEvent($entityWrittenEventMock);
    }

    public function testProcessEntityWrittenEventWithEqualValuesButSpacedJsonString(): void
    {
        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
            SystemConfigSubscriberProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $entityWriteResultMock1 = $this->createMock(EntityWriteResult::class);

        $changeSet1 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig1',
            'sales_channel_id' => null,
            'configuration_value' => '{"_value":"aaa"}',
        ], ['configuration_value' => '{"_value":           "aaa"}'], false);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet1);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getPayload')
            ->willReturn([]);

        $entityWrittenEventMock = $this->createMock(EntityWrittenEvent::class);

        $entityWrittenEventMock->expects(static::exactly(1))
            ->method('getWriteResults')
            ->willReturn([$entityWriteResultMock1]);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::never())
            ->method('generateId');

        $requestStateRegistryMock->expects(static::never())
            ->method('getRequest');

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('insert')
            ->withConsecutive([]);

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityWrittenEvent($entityWrittenEventMock);
    }

    public function testProcessEntityWrittenEventPassingSalesChannelId(): void
    {
        $systemConfigSubscriberProcessRepositoryMock = $this->createMock(
            SystemConfigSubscriberProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $entityWriteResultMock1 = $this->createMock(EntityWriteResult::class);

        $changeSet1 = new ChangeSet([
            'configuration_key' => 'my.custom.systemConfig1',
            'sales_channel_id' => Uuid::fromHexToBytes(TestDefaults::SALES_CHANNEL_ID_ENGLISH),
            'configuration_value' => '{"_value":"aaa"}',
        ], ['configuration_value' => '{"_value":"bbb"}'], false);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getChangeSet')
            ->willReturn($changeSet1);

        $entityWriteResultMock1->expects(static::exactly(1))
            ->method('getPayload')
            ->willReturn([]);

        $entityWrittenEventMock = $this->createMock(EntityWrittenEvent::class);

        $entityWrittenEventMock->expects(static::exactly(1))
            ->method('getWriteResults')
            ->willReturn([$entityWriteResultMock1]);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigSubscriberProcessRepositoryMock->expects(static::exactly(1))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495');

        $requestStateRegistryMock->expects(static::exactly(1))
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
                            'salesChannelId' => TestDefaults::SALES_CHANNEL_ID_ENGLISH,
                            'configurationValueOld' => ['_value' => 'aaa'],
                            'configurationValueNew' => ['_value' => 'bbb'],
                        ],
                    ],
                ]
            );

        $systemConfigSubscriberProcess = new SystemConfigSubscriberProcess(
            $systemConfigSubscriberProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigSubscriberProcess->processEntityWrittenEvent($entityWrittenEventMock);
    }
}
