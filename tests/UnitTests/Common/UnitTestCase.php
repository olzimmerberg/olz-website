<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common;

use Olz\Tests\Fake;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\FakeLogHandler;
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
    protected $fakeLogHandler;

    protected static $slowestTests = [];
    protected static $numSlowestTests = 25;
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

        FakeEntity::reset();
        $logger = new \Monolog\Logger('Fake');
        $handler = new FakeLogHandler();
        $this->fakeLogHandler = $handler;
        $logger->pushHandler($handler);
        WithUtilsCache::setAll([
            'authUtils' => new Fake\FakeAuthUtils(),
            'dateUtils' => new FixedDateUtils('2020-03-13 19:30:00'),
            'devDataUtils' => new Fake\FakeDevDataUtils(),
            'dbUtils' => new Fake\FakeDbUtils(),
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
                    $name = $test['name'] ?? '';
                    $duration = number_format($test['duration'] ?? 0, 3);
                    echo "{$name}: {$duration}s\n";
                }
            });
            self::$shutdownFunctionRegistered = true;
        }
    }

    protected function getLogs(?callable $formatter = null): array {
        return $this->fakeLogHandler->getPrettyRecords($formatter);
    }

    protected function resetLogs(): void {
        $this->fakeLogHandler->resetRecords();
    }
}
