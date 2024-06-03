<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EnvUtils;

class FakeIntegrationTestEnvUtils extends EnvUtils {
    public static function fromEnv(): EnvUtils {
        // For this test, clear the "cache" always
        parent::$from_env_instance = null;
        return parent::fromEnv();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\EnvUtils
 */
final class EnvUtilsIntegrationTest extends IntegrationTestCase {
    public function testEnvUtilsFromEnv(): void {
        $env_utils = FakeIntegrationTestEnvUtils::fromEnv();
        $this->assertSame(
            realpath(__DIR__.'/../../../../../').'/',
            $env_utils->getPrivatePath()
        );
        $this->assertMatchesRegularExpression(
            '/\/tests\/IntegrationTests\/document\-root\/$/',
            $env_utils->getDataPath()
        );
        $this->assertSame('/', $env_utils->getDataHref());
        $this->assertSame(
            realpath(__DIR__.'/../../..').'/',
            $env_utils->getCodePath()
        );
        $this->assertSame('/', $env_utils->getCodeHref());
        $this->assertSame('http://integration-test.host', $env_utils->getBaseHref());
    }

    public function testEnvUtilsFromEnvWithinUnitTest(): void {
        $_SERVER = [
            'DOCUMENT_ROOT' => $this->previous_server['DOCUMENT_ROOT'] ?? 'test-no-root',
            'argv' => ['phpunit', 'tests/UnitTests'],
        ];

        try {
            FakeIntegrationTestEnvUtils::fromEnv();
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertMatchesRegularExpression(
                '/^Unit tests should never use EnvUtils::fromEnv!/',
                $exc->getMessage()
            );
        }
    }
}
