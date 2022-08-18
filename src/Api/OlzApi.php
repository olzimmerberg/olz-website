<?php

namespace Olz\Api;

use Olz\Apps\OlzApps;
use PhpTypeScriptApi\Api;

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
            require_once __DIR__.'/Endpoints/OnDailyEndpoint.php';
            return new Endpoints\OnDailyEndpoint();
        });
        $olz_api->registerEndpoint('onContinuously', function () {
            require_once __DIR__.'/Endpoints/OnContinuouslyEndpoint.php';
            return new Endpoints\OnContinuouslyEndpoint();
        });
        $olz_api->registerEndpoint('login', function () {
            require_once __DIR__.'/Endpoints/LoginEndpoint.php';
            return new Endpoints\LoginEndpoint();
        });
        $olz_api->registerEndpoint('resetPassword', function () {
            require_once __DIR__.'/Endpoints/ResetPasswordEndpoint.php';
            return new Endpoints\ResetPasswordEndpoint();
        });
        $olz_api->registerEndpoint('logout', function () {
            require_once __DIR__.'/Endpoints/LogoutEndpoint.php';
            return new Endpoints\LogoutEndpoint();
        });
        $olz_api->registerEndpoint('updateUser', function () {
            require_once __DIR__.'/Endpoints/UpdateUserEndpoint.php';
            return new Endpoints\UpdateUserEndpoint();
        });
        $olz_api->registerEndpoint('updatePassword', function () {
            require_once __DIR__.'/Endpoints/UpdateUserPasswordEndpoint.php';
            return new Endpoints\UpdateUserPasswordEndpoint();
        });
        $olz_api->registerEndpoint('signUpWithPassword', function () {
            require_once __DIR__.'/Endpoints/SignUpWithPasswordEndpoint.php';
            return new Endpoints\SignUpWithPasswordEndpoint();
        });
        $olz_api->registerEndpoint('loginWithStrava', function () {
            require_once __DIR__.'/Endpoints/LoginWithStravaEndpoint.php';
            return new Endpoints\LoginWithStravaEndpoint();
        });
        $olz_api->registerEndpoint('signUpWithStrava', function () {
            require_once __DIR__.'/Endpoints/SignUpWithStravaEndpoint.php';
            return new Endpoints\SignUpWithStravaEndpoint();
        });
        $olz_api->registerEndpoint('deleteUser', function () {
            require_once __DIR__.'/Endpoints/DeleteUserEndpoint.php';
            return new Endpoints\DeleteUserEndpoint();
        });
        $olz_api->registerEndpoint('executeEmailReaction', function () {
            require_once __DIR__.'/Endpoints/ExecuteEmailReactionEndpoint.php';
            return new Endpoints\ExecuteEmailReactionEndpoint();
        });
        $olz_api->registerEndpoint('linkTelegram', function () {
            require_once __DIR__.'/Endpoints/LinkTelegramEndpoint.php';
            return new Endpoints\LinkTelegramEndpoint();
        });
        $olz_api->registerEndpoint('onTelegram', function () {
            require_once __DIR__.'/Endpoints/OnTelegramEndpoint.php';
            return new Endpoints\OnTelegramEndpoint();
        });
        $olz_api->registerEndpoint('updateOlzText', function () {
            require_once __DIR__.'/Endpoints/UpdateOlzTextEndpoint.php';
            return new Endpoints\UpdateOlzTextEndpoint();
        });
        $olz_api->registerEndpoint('startUpload', function () {
            require_once __DIR__.'/Endpoints/StartUploadEndpoint.php';
            return new Endpoints\StartUploadEndpoint();
        });
        $olz_api->registerEndpoint('updateUpload', function () {
            require_once __DIR__.'/Endpoints/UpdateUploadEndpoint.php';
            return new Endpoints\UpdateUploadEndpoint();
        });
        $olz_api->registerEndpoint('finishUpload', function () {
            require_once __DIR__.'/Endpoints/FinishUploadEndpoint.php';
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

        OlzApps::registerAllEndpoints($olz_api);

        return $olz_api;
    }
}

if (isset($_SERVER['argv']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    OlzApi::generate();
}
