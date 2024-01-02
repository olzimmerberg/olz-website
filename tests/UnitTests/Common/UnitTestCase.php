<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common;

use Olz\Tests\Fake;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\GeneralUtils;
use Olz\Utils\WithUtilsCache;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Translator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class UnitTestCase extends TestCase {
    use WithUtilsTrait;

    private $previous_document_root;

    protected $previous_server;
    protected $setUpAt;

    protected static $slowestTestDuration = 0;
    protected static $slowestTestName;
    protected static $shutdownFunctionRegistered = false;

    protected function setUp(): void {
        global $_SERVER;
        $this->previous_server = $_SERVER;
        $_SERVER = [];

        $_SERVER['argv'] = $this->previous_server['argv'];
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-CH,de';
        $_SERVER['PHP_SELF'] = 'fake-php-self';
        $translator = Translator::getInstance();
        $translator->setAcceptLangs($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        date_default_timezone_set('UTC');

        $env_utils = new Fake\FakeEnvUtils();
        $general_utils = new GeneralUtils();
        $data_path = $env_utils->getDataPath();
        $general_utils->removeRecursive($data_path);
        mkdir($data_path);

        Fake\FakeFactory::reset();
        $logger = Fake\FakeLogger::create();
        WithUtilsCache::setAll([
            'authUtils' => new Fake\FakeAuthUtils(),
            'dateUtils' => new FixedDateUtils('2020-03-13 19:30:00'),
            'devDataUtils' => new Fake\FakeDevDataUtils(),
            'emailUtils' => new Fake\FakeEmailUtils(),
            'entityManager' => new Fake\FakeEntityManager(),
            'entityUtils' => new Fake\FakeEntityUtils(),
            'envUtils' => new Fake\FakeEnvUtils(),
            'generalUtils' => new Fake\DeterministicGeneralUtils(),
            'idUtils' => new Fake\FakeIdUtils(),
            'log' => $logger,
            'logger' => $logger,
            'symfonyUtils' => new Fake\FakeSymfonyUtils(),
            'telegramUtils' => new Fake\FakeTelegramUtils(),
            'uploadUtils' => new Fake\DeterministicUploadUtils(),
        ]);

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

    protected function getLogs(callable $formatter = null): array {
        return WithUtilsCache::get('log')->handler->getPrettyRecords($formatter);
    }
}
