<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @internal
 *
 * @covers \Olz\Utils\Session
 */
final class SessionTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(Session::class));
    }

    public function testHas(): void {
        $session = $this->createSession();
        $this->assertFalse($session->has('test'));
        $session->set('test', 'asdf');
        $this->assertTrue($session->has('test'));
    }

    public function testGet(): void {
        $session = $this->createSession();
        $this->assertNull($session->get('test'));
        $session->set('test', 'asdf');
        $this->assertSame('asdf', $session->get('test'));
    }

    public function testSet(): void {
        $session = $this->createSession();
        $this->assertFalse($session->has('test'));
        $session->set('test', 'asdf');
        $this->assertSame('asdf', $session->get('test'));
    }

    public function testDelete(): void {
        $session = $this->createSession();
        $session->set('test', 'asdf');
        $this->assertSame('asdf', $session->get('test'));
        $session->delete('test');
        $this->assertFalse($session->has('test'));
    }

    public function testClear(): void {
        $session = $this->createSession();
        $session->set('test', 'asdf');
        $this->assertTrue($session->has('test'));
        $session->clear();
        $this->assertFalse($session->has('test'));
    }

    public function testWithoutRequest(): void {
        $session = new Session(new RequestStack());
        $this->assertFalse($session->has('test'));
        $this->assertNull($session->get('test'));
    }

    private function createSession(): Session {
        $symfony_session = new SymfonySession(new MockArraySessionStorage());
        $symfony_session->start();
        $request = new Request();
        $request->setSession($symfony_session);
        $request_stack = new RequestStack();
        $request_stack->push($request);
        return new Session($request_stack);
    }
}
