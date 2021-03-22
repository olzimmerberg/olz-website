<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../src/config/vendor/autoload.php';

/**
 * @internal
 * @coversNothing
 */
class UnitTestCase extends TestCase {
    private $previous_document_root;

    protected function setUp(): void {
        global $_SERVER;
        $this->previous_server = $_SERVER;
        $_SERVER = [];

        date_default_timezone_set('UTC');
    }

    protected function tearDown(): void {
        $_SERVER = $this->previous_server;
    }
}
