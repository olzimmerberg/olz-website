<?php

namespace Olz\Components\OlzAppsList;

use Olz\Apps\OlzApps;
use Olz\Utils\AuthUtils;

class OlzAppsList {
    public static function render($args = []) {
        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getAuthenticatedUser();
        $out = '';
        $out .= "<div style='display:flex; flex-wrap:wrap;'>";
        $out .= implode('', array_map(function ($app) {
            $icon = $app->getIcon();
            $display_name = $app->getDisplayName();
            $href = $app->getHref();
            $basename = $app->getBasename();
            return <<<ZZZZZZZZZZ
            <a href='{$href}'>
                <div style='width:160px;text-align:center;'>
                    <img src='{$icon}' alt='{$basename}-icon' class='noborder' style='max-width:80px;' />
                    <div>{$display_name}</div>
                </div>
            </a>
            ZZZZZZZZZZ;
        }, OlzApps::getAppsForUser($user)));
        $out .= "</div>";
        $out .= "<div style='margin-top:80px;'>Einige Icons mit Emojis von <a href='https://openmoji.org/' target='_blank'>OpenMoji</a> &mdash; dem open-source Emoji- und Icon-Projekt. Lizenz: <a href='https://creativecommons.org/licenses/by-sa/4.0/#' target='_blank'>CC BY-SA 4.0</a></div>";
        return $out;
    }
}
