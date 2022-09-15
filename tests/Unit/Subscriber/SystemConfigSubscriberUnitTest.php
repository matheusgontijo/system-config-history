<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Subscriber;

use MatheusGontijo\SystemConfigHistory\Model\SystemConfigSubscriberProcess;
use MatheusGontijo\SystemConfigHistory\Subscriber\SystemConfigSubscriber;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;

class SystemConfigSubscriberUnitTest extends TestCase
{
    public function testTriggerChangeSetInvalidCommands(): void
    {
        $systemConfigSubscriberProcessMock = $this->createMock(SystemConfigSubscriberProcess::class);

        $entityDefinitionMock = $this->createMock(EntityDefinition::class);

        $entityDefinitionMock->expects(static::exactly(2))
            ->method('getEntityName')
            ->willReturn('product');

        $updateCommandMock = $this->createMock(UpdateCommand::class);

        $updateCommandMock->expects(static::exactly(2))
            ->method('getDefinition')
            ->willReturn($entityDefinitionMock);

        $updateCommandMock->expects(static::never())
            ->method('requestChangeSet');

        $preWriteValidationEventMock = $this->createMock(PreWriteValidationEvent::class);

        $preWriteValidationEventMock->expects(static::exactly(1))
            ->method('getCommands')
            ->willReturn(['invalid', 'invalid', $updateCommandMock, $updateCommandMock]);

        $systemConfigSubscriber = new SystemConfigSubscriber($systemConfigSubscriberProcessMock);

        $systemConfigSubscriber->triggerChangeSet($preWriteValidationEventMock);
    }

    public function testTriggerChangeSet(): void
    {
        $systemConfigSubscriberProcessMock = $this->createMock(SystemConfigSubscriberProcess::class);

        $entityDefinitionMock = $this->createMock(EntityDefinition::class);

        $entityDefinitionMock->expects(static::exactly(1))
            ->method('getEntityName')
            ->willReturn('system_config');

        $updateCommandMock = $this->createMock(UpdateCommand::class);

        $updateCommandMock->expects(static::exactly(1))
            ->method('getDefinition')
            ->willReturn($entityDefinitionMock);

        $updateCommandMock->expects(static::exactly(1))
            ->method('requestChangeSet');

        $preWriteValidationEventMock = $this->createMock(PreWriteValidationEvent::class);

        $preWriteValidationEventMock->expects(static::exactly(1))
            ->method('getCommands')
            ->willReturn([$updateCommandMock]);

        $systemConfigSubscriber = new SystemConfigSubscriber($systemConfigSubscriberProcessMock);

        $systemConfigSubscriber->triggerChangeSet($preWriteValidationEventMock);
    }

    public function testSystemConfigWritten(): void
    {
        $systemConfigSubscriberProcessMock = $this->createMock(SystemConfigSubscriberProcess::class);

        $entityWrittenEvent = $this->createMock(EntityWrittenEvent::class);

        $systemConfigSubscriberProcessMock->expects(static::exactly(1))
            ->method('processEntityWrittenEvent')
            ->withConsecutive([$entityWrittenEvent]);

        $systemConfigSubscriber = new SystemConfigSubscriber($systemConfigSubscriberProcessMock);

        $systemConfigSubscriber->systemConfigWritten($entityWrittenEvent);
    }

    public function testSystemConfigDeleted(): void
    {
        $systemConfigSubscriberProcessMock = $this->createMock(SystemConfigSubscriberProcess::class);

        $entityDeletedEvent = $this->createMock(EntityDeletedEvent::class);

        $systemConfigSubscriberProcessMock->expects(static::exactly(1))
            ->method('processEntityDeletedEvent')
            ->withConsecutive([$entityDeletedEvent]);

        $systemConfigSubscriber = new SystemConfigSubscriber($systemConfigSubscriberProcessMock);

        $systemConfigSubscriber->systemConfigDeleted($entityDeletedEvent);
    }

    public function testGetSubscribedEvents(): void
    {
        static::assertSame(
            [
                PreWriteValidationEvent::class => 'triggerChangeSet',
                'system_config.written' => 'systemConfigWritten',
                'system_config.deleted' => 'systemConfigDeleted',
            ],
            SystemConfigSubscriber::getSubscribedEvents()
        );
    }
}
