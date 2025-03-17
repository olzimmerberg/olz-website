<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\TelegramUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\TelegramUtils
 */
final class TelegramUtilsIntegrationTest extends IntegrationTestCase {
    public function testTelegramUtilsFromEnv(): void {
        $utils = $this->getSut();
        $this->assertSame('olz_bot', $utils->getBotName());
    }

    protected function getSut(): TelegramUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(TelegramUtils::class);
    }
}
