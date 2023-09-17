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
        \Olz\Termine\Endpoints\CreateTerminLocationEndpoint $createTerminLocationEndpoint,
        \Olz\Termine\Endpoints\GetTerminLocationEndpoint $getTerminLocationEndpoint,
        \Olz\Termine\Endpoints\EditTerminLocationEndpoint $editTerminLocationEndpoint,
        \Olz\Termine\Endpoints\UpdateTerminLocationEndpoint $updateTerminLocationEndpoint,
        \Olz\Termine\Endpoints\DeleteTerminLocationEndpoint $deleteTerminLocationEndpoint,
    ) {
        $this->registerEndpoint('onDaily', $onDailyEndpoint);
        $this->registerEndpoint('onContinuously', $onContinuouslyEndpoint);
        $this->registerEndpoint('login', $loginEndpoint);
        $this->registerEndpoint('resetPassword', $resetPasswordEndpoint);
        $this->registerEndpoint('switchUser', $switchUserEndpoint);
        $this->registerEndpoint('logout', $logoutEndpoint);
        $this->registerEndpoint('getAuthenticatedUser', $getAuthenticatedUserEndpoint);
        $this->registerEndpoint('getAuthenticatedRoles', $getAuthenticatedRolesEndpoint);
        $this->registerEndpoint('updateUser', $updateUserEndpoint);
        $this->registerEndpoint('verifyUserEmail', $verifyUserEmailEndpoint);
        $this->registerEndpoint('updatePassword', $updateUserPasswordEndpoint);
        $this->registerEndpoint('signUpWithPassword', $signUpWithPasswordEndpoint);
        $this->registerEndpoint('loginWithStrava', $loginWithStravaEndpoint);
        $this->registerEndpoint('signUpWithStrava', $signUpWithStravaEndpoint);
        $this->registerEndpoint('deleteUser', $deleteUserEndpoint);
        $this->registerEndpoint('executeEmailReaction', $executeEmailReactionEndpoint);
        $this->registerEndpoint('linkTelegram', $linkTelegramEndpoint);
        $this->registerEndpoint('onTelegram', $onTelegramEndpoint);
        $this->registerEndpoint('updateOlzText', $updateOlzTextEndpoint);
        $this->registerEndpoint('startUpload', $startUploadEndpoint);
        $this->registerEndpoint('updateUpload', $updateUploadEndpoint);
        $this->registerEndpoint('finishUpload', $finishUploadEndpoint);

        // News

        $this->registerEndpoint('createNews', $createNewsEndpoint);
        $this->registerEndpoint('getNews', $getNewsEndpoint);
        $this->registerEndpoint('editNews', $editNewsEndpoint);
        $this->registerEndpoint('updateNews', $updateNewsEndpoint);
        $this->registerEndpoint('deleteNews', $deleteNewsEndpoint);

        // Startseite

        $this->registerEndpoint('createWeeklyPicture', $createWeeklyPictureEndpoint);

        // Termine

        $this->registerEndpoint('createTermin', $createTerminEndpoint);
        $this->registerEndpoint('getTermin', $getTerminEndpoint);
        $this->registerEndpoint('editTermin', $editTerminEndpoint);
        $this->registerEndpoint('updateTermin', $updateTerminEndpoint);
        $this->registerEndpoint('deleteTermin', $deleteTerminEndpoint);

        // Termin Locations

        $this->registerEndpoint('createTerminLocation', $createTerminLocationEndpoint);
        $this->registerEndpoint('getTerminLocation', $getTerminLocationEndpoint);
        $this->registerEndpoint('editTerminLocation', $editTerminLocationEndpoint);
        $this->registerEndpoint('updateTerminLocation', $updateTerminLocationEndpoint);
        $this->registerEndpoint('deleteTerminLocation', $deleteTerminLocationEndpoint);

        OlzApps::registerAllEndpoints($this);
    }

    public static function generate() {
        $olz_api = self::getShallowInstance();

        file_put_contents(
            __DIR__.'/client/generated_olz_api_types.ts',
            $olz_api->getTypeScriptDefinition('OlzApi')
        );

        echo "\nOLZ API client generated.\n";
    }

    public static function getShallowInstance() {
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
            new \Olz\Termine\Endpoints\CreateTerminLocationEndpoint(),
            new \Olz\Termine\Endpoints\GetTerminLocationEndpoint(),
            new \Olz\Termine\Endpoints\EditTerminLocationEndpoint(),
            new \Olz\Termine\Endpoints\UpdateTerminLocationEndpoint(),
            new \Olz\Termine\Endpoints\DeleteTerminLocationEndpoint(),
        );
    }
}

// @codeCoverageIgnoreStart
// Reason: Hard to test.
if (isset($_SERVER['argv']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    OlzApi::generate();
}
// @codeCoverageIgnoreEnd
