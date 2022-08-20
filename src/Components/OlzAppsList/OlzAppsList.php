<?php

namespace Olz\Components\OlzAppsList;

use Olz\Apps\OlzApps;
use Olz\Entity\User;
use Olz\Utils\AuthUtils;
use Olz\Utils\EnvUtils;

class OlzAppsList {
    public static function render($args = []) {
        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getAuthenticatedUser();
        $available_apps = OlzApps::getAppsForUser($user);
        $out = '';
        $out .= "<div class='apps-list'>";
        $out .= implode('', array_map(function ($app) {
            $icon = $app->getIcon();
            $display_name = $app->getDisplayName();
            $href = $app->getHref();
            $basename = $app->getBasename();
            return <<<ZZZZZZZZZZ
            <a href='{$href}'>
                <div class='app-container'>
                    <img src='{$icon}' alt='{$basename}-icon' class='app-icon' />
                    <div>{$display_name}</div>
                </div>
            </a>
            ZZZZZZZZZZ;
        }, $available_apps));
        $out .= "</div>";

        if (!$auth_utils->hasPermission('any')) {
            $env_utils = EnvUtils::fromEnv();
            $code_href = $env_utils->getCodeHref();
            $hypothetical_logged_in_user = new User();
            $hypothetical_logged_in_user->setId(null);
            $hypothetical_logged_in_user->setZugriff('');
            $logged_in_apps = OlzApps::getAppsForUser($hypothetical_logged_in_user);
            $available_app_paths = [];
            foreach ($available_apps as $available_app) {
                $available_apps[$available_app->getPath()] = true;
            }
            $additional_logged_in_apps = [];
            foreach ($logged_in_apps as $logged_in_app) {
                if (!($available_apps[$logged_in_app->getPath()] ?? false)) {
                    $additional_logged_in_apps[] = $logged_in_app;
                }
            }
            $out .= "<div class='hypothetical-container'>";
            $out .= <<<ZZZZZZZZZZ
            <div class='hypothetical-overlay'>
                <div>Diese zusätzlichen Apps sind nur für eingeloggte Benutzer verfügbar.</div>
                <div class='auth-buttons'>
                    <a class='btn btn-primary' href='#login-dialog' role='button'>Login</a>
                    <a class='btn btn-secondary' href='{$code_href}konto_passwort.php' role='button'>Konto erstellen</a>
                </div>
            </div>
            ZZZZZZZZZZ;
            $out .= "<div class='apps-list hypothetical'>";
            $out .= implode('', array_map(function ($app) {
                $icon = $app->getIcon();
                $display_name = $app->getDisplayName();
                $href = $app->getHref();
                $basename = $app->getBasename();
                return <<<ZZZZZZZZZZ
                <div class='app-container'>
                    <img src='{$icon}' alt='{$basename}-icon' class='app-icon' />
                    <div>{$display_name}</div>
                </div>
                ZZZZZZZZZZ;
            }, $additional_logged_in_apps));
            $out .= "</div>";
            $out .= "</div>";
        }

        $out .= "<div class='openmoji-credits'>Einige Icons mit Emojis von <a href='https://openmoji.org/' target='_blank'>OpenMoji</a> &mdash; dem open-source Emoji- und Icon-Projekt. Lizenz: <a href='https://creativecommons.org/licenses/by-sa/4.0/#' target='_blank'>CC BY-SA 4.0</a></div>";

        return $out;
    }
}
