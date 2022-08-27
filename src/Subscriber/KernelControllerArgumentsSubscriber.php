<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Subscriber;

use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelControllerArgumentsSubscriber implements EventSubscriberInterface
{
    private RequestStateRegistry $requestStateRegistry;

    public function __construct(RequestStateRegistry $requestStateRegistry)
    {
        $this->requestStateRegistry = $requestStateRegistry;
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $controllerArgumentsEvent): void
    {
        $this->requestStateRegistry->setRequest($controllerArgumentsEvent->getRequest());
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments'];
    }
}
