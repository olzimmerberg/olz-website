<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/config/vendor/autoload.php';

/**
 * @internal
 * @coversNothing
 */
class IntegrationTestCase extends TestCase {
    private $previous_document_root;
    private static $is_first_call = true;

    protected function setUp(): void {
        global $_SERVER;
        $this->previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => realpath(__DIR__.'/../document-root/'),
            'HTTP_HOST' => 'integration-test.host',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (cloud; RiscV) Selenium (like Gecko)',
        ];
        if ($this::$is_first_call) {
            $this->resetDb();
            $this::$is_first_call = false;
        }
    }

    protected function tearDown(): void {
        $_SERVER = $this->previous_server;
    }

    protected function resetDb(): void {
        global $db, $data_path;
        require_once __DIR__.'/../../../src/config/database.php';
        require_once __DIR__.'/../../../src/config/paths.php';
        require_once __DIR__.'/../../../src/tools/dev_data.php';

        reset_db($db, $data_path);
    }
}
