<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 * @coversNothing
 */
class IntegrationTestCase extends KernelTestCase {
    private $previous_document_root;
    private static $is_first_call = true;

    protected function setUp(): void {
        global $_SERVER, $entityManager;
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

        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void {
        $_SERVER = $this->previous_server;
    }

    protected function resetDb(): void {
        global $db, $data_path;
        require_once __DIR__.'/../../../_/config/database.php';
        require_once __DIR__.'/../../../_/config/paths.php';
        require_once __DIR__.'/../../../_/tools/dev_data.php';

        reset_db($db, $data_path);
    }
}
