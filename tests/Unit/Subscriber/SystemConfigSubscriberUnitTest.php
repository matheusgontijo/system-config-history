<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Subscriber;

use MatheusGontijo\SystemConfigHistory\Model\SystemConfigSubscriberProcess;
use MatheusGontijo\SystemConfigHistory\Subscriber\SystemConfigSubscriber;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Shopware\Core\System\SystemConfig\SystemConfigDefinition;

class SystemConfigSubscriberUnitTest extends TestCase
{
    public function testTriggerChangeSet(): void
    {
        $systemConfigSubscriberProcess = $this->createStub(SystemConfigSubscriberProcess::class);
        $writeContext = $this->createStub(WriteContext::class);
        $entityExistence = $this->createStub(EntityExistence::class);

        $systemConfigDefinition = new SystemConfigDefinition();
        $productDefinition = new ProductDefinition();

        $updateCommand1 = new UpdateCommand($systemConfigDefinition, [], [], $entityExistence, '');
        $updateCommand2 = new UpdateCommand($systemConfigDefinition, [], [], $entityExistence, '');
        $updateCommand3 = new UpdateCommand($productDefinition, [], [], $entityExistence, '');

        $commands = ['invalid', $updateCommand2, 'invalid', $updateCommand1, $updateCommand3];

        $preWriteValidationEvent = new PreWriteValidationEvent($writeContext, $commands);

        $systemConfigSubscriber = new SystemConfigSubscriber($systemConfigSubscriberProcess);
        $systemConfigSubscriber->triggerChangeSet($preWriteValidationEvent);

        $resultCommands = $preWriteValidationEvent->getCommands();
        static::assertCount(5, $resultCommands);

        $resultCommand1 = $resultCommands[1];
        assert($resultCommand1 instanceof UpdateCommand);

        $resultCommand3 = $resultCommands[3];
        assert($resultCommand3 instanceof UpdateCommand);

        $resultCommand4 = $resultCommands[4];
        assert($resultCommand4 instanceof UpdateCommand);

        static::assertSame('invalid', $resultCommands[0]);
        static::assertSame(true, $resultCommand1->requiresChangeSet());
        static::assertSame('invalid', $resultCommands[2]);
        static::assertSame(true, $resultCommand3->requiresChangeSet());
        static::assertSame(false, $resultCommand4->requiresChangeSet());
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
