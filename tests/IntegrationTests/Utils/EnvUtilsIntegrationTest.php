<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\EnvUtils;

class FakeIntegrationTestEnvUtils extends EnvUtils {
    public static function fromEnv() {
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
        global $db;
        $env_utils = FakeIntegrationTestEnvUtils::fromEnv();
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

    public function testEnvUtilsFromEnvGetLogger(): void {
        $env_utils = FakeIntegrationTestEnvUtils::fromEnv();
        $data_path = $env_utils->getDataPath();
        $logs_path = "{$data_path}logs/";
        if (is_dir($logs_path)) {
            foreach (scandir($logs_path) as $entry) {
                if ($entry != '.' && $entry != '..') {
                    unlink("{$logs_path}{$entry}");
                }
            }
            rmdir($logs_path);
        }
        $this->assertSame(false, is_dir($logs_path));

        $logger = $env_utils->getLogsUtils()->getLogger('test');
        $logger->debug('just for test');

        $this->assertSame('test', $logger->getName());
        $this->assertSame(true, is_dir($data_path));
        $this->assertSame(true, is_dir($logs_path));
        $this->assertMatchesRegularExpression(
            '/^merged\\-[0-9]{4}\\-[0-9]{2}\\-[0-9]{2}\\.log$/',
            scandir($logs_path)[2]
        );
    }

    public function testEnvUtilsFromEnvWithMissingConfigFile(): void {
        global $_SERVER;
        $previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => __DIR__, // no config file in here.
        ];

        try {
            $env_utils = FakeIntegrationTestEnvUtils::fromEnv();
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

        $config_path = FakeIntegrationTestEnvUtils::getConfigPath();

        $this->assertMatchesRegularExpression(
            '/\/\.\.\/\.\.\/public\/config.php$/',
            $config_path
        );

        $_SERVER = $previous_server;
    }

    public function testEnvUtilsFromEnvWithinUnitTest(): void {
        global $_SERVER;
        $previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => $previous_server['DOCUMENT_ROOT'] ?? 'test-no-root',
            'argv' => ['phpunit', 'tests/UnitTests'],
        ];

        try {
            $env_utils = FakeIntegrationTestEnvUtils::fromEnv();
            $this->fail('Error expected');
        } catch (\Exception $exc) {
            $this->assertMatchesRegularExpression(
                '/^Unit tests should never use EnvUtils::fromEnv!/',
                $exc->getMessage()
            );
        }

        $_SERVER = $previous_server;
    }
}
