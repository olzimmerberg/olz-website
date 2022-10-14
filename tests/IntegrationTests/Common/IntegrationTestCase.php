<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Common;

use Olz\Utils\DevDataUtils;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class IntegrationTestCase extends KernelTestCase {
    private $previous_document_root;
    private static $is_first_call = true;
    private static $is_db_locked = false;

    protected function setUp(): void {
        global $_SERVER, $entityManager;
        $this->previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => realpath(__DIR__.'/../document-root/'),
            'HTTP_HOST' => 'integration-test.host',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (cloud; RiscV) Selenium (like Gecko)',
        ];
        if ($this::$is_first_call) {
            $dev_data_utils = DevDataUtils::fromEnv();
            $dev_data_utils->fullResetDb();
            $this::$is_first_call = false;
        }

        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void {
        $_SERVER = $this->previous_server;
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
