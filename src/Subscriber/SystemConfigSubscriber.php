<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Subscriber;

use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
use MatheusGontijo\SystemConfigHistory\Model\SystemConfigSubscriberProcess;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\DeleteCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\System\SystemConfig\SystemConfigDefinition;
use Shopware\Core\System\SystemConfig\SystemConfigEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\ChangeSetAware;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\InsertCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;

class SystemConfigSubscriber implements EventSubscriberInterface
{
    private SystemConfigSubscriberProcess $systemConfigSubscriberProcess;

    public function __construct(SystemConfigSubscriberProcess $systemConfigSubscriberProcess)
    {
        $this->systemConfigSubscriberProcess = $systemConfigSubscriberProcess;
    }

    public function triggerChangeSet(PreWriteValidationEvent $event): void
    {
        foreach ($event->getCommands() as $command) {
            if (!$command instanceof ChangeSetAware) {
                continue;
            }

            \assert(
                $command instanceof ChangeSetAware
                || $command instanceof InsertCommand
                || $command instanceof UpdateCommand
                || $command instanceof DeleteCommand
            );

            if ($command->getDefinition()->getEntityName() !== SystemConfigDefinition::ENTITY_NAME) {
                continue;
            }

            $command->requestChangeSet();
        }
    }

    public function systemConfigWritten(EntityWrittenEvent $event)
    {
        $this->systemConfigSubscriberProcess->processEntityWrittenEvent($event);
    }

    public function systemConfigDeleted(EntityDeletedEvent $event): void
    {
        $this->systemConfigSubscriberProcess->processEntityDeletedEvent($event);
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => 'triggerChangeSet',
            'system_config.written' => 'systemConfigWritten',
            'system_config.deleted' => 'systemConfigDeleted',
        ];
    }
}
