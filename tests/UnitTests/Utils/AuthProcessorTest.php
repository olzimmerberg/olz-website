<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Monolog\Level;
use Monolog\LogRecord;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AuthProcessor;
use Olz\Utils\MemorySession;

/**
 * @internal
 *
 * @covers \Olz\Utils\AuthProcessor
 */
final class AuthProcessorTest extends UnitTestCase {
    public function testWorksWhenAuthenticated(): void {
        $auth_processor = new AuthProcessor();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'child',
            'auth_user' => 'parent',
        ];
        $this->setSession($session);
        $fake_log_record = new LogRecord(new \DateTimeImmutable('now'), 'channel', Level::Info, "Message", ['con' => 'text']);

        $auth_processor($fake_log_record);

        $this->assertSame([
            'user' => 'child',
            'auth_user' => 'parent',
        ], $fake_log_record->extra);
    }

    public function testWorksWhenAnonymous(): void {
        $auth_processor = new AuthProcessor();
        $session = new MemorySession();
        $session->session_storage = [];
        $this->setSession($session);
        $fake_log_record = new LogRecord(new \DateTimeImmutable('now'), 'channel', Level::Info, "Message", ['con' => 'text']);

        $auth_processor($fake_log_record);

        $this->assertSame([
            'user' => null,
            'auth_user' => null,
        ], $fake_log_record->extra);
    }
}
