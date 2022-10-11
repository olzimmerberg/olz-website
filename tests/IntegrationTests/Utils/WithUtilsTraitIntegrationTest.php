<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Utils;

use Olz\Entity\User;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;
use Olz\Utils\AuthUtils;
use Olz\Utils\EmailUtils;
use Olz\Utils\EntityUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\FixedDateUtils;
use Olz\Utils\GeneralUtils;
use Olz\Utils\IdUtils;
use Olz\Utils\StandardSession;
use Olz\Utils\StravaUtils;
use Olz\Utils\TelegramUtils;
use Olz\Utils\UploadUtils;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldUtils;

class WithUtilsTraitIntegrationClassWithUtilsTrait {
    use WithUtilsTrait;
}

/**
 * @internal
 *
 * @covers \Olz\Utils\WithUtilsTrait
 */
final class WithUtilsTraitIntegrationTest extends IntegrationTestCase {
    public function testCanSetAndGetAllUtils(): void {
        $all_utils = WithUtilsTraitIntegrationClassWithUtilsTrait::$ALL_UTILS;
        $check_by_util_name = [
            'authUtils' => function ($value) {
                return $value instanceof AuthUtils;
            },
            'dateUtils' => function ($value) {
                return $value instanceof FixedDateUtils;
            },
            'emailUtils' => function ($value) {
                return $value instanceof EmailUtils;
            },
            'entityManager' => function ($value) {
                try {
                    $value->getRepository(User::class);
                    return true;
                } catch (\Throwable $th) {
                    return false;
                }
            },
            'entityUtils' => function ($value) {
                return $value instanceof EntityUtils;
            },
            'envUtils' => function ($value) {
                return $value instanceof EnvUtils;
            },
            'fieldUtils' => function ($value) {
                return $value instanceof FieldUtils;
            },
            'generalUtils' => function ($value) {
                return $value instanceof GeneralUtils;
            },
            'getParams' => function ($value) {
                global $_GET;
                return $value === $_GET;
            },
            'idUtils' => function ($value) {
                return $value instanceof IdUtils;
            },
            'log' => function ($value) {
                return method_exists($value, 'warning');
            },
            'server' => function ($value) {
                global $_SERVER;
                return $value === $_SERVER;
            },
            'session' => function ($value) {
                return $value instanceof StandardSession;
            },
            'stravaUtils' => function ($value) {
                return $value instanceof StravaUtils;
            },
            'telegramUtils' => function ($value) {
                return $value instanceof TelegramUtils;
            },
            'uploadUtils' => function ($value) {
                return $value instanceof UploadUtils;
            },
        ];
        $instance = new WithUtilsTraitIntegrationClassWithUtilsTrait();
        $this->assertGreaterThan(0, count($all_utils));
        foreach ($all_utils as $util_name) {
            $value_from_env = $instance->{$util_name}();
            $check = $check_by_util_name[$util_name];
            $this->assertTrue(
                $check($value_from_env),
                "Check for {$util_name} did not pass",
            );
        }
    }
}
