<?php

// =============================================================================
// Die Kopfzeile der Website.
// =============================================================================

namespace Olz\Components\Page\OlzHeaderBar;

use Olz\Components\Auth\OlzAccountMenu\OlzAccountMenu;
use Olz\Components\Page\OlzMenu\OlzMenu;
use Olz\Utils\EnvUtils;

class OlzHeaderBar {
    public static function render($args = []) {
        $out = '';

        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();
        $data_href = $env_utils->getDataHref();
        $back_link = $args['back_link'] ?? null;

        $out .= "<div id='header-bar' class='header-bar menu-closed'>";

        $out .= "<div class='above-header'>";
        $out .= "<div class='account-menu-container'>";

        if (!($args['skip_auth_menu'] ?? false)) {
            $out .= OlzAccountMenu::render();
        }

        $out .= "</div>";
        $out .= "</div>";

        $out .= "<div class='below-header'>";
        $out .= "<div id='menu-container' class='menu-container'>";

        $out .= OlzMenu::render([
            'back_link' => $back_link,
        ]);

        $out .= "</div>"; // menu-container
        $out .= "</div>"; // below-header

        if ($back_link !== null) {
            $out .= "<a href='{$back_link}' id='menu-switch' />";
            $out .= "<img src='{$code_href}icns/menu_back.svg' alt='' class='menu-back noborder' />";
            $out .= "</a>";
        } else {
            $out .= "<div id='menu-switch' onclick='olz.toggleMenu()' />";
            $out .= "<img src='{$code_href}icns/menu_hamburger.svg' alt='' class='menu-hamburger noborder' />";
            $out .= "<img src='{$code_href}icns/menu_close.svg' alt='' class='menu-close noborder' />";
            $out .= "</div>";
        }

        $out .= "<div class='header-content-container'>";
        $out .= "<div class='header-content-scroller'>";
        $out .= "<div class='header-content'>";

        // TODO: Remove switch as soon as Safari properly supports SVGs.
        if (preg_match('/Safari/i', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
            $out .= "<img srcset='{$code_href}icns/olz_logo@2x.png 2x, {$code_href}icns/olz_logo.png 1x' src='{$code_href}icns/olz_logo.png' alt='' class='noborder' id='olz-logo' />";
        } else {
            $out .= "<img src='{$code_href}icns/olz_logo.svg' alt='' class='noborder' id='olz-logo' />";
        }
        $out .= "<div style='flex-grow:1;'></div>";

        // $out .= OlzHeaderJomCounter::render();

        // Nat. OL Weekend Davos Klosters
        // $out .= "<div class='header-box'><a href='{$code_href}zimmerberg_ol/' target='_blank' id='weekend-link'><img src='{$data_href}img/zol_2022/logo_260.png' alt='Nationales OL-Weekend Davos Klosters 2022' /></a></div>";

        // OLZ Trophy 2017
        $out .= "<div class='header-box'><a href='trophy.php' id='trophy-link'><img src='{$data_href}img/trophy.png' alt='trophy' /></a></div>";

        $out .= "</div>"; // header-content
        $out .= "</div>"; // header-content-scroller
        $out .= "</div>"; // header-content-container
        $out .= "</div>"; // header-bar

        return $out;
    }
}
