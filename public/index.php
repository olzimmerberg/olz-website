<?php

// TODO: Reactivate this standard procedure once migration is finished:

// use App\Kernel;

// $deploy_path = dirname(__DIR__);

// require_once $deploy_path.'/vendor/autoload_runtime.php';

// return function (array $context) {
//     return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
// };

// TODO: Delete this legacy migration procedure:

use App\Kernel;
use App\LegacyBridge;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

$deploy_path = dirname(__DIR__);

require_once $deploy_path.'/vendor/autoload.php';

(new Dotenv())->bootEnv($deploy_path.'/.env');

/*
 * The kernel will always be available globally, allowing you to
 * access it from your existing application and through it the
 * service container. This allows for introducing new features in
 * the existing application.
 */
global $kernel;

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(
      explode(',', $trustedProxies),
      Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO
    );
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);

/*
 * LegacyBridge will take care of figuring out whether to boot up the
 * existing application or to send the Symfony response back to the client.
 */
$scriptFile = LegacyBridge::prepareLegacyScript($request, $response, __DIR__);
if ($scriptFile !== null) {
    require $scriptFile;
} else {
    $response->send();
}
$kernel->terminate($request, $response);
