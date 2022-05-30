<?php

declare(strict_types=1);

use PhpTypeScriptApi\Fields\FieldUtils;

require_once __DIR__.'/../../../_/config/vendor/autoload.php';
require_once __DIR__.'/../../../_/utils/auth/AuthUtils.php';
require_once __DIR__.'/../../../_/utils/auth/StravaUtils.php';
require_once __DIR__.'/../../../_/utils/date/FixedDateUtils.php';
require_once __DIR__.'/../../../_/utils/env/EnvUtils.php';
require_once __DIR__.'/../../../_/utils/notify/EmailUtils.php';
require_once __DIR__.'/../../../_/utils/notify/TelegramUtils.php';
require_once __DIR__.'/../../../_/utils/session/StandardSession.php';
require_once __DIR__.'/../../../_/utils/EntityUtils.php';
require_once __DIR__.'/../../../_/utils/GeneralUtils.php';
require_once __DIR__.'/../../../_/utils/IdUtils.php';
require_once __DIR__.'/../../../_/utils/UploadUtils.php';
require_once __DIR__.'/../../../_/utils/WithUtilsTrait.php';
require_once __DIR__.'/../common/IntegrationTestCase.php';

class WithUtilsTraitIntegrationClassWithUtilsTrait {
    use WithUtilsTrait;
}

/**
 * @internal
 * @covers \WithUtilsTrait
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
                    $value->getRepository('User');
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
            'logger' => function ($value) {
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
            $cap_util_name = ucfirst($util_name);
            $getter_name = "get{$cap_util_name}";
            $value_from_env = $instance->{$getter_name}();
            $check = $check_by_util_name[$util_name];
            $this->assertTrue(
                $check($value_from_env),
                "Check for {$util_name} did not pass",
            );
        }
    }
}
