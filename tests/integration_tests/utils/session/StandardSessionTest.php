<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/utils/session/StandardSession.php';

global $standard_session;

$standard_session = new StandardSession();

/**
 * @internal
 * @covers \StandardSession
 */
final class StandardSessionTest extends TestCase {
    public function testStandardSession(): void {
        global $standard_session;

        $this->assertSame(false, $standard_session->has('a'));
        $this->assertSame(null, $standard_session->get('a'));

        $standard_session->set('a', 'value of a');

        $this->assertSame(true, $standard_session->has('a'));
        $this->assertSame('value of a', $standard_session->get('a'));

        $standard_session->set('a', 'new value of a');

        $this->assertSame(true, $standard_session->has('a'));
        $this->assertSame('new value of a', $standard_session->get('a'));

        $standard_session->set('b', 'value of b');

        $this->assertSame(true, $standard_session->has('a'));
        $this->assertSame('new value of a', $standard_session->get('a'));
        $this->assertSame(true, $standard_session->has('b'));
        $this->assertSame('value of b', $standard_session->get('b'));

        $standard_session->delete('a');

        $this->assertSame(false, $standard_session->has('a'));
        $this->assertSame(null, $standard_session->get('a'));
        $this->assertSame(true, $standard_session->has('b'));
        $this->assertSame('value of b', $standard_session->get('b'));
    }

    public function testStandardSessionCreate(): void {
        $this->expectException(Exception::class);
        session_write_close();

        new StandardSession();
    }
}
