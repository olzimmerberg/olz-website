<?php

namespace Olz\Api;

use Olz\Apps\OlzApps;
use PhpTypeScriptApi\Api;

// Needed because this file can be called directly.
require_once __DIR__.'/../../vendor/autoload.php';

class OlzApi extends Api {
    public function __construct(
        Endpoints\OnDailyEndpoint $onDailyEndpoint,
        Endpoints\OnContinuouslyEndpoint $onContinuouslyEndpoint,
        Endpoints\LoginEndpoint $loginEndpoint,
        Endpoints\ResetPasswordEndpoint $resetPasswordEndpoint,
        Endpoints\SwitchUserEndpoint $switchUserEndpoint,
        Endpoints\LogoutEndpoint $logoutEndpoint,
        Endpoints\GetAuthenticatedUserEndpoint $getAuthenticatedUserEndpoint,
        Endpoints\GetAuthenticatedRolesEndpoint $getAuthenticatedRolesEndpoint,
        Endpoints\UpdateUserEndpoint $updateUserEndpoint,
        Endpoints\VerifyUserEmailEndpoint $verifyUserEmailEndpoint,
        Endpoints\UpdateUserPasswordEndpoint $updateUserPasswordEndpoint,
        Endpoints\SignUpWithPasswordEndpoint $signUpWithPasswordEndpoint,
        Endpoints\LoginWithStravaEndpoint $loginWithStravaEndpoint,
        Endpoints\SignUpWithStravaEndpoint $signUpWithStravaEndpoint,
        Endpoints\DeleteUserEndpoint $deleteUserEndpoint,
        Endpoints\ExecuteEmailReactionEndpoint $executeEmailReactionEndpoint,
        Endpoints\LinkTelegramEndpoint $linkTelegramEndpoint,
        Endpoints\OnTelegramEndpoint $onTelegramEndpoint,
        Endpoints\UpdateOlzTextEndpoint $updateOlzTextEndpoint,
        Endpoints\StartUploadEndpoint $startUploadEndpoint,
        Endpoints\UpdateUploadEndpoint $updateUploadEndpoint,
        Endpoints\FinishUploadEndpoint $finishUploadEndpoint,
        \Olz\News\Endpoints\CreateNewsEndpoint $createNewsEndpoint,
        \Olz\News\Endpoints\GetNewsEndpoint $getNewsEndpoint,
        \Olz\News\Endpoints\EditNewsEndpoint $editNewsEndpoint,
        \Olz\News\Endpoints\UpdateNewsEndpoint $updateNewsEndpoint,
        \Olz\News\Endpoints\DeleteNewsEndpoint $deleteNewsEndpoint,
        \Olz\Startseite\Endpoints\CreateWeeklyPictureEndpoint $createWeeklyPictureEndpoint,
        \Olz\Termine\Endpoints\CreateTerminEndpoint $createTerminEndpoint,
        \Olz\Termine\Endpoints\GetTerminEndpoint $getTerminEndpoint,
        \Olz\Termine\Endpoints\EditTerminEndpoint $editTerminEndpoint,
        \Olz\Termine\Endpoints\UpdateTerminEndpoint $updateTerminEndpoint,
        \Olz\Termine\Endpoints\DeleteTerminEndpoint $deleteTerminEndpoint,
    ) {
        $this->registerEndpoint('onDaily', function () use ($onDailyEndpoint) {
            return $onDailyEndpoint;
        });
        $this->registerEndpoint('onContinuously', function () use ($onContinuouslyEndpoint) {
            return $onContinuouslyEndpoint;
        });
        $this->registerEndpoint('login', function () use ($loginEndpoint) {
            return $loginEndpoint;
        });
        $this->registerEndpoint('resetPassword', function () use ($resetPasswordEndpoint) {
            return $resetPasswordEndpoint;
        });
        $this->registerEndpoint('switchUser', function () use ($switchUserEndpoint) {
            return $switchUserEndpoint;
        });
        $this->registerEndpoint('logout', function () use ($logoutEndpoint) {
            return $logoutEndpoint;
        });
        $this->registerEndpoint('getAuthenticatedUser', function () use ($getAuthenticatedUserEndpoint) {
            return $getAuthenticatedUserEndpoint;
        });
        $this->registerEndpoint('getAuthenticatedRoles', function () use ($getAuthenticatedRolesEndpoint) {
            return $getAuthenticatedRolesEndpoint;
        });
        $this->registerEndpoint('updateUser', function () use ($updateUserEndpoint) {
            return $updateUserEndpoint;
        });
        $this->registerEndpoint('verifyUserEmail', function () use ($verifyUserEmailEndpoint) {
            return $verifyUserEmailEndpoint;
        });
        $this->registerEndpoint('updatePassword', function () use ($updateUserPasswordEndpoint) {
            return $updateUserPasswordEndpoint;
        });
        $this->registerEndpoint('signUpWithPassword', function () use ($signUpWithPasswordEndpoint) {
            return $signUpWithPasswordEndpoint;
        });
        $this->registerEndpoint('loginWithStrava', function () use ($loginWithStravaEndpoint) {
            return $loginWithStravaEndpoint;
        });
        $this->registerEndpoint('signUpWithStrava', function () use ($signUpWithStravaEndpoint) {
            return $signUpWithStravaEndpoint;
        });
        $this->registerEndpoint('deleteUser', function () use ($deleteUserEndpoint) {
            return $deleteUserEndpoint;
        });
        $this->registerEndpoint('executeEmailReaction', function () use ($executeEmailReactionEndpoint) {
            return $executeEmailReactionEndpoint;
        });
        $this->registerEndpoint('linkTelegram', function () use ($linkTelegramEndpoint) {
            return $linkTelegramEndpoint;
        });
        $this->registerEndpoint('onTelegram', function () use ($onTelegramEndpoint) {
            return $onTelegramEndpoint;
        });
        $this->registerEndpoint('updateOlzText', function () use ($updateOlzTextEndpoint) {
            return $updateOlzTextEndpoint;
        });
        $this->registerEndpoint('startUpload', function () use ($startUploadEndpoint) {
            return $startUploadEndpoint;
        });
        $this->registerEndpoint('updateUpload', function () use ($updateUploadEndpoint) {
            return $updateUploadEndpoint;
        });
        $this->registerEndpoint('finishUpload', function () use ($finishUploadEndpoint) {
            return $finishUploadEndpoint;
        });

        // News

        $this->registerEndpoint('createNews', function () use ($createNewsEndpoint) {
            return $createNewsEndpoint;
        });
        $this->registerEndpoint('getNews', function () use ($getNewsEndpoint) {
            return $getNewsEndpoint;
        });
        $this->registerEndpoint('editNews', function () use ($editNewsEndpoint) {
            return $editNewsEndpoint;
        });
        $this->registerEndpoint('updateNews', function () use ($updateNewsEndpoint) {
            return $updateNewsEndpoint;
        });
        $this->registerEndpoint('deleteNews', function () use ($deleteNewsEndpoint) {
            return $deleteNewsEndpoint;
        });

        // Startseite

        $this->registerEndpoint('createWeeklyPicture', function () use ($createWeeklyPictureEndpoint) {
            return $createWeeklyPictureEndpoint;
        });

        // Termine

        $this->registerEndpoint('createTermin', function () use ($createTerminEndpoint) {
            return $createTerminEndpoint;
        });
        $this->registerEndpoint('getTermin', function () use ($getTerminEndpoint) {
            return $getTerminEndpoint;
        });
        $this->registerEndpoint('editTermin', function () use ($editTerminEndpoint) {
            return $editTerminEndpoint;
        });
        $this->registerEndpoint('updateTermin', function () use ($updateTerminEndpoint) {
            return $updateTerminEndpoint;
        });
        $this->registerEndpoint('deleteTermin', function () use ($deleteTerminEndpoint) {
            return $deleteTerminEndpoint;
        });

        OlzApps::registerAllEndpoints($this);
    }

    public static function generate() {
        $olz_api = self::getInstance();

        file_put_contents(
            __DIR__.'/client/generated_olz_api_types.ts',
            $olz_api->getTypeScriptDefinition('OlzApi')
        );

        echo "\nOLZ API client generated.\n";
    }

    public static function getInstance() {
        return new self(
            new Endpoints\OnDailyEndpoint(),
            new Endpoints\OnContinuouslyEndpoint(),
            new Endpoints\LoginEndpoint(),
            new Endpoints\ResetPasswordEndpoint(),
            new Endpoints\SwitchUserEndpoint(),
            new Endpoints\LogoutEndpoint(),
            new Endpoints\GetAuthenticatedUserEndpoint(),
            new Endpoints\GetAuthenticatedRolesEndpoint(),
            new Endpoints\UpdateUserEndpoint(),
            new Endpoints\VerifyUserEmailEndpoint(),
            new Endpoints\UpdateUserPasswordEndpoint(),
            new Endpoints\SignUpWithPasswordEndpoint(),
            new Endpoints\LoginWithStravaEndpoint(),
            new Endpoints\SignUpWithStravaEndpoint(),
            new Endpoints\DeleteUserEndpoint(),
            new Endpoints\ExecuteEmailReactionEndpoint(),
            new Endpoints\LinkTelegramEndpoint(),
            new Endpoints\OnTelegramEndpoint(),
            new Endpoints\UpdateOlzTextEndpoint(),
            new Endpoints\StartUploadEndpoint(),
            new Endpoints\UpdateUploadEndpoint(),
            new Endpoints\FinishUploadEndpoint(),
            new \Olz\News\Endpoints\CreateNewsEndpoint(),
            new \Olz\News\Endpoints\GetNewsEndpoint(),
            new \Olz\News\Endpoints\EditNewsEndpoint(),
            new \Olz\News\Endpoints\UpdateNewsEndpoint(),
            new \Olz\News\Endpoints\DeleteNewsEndpoint(),
            new \Olz\Startseite\Endpoints\CreateWeeklyPictureEndpoint(),
            new \Olz\Termine\Endpoints\CreateTerminEndpoint(),
            new \Olz\Termine\Endpoints\GetTerminEndpoint(),
            new \Olz\Termine\Endpoints\EditTerminEndpoint(),
            new \Olz\Termine\Endpoints\UpdateTerminEndpoint(),
            new \Olz\Termine\Endpoints\DeleteTerminEndpoint(),
        );
    }
}

// @codeCoverageIgnoreStart
// Reason: Hard to test.
if (isset($_SERVER['argv']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    OlzApi::generate();
}
// @codeCoverageIgnoreEnd
