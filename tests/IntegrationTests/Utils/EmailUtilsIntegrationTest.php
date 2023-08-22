<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EmailUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\EmailUtils
 */
final class EmailUtilsIntegrationTest extends IntegrationTestCase {
    public function testEmailUtilsFromEnv(): void {
        $email_utils = EmailUtils::fromEnv();
        $this->assertSame(true, (bool) $email_utils);
    }
}
