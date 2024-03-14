<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Monolog\Level;
use Monolog\LogRecord;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use Olz\Utils\OlzProcessor;

/**
 * @internal
 *
 * @covers \Olz\Utils\OlzProcessor
 */
final class OlzProcessorTest extends UnitTestCase {
    public function testOlzProcessorMaximal(): void {
        $auth_processor = new OlzProcessor();
        $session = new MemorySession();
        $session->session_storage = [
            'user' => 'child',
            'auth_user' => 'parent',
        ];
        $this->setSession($session);
        $this->setServer([
            'REQUEST_URI' => '/path',
            'HTTP_USER_AGENT' => 'user-agent',
            'HTTP_REFERER' => 'https://olzimmerberg.ch/page',
        ]);
        $fake_log_record = new LogRecord(new \DateTimeImmutable('now'), 'channel', Level::Info, "Message", ['con' => 'text']);

        $auth_processor($fake_log_record);

        $this->assertSame([
            'url' => '/path',
            'referrer' => 'https://olzimmerberg.ch/page',
            'user_agent' => 'user-agent',
            'user' => 'child',
            'auth_user' => 'parent',
        ], $fake_log_record->extra);
    }

    public function testOlzProcessorMinimal(): void {
        $auth_processor = new OlzProcessor();
        $session = new MemorySession();
        $session->session_storage = [];
        $this->setSession($session);
        $fake_log_record = new LogRecord(new \DateTimeImmutable('now'), 'channel', Level::Info, "Message", ['con' => 'text']);

        $auth_processor($fake_log_record);

        $this->assertSame([
            'url' => null,
            'referrer' => null,
            'user_agent' => null,
            'user' => null,
            'auth_user' => null,
        ], $fake_log_record->extra);
    }
}
