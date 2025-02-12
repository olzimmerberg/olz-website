<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\RecaptchaUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\RecaptchaUtils
 */
final class RecaptchaUtilsIntegrationTest extends IntegrationTestCase {
    public function testRecaptchaUtilsFromEnv(): void {
        $recaptcha_utils = RecaptchaUtils::fromEnv();

        $this->assertSame(RecaptchaUtils::class, get_class($recaptcha_utils));
    }
}
