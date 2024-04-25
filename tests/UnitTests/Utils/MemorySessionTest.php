<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;

/**
 * @internal
 *
 * @covers \Olz\Utils\MemorySession
 */
final class MemorySessionTest extends UnitTestCase {
    public function testMemorySessionInit(): void {
        $session = new MemorySession();

        $this->assertFalse($session->has('a'));
        $this->assertNull($session->get('a'));
    }

    public function testMemorySessionSetValue(): void {
        $session = new MemorySession();

        $session->set('a', 'value of a');

        $this->assertTrue($session->has('a'));
        $this->assertSame('value of a', $session->get('a'));
    }

    public function testMemorySessionOverwriteValue(): void {
        $session = new MemorySession();
        $session->set('a', 'old value of a');

        $session->set('a', 'new value of a');

        $this->assertTrue($session->has('a'));
        $this->assertSame('new value of a', $session->get('a'));
    }

    public function testMemorySessionSetDifferentKeys(): void {
        $session = new MemorySession();
        $session->set('a', 'value of a');

        $session->set('b', 'value of b');

        $this->assertTrue($session->has('a'));
        $this->assertSame('value of a', $session->get('a'));
        $this->assertTrue($session->has('b'));
        $this->assertSame('value of b', $session->get('b'));
    }

    public function testMemorySessionDeleteValue(): void {
        $session = new MemorySession();
        $session->set('a', 'value of a');

        $session->delete('a');

        $this->assertFalse($session->has('a'));
        $this->assertNull($session->get('a'));
    }

    public function testMemorySessionClear(): void {
        $session = new MemorySession();
        $session->set('a', 'value of a');

        $session->clear();

        $this->assertFalse($session->has('a'));
        $this->assertNull($session->get('a'));
        $this->assertTrue($session->cleared);
    }
}
