<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Model;

use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
// phpcs:ignore
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestStateRegistryUnitTest extends TestCase
{
    public function testSetGet(): void
    {
        $request1Mock = $this->createMock(Request::class);
        $request2Mock = $this->createMock(Request::class);

        $requestStateRegistry = new RequestStateRegistry();

        $requestStateRegistry->setRequest($request1Mock);
        $requestStateRegistry->setRequest($request2Mock);

        static::assertSame($request1Mock, $requestStateRegistry->getRequest());
    }
}
