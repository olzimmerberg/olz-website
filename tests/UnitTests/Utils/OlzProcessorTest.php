<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Monolog\Level;
use Monolog\LogRecord;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\OlzProcessor;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Utils\OlzProcessor
 */
final class OlzProcessorTest extends UnitTestCase {
    public function testOlzProcessorMaximal(): void {
        $processor = new OlzProcessor();
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'child',
            'auth_user' => 'parent',
        ];
        $this->setServer([
            'REQUEST_URI' => '/path?access_token=ABC_-def/+123',
            'HTTP_USER_AGENT' => 'user-agent',
            'HTTP_REFERER' => 'https://olzimmerberg.ch/page',
            'APP_SECRET' => 'verysecretappsecretohsoprivate',
        ]);
        $fake_log_record = new LogRecord(
            new \DateTimeImmutable('now'),
            'app',
            Level::Info,
            "Leaking verysecretappsecretohsoprivate message",
            ['con' => 'text'],
        );

        $result = $processor($fake_log_record);

        $this->assertSame([
            'url' => '/path?access_token=ABC***123',
            'referrer' => 'https://olzimmerberg.ch/page',
            'user_agent' => 'user-agent',
            'user' => 'child',
            'auth_user' => 'parent',
        ], $result->extra);
        $this->assertSame('OlzProcessorTest', $result->channel);
        $this->assertSame('Leaking ***APP_SECRET*** message', $result->message);
    }

    public function testOlzProcessorMinimal(): void {
        $processor = new OlzProcessor();
        WithUtilsCache::get('session')->session_storage = [];
        $fake_log_record = new LogRecord(
            new \DateTimeImmutable('now'),
            '',
            Level::Info,
            "Leaking verysecretappsecretohsoprivate message",
            ['con' => 'text'],
        );

        $result = $processor($fake_log_record);

        $this->assertSame([
            'url' => null,
            'referrer' => null,
            'user_agent' => null,
            'user' => null,
            'auth_user' => null,
        ], $result->extra);
        $this->assertSame('OlzProcessorTest', $result->channel);
        // The APP_SECRET is not defined in this test
        $this->assertSame('Leaking verysecretappsecretohsoprivate message', $result->message);
    }

    public function testOlzProcessorNonApp(): void {
        $processor = new OlzProcessor();
        WithUtilsCache::get('session')->session_storage = [
            'user' => 'admin',
            'auth_user' => 'admin',
        ];
        $this->setServer([
            'REQUEST_URI' => '/path?access_token=ABC_-def/+123',
            'HTTP_USER_AGENT' => 'user-agent',
            'HTTP_REFERER' => 'https://olzimmerberg.ch/page',
            'APP_SECRET' => 'verysecretappsecretohsoprivate',
        ]);
        $fake_log_record = new LogRecord(
            new \DateTimeImmutable('now'),
            'non-app',
            Level::Info,
            "Leaking verysecretappsecretohsoprivate message",
            ['con' => 'text'],
        );

        $result = $processor($fake_log_record);

        $this->assertSame([
            'url' => '/path?access_token=ABC***123',
            'referrer' => 'https://olzimmerberg.ch/page',
            'user_agent' => 'user-agent',
            'user' => 'admin',
            'auth_user' => 'admin',
        ], $result->extra);
        $this->assertSame('non-app', $result->channel);
        $this->assertSame('Leaking ***APP_SECRET*** message', $result->message);
    }
}
