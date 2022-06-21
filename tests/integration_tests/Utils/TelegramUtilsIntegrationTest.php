<?php

declare(strict_types=1);

use Olz\Utils\TelegramUtils;

require_once __DIR__.'/../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \Olz\Utils\TelegramUtils
 */
final class TelegramUtilsIntegrationTest extends IntegrationTestCase {
    public function testTelegramUtilsFromEnv(): void {
        $telegram_utils = TelegramUtils::fromEnv();
        $this->assertSame('olz_bot', $telegram_utils->getBotName());
    }
}
