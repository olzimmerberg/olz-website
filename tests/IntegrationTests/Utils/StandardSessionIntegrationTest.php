<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
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
final class StandardSessionIntegrationTest extends IntegrationTestCase {
    public function testStandardSession(): void {
        self::bootKernel();
        $container = self::getContainer();

        // Attach a mock session to the current request, so that the real
        // `Session` service (wired to the request stack) can be exercised.
        /** @var RequestStack $request_stack */
        $request_stack = $container->get(RequestStack::class);
        $symfony_session = new SymfonySession(new MockArraySessionStorage());
        $symfony_session->start();
        $request = new Request();
        $request->setSession($symfony_session);
        $request_stack->push($request);

        /** @var Session $standard_session */
        $standard_session = $container->get(Session::class);

        $this->assertFalse($standard_session->has('a'));
        $this->assertNull($standard_session->get('a'));

        $standard_session->set('a', 'value of a');

        $this->assertTrue($standard_session->has('a'));
        $this->assertSame('value of a', $standard_session->get('a'));

        $standard_session->set('a', 'new value of a');

        $this->assertTrue($standard_session->has('a'));
        $this->assertSame('new value of a', $standard_session->get('a'));

        $standard_session->set('b', 'value of b');

        $this->assertTrue($standard_session->has('a'));
        $this->assertSame('new value of a', $standard_session->get('a'));
        $this->assertTrue($standard_session->has('b'));
        $this->assertSame('value of b', $standard_session->get('b'));

        $standard_session->delete('a');

        $this->assertFalse($standard_session->has('a'));
        $this->assertNull($standard_session->get('a'));
        $this->assertTrue($standard_session->has('b'));
        $this->assertSame('value of b', $standard_session->get('b'));
    }
}
