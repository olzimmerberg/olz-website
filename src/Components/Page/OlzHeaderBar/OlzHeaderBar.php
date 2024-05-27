<?php

// =============================================================================
// Die Kopfzeile der Website.
// =============================================================================

namespace Olz\Components\Page\OlzHeaderBar;

use Olz\Components\Auth\OlzAccountMenu\OlzAccountMenu;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzMenu\OlzMenu;

class OlzHeaderBar extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $back_link = $args['back_link'] ?? null;

        $account_menu_out = !($args['skip_auth_menu'] ?? false)
            ? OlzAccountMenu::render([], $this)
            : '';

        $menu_out = OlzMenu::render([
            'back_link' => $back_link,
        ], $this);

        $hamburger_or_back_out = ($back_link !== null)
            ? <<<ZZZZZZZZZZ
                <a href='{$back_link}' id='menu-switch'>
                    <img
                        src='{$code_href}assets/icns/menu_back.svg'
                        alt=''
                        class='menu-back noborder'
                    />
                </a>
                ZZZZZZZZZZ
            : <<<ZZZZZZZZZZ
                <div id='menu-switch' onclick='olz.toggleMenu()'>
                    <img
                        src='{$code_href}assets/icns/menu_hamburger.svg'
                        alt=''
                        class='menu-hamburger noborder'
                    />
                    <img
                        src='{$code_href}assets/icns/menu_close.svg'
                        alt=''
                        class='menu-close noborder'
                    />
                </div>
                ZZZZZZZZZZ;

        // TODO: Remove switch as soon as Safari properly supports SVGs.
        $logo_out = preg_match('/Safari/i', $_SERVER['HTTP_USER_AGENT'] ?? '')
            ? <<<ZZZZZZZZZZ
                <img
                    srcset='
                        {$code_href}assets/icns/olz_logo@2x.png 2x,
                        {$code_href}assets/icns/olz_logo.png 1x
                    '
                    src='{$code_href}assets/icns/olz_logo.png'
                    alt=''
                    class='noborder'
                    id='olz-logo'
                />
                ZZZZZZZZZZ
            : <<<ZZZZZZZZZZ
                <img
                    src='{$code_href}assets/icns/olz_logo.svg'
                    alt=''
                    class='noborder'
                    id='olz-logo'
                />
                ZZZZZZZZZZ;

        $out = <<<ZZZZZZZZZZ
            <div id='header-bar' class='header-bar menu-closed'>
                <div class='header-content'>
                    {$hamburger_or_back_out}
                    <div class='logo-container'>{$logo_out}</div>
                    <div class='spacer'></div>
                    <div class='account-menu-container'>{$account_menu_out}</div>
                </div>
                <div class='below-header'>
                    <div id='menu-container' class='menu-container'>
                        {$menu_out}
                    </div>
                </div>
            </div>
            ZZZZZZZZZZ;

        return $out;
    }
}
