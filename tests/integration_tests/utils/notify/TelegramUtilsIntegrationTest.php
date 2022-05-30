<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/utils/notify/TelegramUtils.php';
require_once __DIR__.'/../../common/IntegrationTestCase.php';

/**
 * @internal
 * @covers \TelegramUtils
 */
final class TelegramUtilsIntegrationTest extends IntegrationTestCase {
    public function testTelegramUtilsFromEnv(): void {
        $telegram_utils = TelegramUtils::fromEnv();
        $this->assertSame('olz_bot', $telegram_utils->getBotName());
    }
}
