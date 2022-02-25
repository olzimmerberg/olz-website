<?php

use PhpTypeScriptApi\Api;

require_once __DIR__.'/../config/vendor/autoload.php';

$olz_api = new Api();

$olz_api->registerEndpoint('onDaily', function () {
    require_once __DIR__.'/endpoints/OnDailyEndpoint.php';
    return new OnDailyEndpoint();
});
$olz_api->registerEndpoint('onContinuously', function () {
    require_once __DIR__.'/endpoints/OnContinuouslyEndpoint.php';
    return new OnContinuouslyEndpoint();
});
$olz_api->registerEndpoint('login', function () {
    require_once __DIR__.'/endpoints/LoginEndpoint.php';
    return new LoginEndpoint();
});
$olz_api->registerEndpoint('resetPassword', function () {
    require_once __DIR__.'/endpoints/ResetPasswordEndpoint.php';
    return new ResetPasswordEndpoint();
});
$olz_api->registerEndpoint('logout', function () {
    require_once __DIR__.'/endpoints/LogoutEndpoint.php';
    return new LogoutEndpoint();
});
$olz_api->registerEndpoint('updateUser', function () {
    require_once __DIR__.'/endpoints/UpdateUserEndpoint.php';
    return new UpdateUserEndpoint();
});
$olz_api->registerEndpoint('updatePassword', function () {
    require_once __DIR__.'/endpoints/UpdateUserPasswordEndpoint.php';
    return new UpdateUserPasswordEndpoint();
});
$olz_api->registerEndpoint('signUpWithPassword', function () {
    require_once __DIR__.'/endpoints/SignUpWithPasswordEndpoint.php';
    return new SignUpWithPasswordEndpoint();
});
$olz_api->registerEndpoint('loginWithStrava', function () {
    require_once __DIR__.'/endpoints/LoginWithStravaEndpoint.php';
    return new LoginWithStravaEndpoint();
});
$olz_api->registerEndpoint('signUpWithStrava', function () {
    require_once __DIR__.'/endpoints/SignUpWithStravaEndpoint.php';
    return new SignUpWithStravaEndpoint();
});
$olz_api->registerEndpoint('executeEmailReaction', function () {
    require_once __DIR__.'/endpoints/ExecuteEmailReactionEndpoint.php';
    return new ExecuteEmailReactionEndpoint();
});
$olz_api->registerEndpoint('linkTelegram', function () {
    require_once __DIR__.'/endpoints/LinkTelegramEndpoint.php';
    return new LinkTelegramEndpoint();
});
$olz_api->registerEndpoint('onTelegram', function () {
    require_once __DIR__.'/endpoints/OnTelegramEndpoint.php';
    return new OnTelegramEndpoint();
});
$olz_api->registerEndpoint('getLogs', function () {
    require_once __DIR__.'/endpoints/GetLogsEndpoint.php';
    return new GetLogsEndpoint();
});
$olz_api->registerEndpoint('updateNotificationSubscriptions', function () {
    require_once __DIR__.'/endpoints/UpdateNotificationSubscriptionsEndpoint.php';
    return new UpdateNotificationSubscriptionsEndpoint();
});
$olz_api->registerEndpoint('updateOlzText', function () {
    require_once __DIR__.'/endpoints/UpdateOlzTextEndpoint.php';
    return new UpdateOlzTextEndpoint();
});
$olz_api->registerEndpoint('startUpload', function () {
    require_once __DIR__.'/endpoints/StartUploadEndpoint.php';
    return new StartUploadEndpoint();
});
$olz_api->registerEndpoint('updateUpload', function () {
    require_once __DIR__.'/endpoints/UpdateUploadEndpoint.php';
    return new UpdateUploadEndpoint();
});
$olz_api->registerEndpoint('finishUpload', function () {
    require_once __DIR__.'/endpoints/FinishUploadEndpoint.php';
    return new FinishUploadEndpoint();
});

// Anmelden

$olz_api->registerEndpoint('createRegistration', function () {
    require_once __DIR__.'/../anmelden/endpoints/CreateRegistrationEndpoint.php';
    return new CreateRegistrationEndpoint();
});
$olz_api->registerEndpoint('createRegistrationForm', function () {
    require_once __DIR__.'/../anmelden/endpoints/CreateRegistrationFormEndpoint.php';
    return new CreateRegistrationFormEndpoint();
});
$olz_api->registerEndpoint('getManagedUsers', function () {
    require_once __DIR__.'/../anmelden/endpoints/GetManagedUsersEndpoint.php';
    return new GetManagedUsersEndpoint();
});
$olz_api->registerEndpoint('getRegistrationForm', function () {
    require_once __DIR__.'/../anmelden/endpoints/GetRegistrationFormEndpoint.php';
    return new GetRegistrationFormEndpoint();
});

// News

$olz_api->registerEndpoint('createNews', function () {
    require_once __DIR__.'/../news/endpoints/CreateNewsEndpoint.php';
    return new CreateNewsEndpoint();
});
$olz_api->registerEndpoint('getNews', function () {
    require_once __DIR__.'/../news/endpoints/GetNewsEndpoint.php';
    return new GetNewsEndpoint();
});
$olz_api->registerEndpoint('editNews', function () {
    require_once __DIR__.'/../news/endpoints/EditNewsEndpoint.php';
    return new EditNewsEndpoint();
});
$olz_api->registerEndpoint('updateNews', function () {
    require_once __DIR__.'/../news/endpoints/UpdateNewsEndpoint.php';
    return new UpdateNewsEndpoint();
});
$olz_api->registerEndpoint('deleteNews', function () {
    require_once __DIR__.'/../news/endpoints/DeleteNewsEndpoint.php';
    return new DeleteNewsEndpoint();
});

// ÖV

$olz_api->registerEndpoint('searchTransportConnection', function () {
    require_once __DIR__.'/../oev/endpoints/SearchTransportConnectionEndpoint.php';
    return new SearchTransportConnectionEndpoint();
});

// WebDAV

$olz_api->registerEndpoint('getWebdavAccessToken', function () {
    require_once __DIR__.'/../webdav/endpoints/GetWebdavAccessTokenEndpoint.php';
    return new GetWebdavAccessTokenEndpoint();
});
$olz_api->registerEndpoint('revokeWebdavAccessToken', function () {
    require_once __DIR__.'/../webdav/endpoints/RevokeWebdavAccessTokenEndpoint.php';
    return new RevokeWebdavAccessTokenEndpoint();
});

return $olz_api;
