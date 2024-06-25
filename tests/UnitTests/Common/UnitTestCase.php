<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common;

use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\FakeAuthUtils;
use Olz\Tests\Fake\FakeDbUtils;
use Olz\Tests\Fake\FakeDevDataUtils;
use Olz\Tests\Fake\FakeEmailUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEntityUtils;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeGeneralUtils;
use Olz\Tests\Fake\FakeIdUtils;
use Olz\Tests\Fake\FakeImageUtils;
use Olz\Tests\Fake\FakeLogHandler;
use Olz\Tests\Fake\FakeSymfonyUtils;
use Olz\Tests\Fake\FakeTelegramUtils;
use Olz\Tests\Fake\FakeUploadUtils;
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

    /** @var array<string, mixed> */
    protected array $previous_server;
    protected float $setUpAt;
    protected FakeLogHandler $fakeLogHandler;

    /** @var array<array{name: string, duration: float}> */
    protected static array $slowestTests = [];
    protected static int $numSlowestTests = 25;
    protected static bool $shutdownFunctionRegistered = false;

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

        $env_utils = new FakeEnvUtils();
        $general_utils = new GeneralUtils();
        $data_path = $env_utils->getDataPath();
        $private_path = $env_utils->getPrivatePath();
        $general_utils->removeRecursive($data_path);
        mkdir($data_path);
        mkdir($private_path);

        FakeEntity::reset();
        $logger = new \Monolog\Logger('Fake');
        $handler = new FakeLogHandler();
        $this->fakeLogHandler = $handler;
        $logger->pushHandler($handler);
        WithUtilsCache::setAll([
            'authUtils' => new FakeAuthUtils(),
            'dateUtils' => new FixedDateUtils('2020-03-13 19:30:00'),
            'devDataUtils' => new FakeDevDataUtils(),
            'dbUtils' => new FakeDbUtils(),
            'emailUtils' => new FakeEmailUtils(),
            'entityManager' => new FakeEntityManager(),
            'entityUtils' => new FakeEntityUtils(),
            'envUtils' => new FakeEnvUtils(),
            'generalUtils' => new FakeGeneralUtils(),
            'idUtils' => new FakeIdUtils(),
            'imageUtils' => new FakeImageUtils(),
            'log' => $logger,
            'logger' => $logger,
            'symfonyUtils' => new FakeSymfonyUtils(),
            'telegramUtils' => new FakeTelegramUtils(),
            'uploadUtils' => new FakeUploadUtils(),
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

    /** @return array<string> */
    protected function getLogs(?callable $formatter = null): array {
        return $this->fakeLogHandler->getPrettyRecords($formatter);
    }

    protected function resetLogs(): void {
        $this->fakeLogHandler->resetRecords();
    }
}
