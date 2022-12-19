<?php

namespace Olz\Api;

use Olz\Apps\OlzApps;
use PhpTypeScriptApi\Api;

// Needed because this file can be called directly.
require_once __DIR__.'/../../vendor/autoload.php';

class OlzApi extends Api {
    public static function generate() {
        $olz_api = self::getInstance();

        file_put_contents(
            __DIR__.'/client/generated_olz_api_types.ts',
            $olz_api->getTypeScriptDefinition('OlzApi')
        );

        echo "\nOLZ API client generated.\n";
    }

    public static function getInstance() {
        $olz_api = new self();

        $olz_api->registerEndpoint('onDaily', function () {
            return new Endpoints\OnDailyEndpoint();
        });
        $olz_api->registerEndpoint('onContinuously', function () {
            return new Endpoints\OnContinuouslyEndpoint();
        });
        $olz_api->registerEndpoint('login', function () {
            return new Endpoints\LoginEndpoint();
        });
        $olz_api->registerEndpoint('resetPassword', function () {
            return new Endpoints\ResetPasswordEndpoint();
        });
        $olz_api->registerEndpoint('logout', function () {
            return new Endpoints\LogoutEndpoint();
        });
        $olz_api->registerEndpoint('getAuthenticatedUser', function () {
            return new Endpoints\GetAuthenticatedUserEndpoint();
        });
        $olz_api->registerEndpoint('getAuthenticatedRoles', function () {
            return new Endpoints\GetAuthenticatedRolesEndpoint();
        });
        $olz_api->registerEndpoint('updateUser', function () {
            return new Endpoints\UpdateUserEndpoint();
        });
        $olz_api->registerEndpoint('verifyUserEmail', function () {
            return new Endpoints\VerifyUserEmailEndpoint();
        });
        $olz_api->registerEndpoint('updatePassword', function () {
            return new Endpoints\UpdateUserPasswordEndpoint();
        });
        $olz_api->registerEndpoint('signUpWithPassword', function () {
            return new Endpoints\SignUpWithPasswordEndpoint();
        });
        $olz_api->registerEndpoint('loginWithStrava', function () {
            return new Endpoints\LoginWithStravaEndpoint();
        });
        $olz_api->registerEndpoint('signUpWithStrava', function () {
            return new Endpoints\SignUpWithStravaEndpoint();
        });
        $olz_api->registerEndpoint('deleteUser', function () {
            return new Endpoints\DeleteUserEndpoint();
        });
        $olz_api->registerEndpoint('executeEmailReaction', function () {
            return new Endpoints\ExecuteEmailReactionEndpoint();
        });
        $olz_api->registerEndpoint('linkTelegram', function () {
            return new Endpoints\LinkTelegramEndpoint();
        });
        $olz_api->registerEndpoint('onTelegram', function () {
            return new Endpoints\OnTelegramEndpoint();
        });
        $olz_api->registerEndpoint('updateOlzText', function () {
            return new Endpoints\UpdateOlzTextEndpoint();
        });
        $olz_api->registerEndpoint('startUpload', function () {
            return new Endpoints\StartUploadEndpoint();
        });
        $olz_api->registerEndpoint('updateUpload', function () {
            return new Endpoints\UpdateUploadEndpoint();
        });
        $olz_api->registerEndpoint('finishUpload', function () {
            return new Endpoints\FinishUploadEndpoint();
        });

        // News

        $olz_api->registerEndpoint('createNews', function () {
            return new \Olz\News\Endpoints\CreateNewsEndpoint();
        });
        $olz_api->registerEndpoint('getNews', function () {
            return new \Olz\News\Endpoints\GetNewsEndpoint();
        });
        $olz_api->registerEndpoint('editNews', function () {
            return new \Olz\News\Endpoints\EditNewsEndpoint();
        });
        $olz_api->registerEndpoint('updateNews', function () {
            return new \Olz\News\Endpoints\UpdateNewsEndpoint();
        });
        $olz_api->registerEndpoint('deleteNews', function () {
            return new \Olz\News\Endpoints\DeleteNewsEndpoint();
        });

        // Startseite

        $olz_api->registerEndpoint('createWeeklyPicture', function () {
            return new \Olz\Startseite\Endpoints\CreateWeeklyPictureEndpoint();
        });

        OlzApps::registerAllEndpoints($olz_api);

        return $olz_api;
    }
}

// @codeCoverageIgnoreStart
// Reason: Hard to test.
if (isset($_SERVER['argv']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    OlzApi::generate();
}
// @codeCoverageIgnoreEnd
