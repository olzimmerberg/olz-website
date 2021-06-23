<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \MemorySession
 */
final class MemorySessionTest extends UnitTestCase {
    public function testMemorySessionInit(): void {
        $session = new MemorySession();

        $this->assertSame(false, $session->has('a'));
        $this->assertSame(null, $session->get('a'));
    }

    public function testMemorySessionSetValue(): void {
        $session = new MemorySession();

        $session->set('a', 'value of a');

        $this->assertSame(true, $session->has('a'));
        $this->assertSame('value of a', $session->get('a'));
    }

    public function testMemorySessionOverwriteValue(): void {
        $session = new MemorySession();
        $session->set('a', 'old value of a');

        $session->set('a', 'new value of a');

        $this->assertSame(true, $session->has('a'));
        $this->assertSame('new value of a', $session->get('a'));
    }

    public function testMemorySessionSetDifferentKeys(): void {
        $session = new MemorySession();
        $session->set('a', 'value of a');

        $session->set('b', 'value of b');

        $this->assertSame(true, $session->has('a'));
        $this->assertSame('value of a', $session->get('a'));
        $this->assertSame(true, $session->has('b'));
        $this->assertSame('value of b', $session->get('b'));
    }

    public function testMemorySessionDeleteValue(): void {
        $session = new MemorySession();
        $session->set('a', 'value of a');

        $session->delete('a');

        $this->assertSame(false, $session->has('a'));
        $this->assertSame(null, $session->get('a'));
    }

    public function testMemorySessionClear(): void {
        $session = new MemorySession();
        $session->set('a', 'value of a');

        $session->clear();

        $this->assertSame(false, $session->has('a'));
        $this->assertSame(null, $session->get('a'));
        $this->assertSame(true, $session->cleared);
    }
}
