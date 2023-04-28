<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Common;

use Olz\Utils\DevDataUtils;
use Olz\Utils\WithUtilsCache;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class IntegrationTestCase extends KernelTestCase {
    private static $is_first_call = true;
    private static $is_db_locked = false;

    private $previous_document_root;

    protected static $slowestTestDuration = 0;
    protected static $slowestTestName;
    protected static $shutdownFunctionRegistered = false;

    protected $previous_server;
    protected $setUpAt;

    protected function setUp(): void {
        global $_SERVER, $entityManager;
        $this->previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => realpath(__DIR__.'/../document-root/'),
            'HTTP_HOST' => 'integration-test.host',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (cloud; RiscV) Selenium (like Gecko)',
            'PHP_SELF' => 'fake-php-self',
        ];
        WithUtilsCache::reset();
        if ($this::$is_first_call) {
            $dev_data_utils = DevDataUtils::fromEnv();
            $dev_data_utils->fullResetDb();
            $this::$is_first_call = false;
        }

        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->setUpAt = microtime(true);
    }

    protected function tearDown(): void {
        $_SERVER = $this->previous_server;

        $duration = microtime(true) - $this->setUpAt;
        if ($duration > self::$slowestTestDuration) {
            self::$slowestTestDuration = $duration;
            self::$slowestTestName = $this->getName();
        }
    }

    public static function tearDownAfterClass(): void {
        if (!self::$shutdownFunctionRegistered) {
            register_shutdown_function(function () {
                $duration = number_format(self::$slowestTestDuration, 2);
                $name = self::$slowestTestName;
                echo "Slowest test ({$duration}s): {$name}\n";
            });
            self::$shutdownFunctionRegistered = true;
        }
    }

    protected function withLockedDb($fn): void {
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
        $dev_data_utils = DevDataUtils::fromEnv();
        $dev_data_utils->resetDbContent();
    }
}
