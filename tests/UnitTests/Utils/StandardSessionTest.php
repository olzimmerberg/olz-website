<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\StandardSession;

/**
 * @internal
 *
 * @covers \Olz\Utils\StandardSession
 */
final class StandardSessionTest extends UnitTestCase {
    public function testExists(): void {
        $this->assertTrue(class_exists(StandardSession::class));
    }

    public function testHas(): void {
        global $_SESSION;
        $_SESSION = [];
        $session = new StandardSession();
        $this->assertFalse($session->has('test'));
        $_SESSION['test'] = 'asdf';
        $this->assertTrue($session->has('test'));
    }

    public function testGet(): void {
        global $_SESSION;
        $_SESSION = [];
        $session = new StandardSession();
        $this->assertNull($session->get('test'));
        $_SESSION['test'] = 'asdf';
        $this->assertSame('asdf', $session->get('test'));
    }

    public function testSet(): void {
        global $_SESSION;
        $_SESSION = [];
        $session = new StandardSession();
        $this->assertFalse($session->has('test'));
        $session->set('test', 'asdf');
        $this->assertSame('asdf', $session->get('test'));
    }

    public function testDelete(): void {
        global $_SESSION;
        $_SESSION = ['test' => 'asdf'];
        $session = new StandardSession();
        $this->assertSame('asdf', $session->get('test'));
        $session->delete('test');
        $this->assertFalse($session->has('test'));
    }
}
