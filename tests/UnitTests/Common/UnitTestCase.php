<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common;

use Olz\Tests\Fake;
use Olz\Utils\GeneralUtils;
use PhpTypeScriptApi\Translator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UnitTestCase extends TestCase {
    private $previous_document_root;

    protected $previous_server;
    protected $setUpAt;

    protected static $slowestTestDuration = 0;
    protected static $slowestTestName;

    protected function setUp(): void {
        global $_SERVER;
        $this->previous_server = $_SERVER;
        $_SERVER = [];

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-CH,de';
        $translator = Translator::getInstance();
        $translator->setAcceptLangs($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        date_default_timezone_set('UTC');

        $general_utils = new GeneralUtils();
        $env_utils = new Fake\FakeEnvUtils();
        $data_path = $env_utils->getDataPath();
        $general_utils->removeRecursive($data_path);
        mkdir($data_path);

        Fake\FakeFactory::reset();

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
        $duration = number_format(self::$slowestTestDuration, 2);
        $name = self::$slowestTestName;
        echo "Slowest test ({$duration}s): {$name}\n";
    }
}
