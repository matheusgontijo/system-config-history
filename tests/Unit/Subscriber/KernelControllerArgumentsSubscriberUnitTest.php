<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Subscriber;

use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
use MatheusGontijo\SystemConfigHistory\Subscriber\KernelControllerArgumentsSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelControllerArgumentsSubscriberUnitTest extends TestCase
{
    public function testOnKernelControllerArguments(): void
    {
        $requestMock = $this->createMock(Request::class);

        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        $requestStateRegistryMock->expects(static::exactly(1))
                ->method('setRequest')
                ->withConsecutive([$requestMock]);

        $kernelControllerArgumentsSubscriber = new KernelControllerArgumentsSubscriber($requestStateRegistryMock);

        $controllerArgumentsEvent = new ControllerArgumentsEvent(
            $this->createStub(HttpKernelInterface::class),
            static function (): void {
            },
            ['request' => $requestMock],
            $this->createStub(Request::class),
            1
        );

        $kernelControllerArgumentsSubscriber->onKernelControllerArguments($controllerArgumentsEvent);
    }

    public function testGetSubscribedEvents(): void
    {
        static::assertSame(
            [KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments'],
            KernelControllerArgumentsSubscriber::getSubscribedEvents()
        );
    }
}
