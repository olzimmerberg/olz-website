<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Common;

use Olz\Utils\DevDataUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * @internal
 *
 * @coversNothing
 */
class IntegrationTestCase extends KernelTestCase {
    private static bool $is_first_call = true;
    private static bool $is_db_locked = false;

    /** @var array<array{name: string, duration: float}> */
    protected static array $slowestTests = [];
    protected static int $numSlowestTests = 25;
    protected static bool $shutdownFunctionRegistered = false;

    /** @var array<string, mixed> */
    protected array $previous_server;
    protected float $setUpAt;

    protected Container $container;

    protected function setUp(): void {
        global $kernel, $_SERVER, $entityManager;
        $this->previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => realpath(__DIR__.'/../document-root/'),
            'HTTP_HOST' => 'integration-test.host',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (cloud; RiscV) Selenium (like Gecko)',
            'PHP_SELF' => 'fake-php-self',
        ];
        WithUtilsCache::reset();

        $kernel = self::bootKernel();
        $this->container = static::getContainer();
        // @phpstan-ignore-next-line
        $entityManager = $this->container->get('doctrine')->getManager();

        if ($this::$is_first_call) {
            $dev_data_utils = $this->getDevDataUtils();
            $dev_data_utils->setEnvUtils(new EnvUtils());
            $dev_data_utils->fullResetDb();
            $this::$is_first_call = false;
        }
        $this->resetLogs();

        $this->setUpAt = microtime(true);
    }

    protected function tearDown(): void {
        parent::tearDown();
        $_SERVER = $this->previous_server;

        $duration = microtime(true) - $this->setUpAt;
        self::$slowestTests[] = [
            'name' => $this->getName(),
            'duration' => $duration,
        ];
    }

    public static function tearDownAfterClass(): void {
        if (!self::$shutdownFunctionRegistered) {
            register_shutdown_function(function () {
                echo "Slowest tests:\n";
                usort(self::$slowestTests, function ($a, $b) {
                    return $a['duration'] < $b['duration'] ? 1 : -1;
                });
                for ($i = 0; $i < self::$numSlowestTests; $i++) {
                    $test = self::$slowestTests[$i] ?? [];
                    $name = $test['name'] ?? '-';
                    $duration = number_format($test['duration'] ?? 0, 2);
                    echo "{$name}: {$duration}s\n";
                }
            });
            self::$shutdownFunctionRegistered = true;
        }
    }

    protected function withLockedDb(callable $fn): void {
        $this->lockDb();
        try {
            $fn();
        } finally {
            $this->resetDbContent();
            $this->unlockDb();
        }
    }

    private function lockDb(): void {
        while ($this::$is_db_locked) {
            usleep(100 * 1000);
        }
        $this::$is_db_locked = true;
    }

    private function unlockDb(): void {
        $this::$is_db_locked = false;
    }

    protected function resetDbContent(): void {
        $dev_data_utils = $this->getDevDataUtils();
        $dev_data_utils->resetDbContent();
    }

    /** @return array<string> */
    protected function getLogs(?callable $formatter = null): array {
        $log_dir = self::$kernel?->getLogDir() ?? '';
        $log_file = "{$log_dir}test.log";
        $lines = explode("\n", @file_get_contents($log_file) ?: '');
        $format = $formatter ?? fn ($record, $level_name, $message) => "{$level_name} {$message}";
        $out = [];
        $channel_denylist = [
            'doctrine' => true,
            'messenger' => true,
        ];
        foreach ($lines as $line) {
            $record = json_decode($line, true);
            if (is_array($record) && !($channel_denylist[$record['channel']] ?? false)) {
                $out[] = $format($record, $record['level_name'], $record['message']);
            }
        }
        return $out;
    }

    protected function resetLogs(): void {
        $log_dir = self::$kernel?->getLogDir() ?? '';
        $log_file = "{$log_dir}test.log";
        if (is_file($log_file)) {
            unlink($log_file);
        }
    }

    protected function getDevDataUtils(): DevDataUtils {
        self::bootKernel();
        // @phpstan-ignore-next-line
        return self::getContainer()->get(DevDataUtils::class);
    }
}
