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

    protected function setUp(): void {
        global $_SERVER;
        $this->previous_server = $_SERVER;
        $_SERVER = [
            'DOCUMENT_ROOT' => realpath(__DIR__.'/../document-root/'),
            'HTTP_HOST' => 'integration-test.host',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (cloud; RiscV) Selenium (like Gecko)',
        ];
    }

    protected function tearDown(): void {
        $_SERVER = $this->previous_server;
    }
}
