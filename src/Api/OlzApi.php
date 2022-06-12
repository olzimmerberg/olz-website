<?php

namespace Olz\Api;

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
        $olz_api->registerEndpoint('getLogs', function () {
            require_once __DIR__.'/Endpoints/GetLogsEndpoint.php';
            return new Endpoints\GetLogsEndpoint();
        });
        $olz_api->registerEndpoint('updateNotificationSubscriptions', function () {
            require_once __DIR__.'/Endpoints/UpdateNotificationSubscriptionsEndpoint.php';
            return new Endpoints\UpdateNotificationSubscriptionsEndpoint();
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

        // Anmelden

        $olz_api->registerEndpoint('createBooking', function () {
            require_once __DIR__.'/../../_/anmelden/endpoints/CreateBookingEndpoint.php';
            return new \CreateBookingEndpoint();
        });
        $olz_api->registerEndpoint('createRegistration', function () {
            require_once __DIR__.'/../../_/anmelden/endpoints/CreateRegistrationEndpoint.php';
            return new \CreateRegistrationEndpoint();
        });
        $olz_api->registerEndpoint('getManagedUsers', function () {
            require_once __DIR__.'/../../_/anmelden/endpoints/GetManagedUsersEndpoint.php';
            return new \GetManagedUsersEndpoint();
        });
        $olz_api->registerEndpoint('getRegistration', function () {
            require_once __DIR__.'/../../_/anmelden/endpoints/GetRegistrationEndpoint.php';
            return new \GetRegistrationEndpoint();
        });

        // News

        $olz_api->registerEndpoint('createNews', function () {
            require_once __DIR__.'/../../_/news/endpoints/CreateNewsEndpoint.php';
            return new \CreateNewsEndpoint();
        });
        $olz_api->registerEndpoint('getNews', function () {
            require_once __DIR__.'/../../_/news/endpoints/GetNewsEndpoint.php';
            return new \GetNewsEndpoint();
        });
        $olz_api->registerEndpoint('editNews', function () {
            require_once __DIR__.'/../../_/news/endpoints/EditNewsEndpoint.php';
            return new \EditNewsEndpoint();
        });
        $olz_api->registerEndpoint('updateNews', function () {
            require_once __DIR__.'/../../_/news/endpoints/UpdateNewsEndpoint.php';
            return new \UpdateNewsEndpoint();
        });
        $olz_api->registerEndpoint('deleteNews', function () {
            require_once __DIR__.'/../../_/news/endpoints/DeleteNewsEndpoint.php';
            return new \DeleteNewsEndpoint();
        });

        // ÖV

        $olz_api->registerEndpoint('searchTransportConnection', function () {
            require_once __DIR__.'/../Apps/Oev/Endpoints/SearchTransportConnectionEndpoint.php';
            return new \Olz\Apps\Oev\Endpoints\SearchTransportConnectionEndpoint();
        });

        // Quiz

        $olz_api->registerEndpoint('getMySkillLevels', function () {
            require_once __DIR__.'/../../_/quiz/endpoints/GetMySkillLevelsEndpoint.php';
            return new \GetMySkillLevelsEndpoint();
        });
        $olz_api->registerEndpoint('updateMySkillLevels', function () {
            require_once __DIR__.'/../../_/quiz/endpoints/UpdateMySkillLevelsEndpoint.php';
            return new \UpdateMySkillLevelsEndpoint();
        });
        $olz_api->registerEndpoint('registerSkillCategories', function () {
            require_once __DIR__.'/../../_/quiz/endpoints/RegisterSkillCategoriesEndpoint.php';
            return new \RegisterSkillCategoriesEndpoint();
        });
        $olz_api->registerEndpoint('registerSkills', function () {
            require_once __DIR__.'/../../_/quiz/endpoints/RegisterSkillsEndpoint.php';
            return new \RegisterSkillsEndpoint();
        });

        // WebDAV

        $olz_api->registerEndpoint('getWebdavAccessToken', function () {
            require_once __DIR__.'/../Apps/Files/Endpoints/GetWebdavAccessTokenEndpoint.php';
            return new \Olz\Apps\Files\Endpoints\GetWebdavAccessTokenEndpoint();
        });
        $olz_api->registerEndpoint('revokeWebdavAccessToken', function () {
            require_once __DIR__.'/../Apps/Files/Endpoints/RevokeWebdavAccessTokenEndpoint.php';
            return new \Olz\Apps\Files\Endpoints\RevokeWebdavAccessTokenEndpoint();
        });

        return $olz_api;
    }
}

if (isset($_SERVER['argv']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    OlzApi::generate();
}
