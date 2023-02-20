<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Fetchers;

use Olz\Fetchers\TelegramFetcher;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\Fetchers\TelegramFetcher
 */
final class TelegramFetcherTest extends IntegrationTestCase {
    protected $telegramFetcher;

    public function __construct() {
        parent::__construct();
        $this->telegramFetcher = new TelegramFetcher();
    }

    public function testCallTelegramApiWithoutToken(): void {
        $content = $this->telegramFetcher->callTelegramApi('test', [], '');
        $this->assertSame([
            'ok' => false,
            'error_code' => 404,
            'description' => 'Not Found',
        ], $content);
    }

    public function testCallTelegramApiWithInvalidToken(): void {
        $content = $this->telegramFetcher->callTelegramApi('test', [], 'invalid-token');
        $this->assertSame([
            'ok' => false,
            'error_code' => 404,
            'description' => 'Not Found',
        ], $content);
    }
}
