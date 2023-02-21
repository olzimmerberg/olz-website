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

        $this->assertSame(false, !$auth_utils);
    }

    public function testGenerateRandomToken(): void {
        $auth_utils = AuthUtils::fromEnv();

        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9\/\+]{24}$/',
            $auth_utils->generateRandomToken()
        );
    }

    public function testGenerateRandomTokenAithArg(): void {
        $auth_utils = AuthUtils::fromEnv();

        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9\/\+]{8}$/',
            $auth_utils->generateRandomToken(6)
        );
    }
}
