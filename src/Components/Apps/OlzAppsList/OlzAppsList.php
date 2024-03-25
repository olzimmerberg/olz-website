<?php

namespace Olz\Components\Apps\OlzAppsList;

use Olz\Apps\OlzApps;
use Olz\Components\Common\OlzComponent;
use Olz\Entity\User;

class OlzAppsList extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $user = $this->authUtils()->getCurrentUser();
        $available_apps = OlzApps::getAppsForUser($user);
        $out = '';
        $out .= "<div class='apps-list'>";
        $out .= implode('', array_map(function ($app) use ($code_href) {
            $icon_href = $app->getIconHref();
            $display_name = $app->getDisplayName();
            $href = $app->getHref();
            $basename = $app->getBasename();
            return <<<ZZZZZZZZZZ
            <a href='{$code_href}{$href}'>
                <div class='app-container'>
                    <img src='{$icon_href}' alt='{$basename}-icon' class='app-icon' />
                    <div>{$display_name}</div>
                </div>
            </a>
            ZZZZZZZZZZ;
        }, $available_apps));
        $out .= "</div>";

        if (!$this->authUtils()->hasPermission('any')) {
            $hypothetical_logged_in_user = new User();
            $hypothetical_logged_in_user->setId(null);
            $hypothetical_logged_in_user->setPermissions(' verified_email ');
            $logged_in_apps = OlzApps::getAppsForUser($hypothetical_logged_in_user);
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
                    <a class='btn btn-secondary' href='{$code_href}konto_passwort' role='button'>Konto erstellen</a>
                </div>
            </div>
            ZZZZZZZZZZ;
            $out .= "<div class='apps-list hypothetical'>";
            $out .= implode('', array_map(function ($app) {
                $icon_href = $app->getIconHref();
                $display_name = $app->getDisplayName();
                $basename = $app->getBasename();
                return <<<ZZZZZZZZZZ
                <div class='app-container'>
                    <img src='{$icon_href}' alt='{$basename}-icon' class='app-icon' />
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
