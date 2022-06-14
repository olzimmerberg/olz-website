<?php

declare(strict_types=1);

use Olz\Fetchers\TelegramFetcher;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \TelegramFetcher
 */
final class TelegramFetcherTest extends IntegrationTestCase {
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
