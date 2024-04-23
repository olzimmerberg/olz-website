<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Monolog\Logger;
use Olz\Fetchers\SolvFetcher;
use Olz\Tests\Fake\FakeDevDataUtils;
use Olz\Tests\Fake\FakeEntityManager;
use Olz\Tests\Fake\FakeEntityUtils;
use Olz\Tests\Fake\FakeEnvUtils;
use Olz\Tests\Fake\FakeIdUtils;
use Olz\Tests\Fake\FakeSymfonyUtils;
use Olz\Tests\Fake\FakeTelegramUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\AuthUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EmailUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\GeneralUtils;
use Olz\Utils\HtmlUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\ImageUtils;
use Olz\Utils\MapUtils;
use Olz\Utils\MemorySession;
use Olz\Utils\StravaUtils;
use Olz\Utils\UploadUtils;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldUtils;

class WithUtilsTraitClassWithUtilsTrait {
    use WithUtilsTrait;
}

/**
 * @internal
 *
 * @coversNothing
 */
class BrokenTestCase extends \Exception {
}

/**
 * @internal
 *
 * @covers \Olz\Utils\WithUtilsTrait
 */
final class WithUtilsTraitTest extends UnitTestCase {
    public function testCanSetAndGetAllUtils(): void {
        $get_instance_by_util_name = [
            'authUtils' => function () { return new AuthUtils(); },
            'dateUtils' => function () { return new FixedDateUtils('2020-03-13'); },
            'dbUtils' => function () { return new DbUtils(); },
            'devDataUtils' => function () { return new FakeDevDataUtils(); },
            'emailUtils' => function () { return new EmailUtils(); },
            'entityManager' => function () { return new FakeEntityManager(); },
            'entityUtils' => function () { return new FakeEntityUtils(); },
            'envUtils' => function () { return new FakeEnvUtils(); },
            'fieldUtils' => function () { return new FieldUtils(); },
            'fileUtils' => function () { return new FileUtils(); },
            'generalUtils' => function () { return new GeneralUtils(); },
            'getParams' => function () { return []; },
            'htmlUtils' => function () { return new HtmlUtils(); },
            'httpUtils' => function () { return new HttpUtils(); },
            'idUtils' => function () { return new FakeIdUtils(); },
            'imageUtils' => function () { return new ImageUtils(); },
            'log' => function () { return new Logger('fake'); },
            'mapUtils' => function () { return new MapUtils(); },
            'server' => function () { throw new BrokenTestCase(); },
            'session' => function () { return new MemorySession(); },
            'solvFetcher' => function () { return new SolvFetcher(); },
            'stravaUtils' => function () { return new StravaUtils(); },
            'symfonyUtils' => function () { return new FakeSymfonyUtils(); },
            'telegramUtils' => function () { return new FakeTelegramUtils(); },
            'uploadUtils' => function () { return new UploadUtils(); },
        ];
        $all_utils = WithUtilsTraitClassWithUtilsTrait::$ALL_UTILS;
        $instance = new WithUtilsTraitClassWithUtilsTrait();
        $this->assertGreaterThan(0, count($all_utils));
        foreach ($all_utils as $util_name) {
            $cap_util_name = ucfirst($util_name);
            $setter_name = "set{$cap_util_name}";
            try {
                $util_instance = $get_instance_by_util_name[$util_name]();
                $instance->{$setter_name}($util_instance);
                $this->assertSame($util_instance, $instance->{$util_name}());
            } catch (BrokenTestCase $exc) {
                // ignore
            }
        }
    }
}
