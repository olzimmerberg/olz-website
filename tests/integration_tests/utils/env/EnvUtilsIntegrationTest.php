<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../../src/utils/env/EnvUtils.php';

class FakeEnvUtils extends EnvUtils {
    public static function fromEnv() {
        // For this test, clear the "cache" always
        parent::$from_env_instance = null;
        return parent::fromEnv();
    }
}

/**
 * @internal
 * @covers \EnvUtils
 */
final class EnvUtilsIntegrationTest extends TestCase {
    public function testEnvUtilsFromEnv(): void {
        global $_SERVER;
        $env_utils = FakeEnvUtils::fromEnv();
        $this->assertMatchesRegularExpression(
            '/\/tests\/integration_tests\/document\-root\/$/',
            $env_utils->getDataPath()
        );
        $this->assertSame('/', $env_utils->getDataHref());
        $this->assertMatchesRegularExpression(
            '/\/src\/utils\/$/',
            $env_utils->getCodePath()
        );
        $this->assertSame('/_/', $env_utils->getCodeHref());
        $this->assertMatchesRegularExpression(
            '/\/tests\/integration_tests\/document\-root\/deploy\/$/',
            $env_utils->getDeployPath()
        );
        $this->assertSame('/deploy/', $env_utils->getDeployHref());
        $this->assertSame('http://fake-host', $env_utils->getBaseHref());
    }

    public function testEnvUtilsFromEnvWithMissingConfigFile(): void {
        global $_SERVER;
        $previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => __DIR__, // no config file in here.
        ];

        try {
            $env_utils = FakeEnvUtils::fromEnv();
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Konfigurationsdatei nicht gefunden!', $exc->getMessage());
        }

        $_SERVER = $previous_server;
    }

    public function testEnvUtilsGetConfigPathWithNoDocumentRoot(): void {
        global $_SERVER;
        $previous_server = $_SERVER;
        $_SERVER = []; // e.g. for doctrine cli-config.php

        $config_path = FakeEnvUtils::getConfigPath();

        $this->assertMatchesRegularExpression(
            '/\/src\/utils\/env\/\.\.\/\.\.\/\.\.\/dev-server\/config.php$/',
            $config_path
        );

        $_SERVER = $previous_server;
    }

    public function testEnvUtilsFromEnvWithinUnitTest(): void {
        global $_SERVER;
        $previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => $previous_server['DOCUMENT_ROOT'] ?? 'test-no-root',
            'argv' => ['phpunit', 'tests/unit_tests'],
        ];

        try {
            $env_utils = FakeEnvUtils::fromEnv();
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertSame('Unit tests should never use EnvUtils::fromEnv!', $exc->getMessage());
        }

        $_SERVER = $previous_server;
    }
}
