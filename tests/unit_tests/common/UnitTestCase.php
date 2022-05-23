<?php

declare(strict_types=1);

use PhpTypescriptApi\Translator;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../../public/_/tools/common.php';
require_once __DIR__.'/../../fake/FakeFactory.php';
require_once __DIR__.'/../../fake/FakeEnvUtils.php';

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

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-CH,de';
        $translator = Translator::getInstance();
        $translator->setAcceptLangs($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        date_default_timezone_set('UTC');

        $env_utils = new FakeEnvUtils();
        $data_path = $env_utils->getDataPath();
        remove_r($data_path);
        mkdir($data_path);

        FakeFactory::reset();
    }

    protected function tearDown(): void {
        $_SERVER = $this->previous_server;
    }
}
