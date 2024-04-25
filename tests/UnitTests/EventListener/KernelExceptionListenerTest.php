<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\EventListener;

use Olz\EventListener\KernelExceptionListener;
use Olz\Kernel;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @internal
 *
 * @coversNothing
 */
class KernelExceptionListenerForTest extends KernelExceptionListener {
    public function testOnlySetIsHandlingException(\Throwable $exception) {
        $this->is_handling_exception = $exception;
    }
}

/**
 * @internal
 *
 * @covers \Olz\EventListener\KernelExceptionListener
 */
final class KernelExceptionListenerTest extends UnitTestCase {
    public function testOnKernelHttpException(): void {
        $kernel = new Kernel('test', true);
        $request = new Request();
        $throwable = new HttpException(500, 'fake-internal-error');
        $exception_event = new ExceptionEvent($kernel, $request, 0, $throwable);

        $listener = new KernelExceptionListenerForTest();

        $listener->onKernelException($exception_event);

        $this->assertSame([
            'INFO Handling HttpExceptionInterface fake-internal-error...',
        ], $this->getLogs());
    }

    public function testOnKernelOtherException(): void {
        $kernel = new Kernel('test', true);
        $request = new Request();
        $throwable = new \Exception('fake-exception');
        $exception_event = new ExceptionEvent($kernel, $request, 0, $throwable);

        $listener = new KernelExceptionListenerForTest();

        $listener->onKernelException($exception_event);

        $this->assertSame([
            'WARNING Non-HttpExceptionInterface exception: fake-exception',
        ], $this->getLogs());
    }

    public function testOnKernelRecursiveException(): void {
        $kernel = new Kernel('test', true);
        $request = new Request();
        $throwable_1 = new \Exception('fake-exception-1');
        $throwable_2 = new \Exception('fake-exception-2');
        $exception_event_2 = new ExceptionEvent($kernel, $request, 0, $throwable_2);

        $listener = new KernelExceptionListenerForTest();
        $listener->testOnlySetIsHandlingException($throwable_1);

        $listener->onKernelException($exception_event_2);

        $this->assertSame([
            'WARNING Was already handling exception fake-exception-1, but now also fake-exception-2.',
        ], $this->getLogs());
    }
}
