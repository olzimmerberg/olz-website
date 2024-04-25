<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\StandardSession;

/**
 * @internal
 *
 * @covers \Olz\Utils\StandardSession
 */
final class StandardSessionIntegrationTest extends IntegrationTestCase {
    public function testStandardSession(): void {
        $standard_session = new StandardSession();

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
