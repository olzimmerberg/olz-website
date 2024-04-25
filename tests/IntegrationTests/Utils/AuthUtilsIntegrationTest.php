<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\AuthUtils;

/**
 * @internal
 *
 * @covers \Olz\Utils\AuthUtils
 */
final class AuthUtilsIntegrationTest extends IntegrationTestCase {
    public function testAuthUtilsFromEnv(): void {
        $auth_utils = AuthUtils::fromEnv();

        $this->assertTrue($auth_utils instanceof AuthUtils);
    }
}
