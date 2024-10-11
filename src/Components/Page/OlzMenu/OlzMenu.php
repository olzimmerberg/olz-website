<?php

// =============================================================================
// Das Navigationsmenu der Website.
// =============================================================================

namespace Olz\Components\Page\OlzMenu;

use Olz\Components\Common\OlzComponent;

class OlzMenu extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $out = '';

        $code_href = $this->envUtils()->getCodeHref();
        $data_path = $this->envUtils()->getDataPath();

        $main_menu = [
            ["Startseite", ""], // Menüpunkt ('Name','Link')
            ["", "", ''],
            ["News", "news"],
            ["Termine", "termine"],
            ["", "", ''],
            ["Karten", "karten"],
            ["Material & Kleider", "material"],
            ["", "", ''],
            ["Service", "service"],
            ["Verein", "verein"],
        ];

        // BACK-BUTTON
        $back_menu_out = '';
        $back_link = $args['back_link'] ?? null;
        if ($back_link !== null) {
            $back_menu_out = <<<ZZZZZZZZZZ
                <a href='{$back_link}' class='menu-link' id='back-link'>
                    <div class='menutag'>
                        <img src='{$code_href}assets/icns/back_16.svg' alt='&lt;' class='back-icon'>
                        Zurück
                    </div>
                </a>
                ZZZZZZZZZZ;
        }

        // LIVE-RESULTATE
        $live_menu_out = '';
        $live_json_path = "{$data_path}results/_live.json";
        if (is_file($live_json_path)) {
            $content = file_get_contents($live_json_path);
            if ($content) {
                $live = json_decode($content, true);
                $last_updated_at = strtotime($live['last_updated_at']);
                $now = strtotime($this->dateUtils()->getIsoNow());
                if ($live && $last_updated_at > $now - 3600) {
                    $live_file = $live['file'];
                    $style = preg_match('/test/', $live_file) ? " style='display:none;'" : "";
                    $live_menu_out = <<<ZZZZZZZZZZ
                        <a href='{$code_href}apps/resultate/?file={$live_file}'{$style} class='menu-link' id='live-results-link'>
                            <div class='menutag'>
                                Live-Resultate
                            </div>
                        </a>
                        ZZZZZZZZZZ;
                }
            }
        }

        $main_menu_out = self::getMenu($main_menu, $code_href);

        $out .= <<<ZZZZZZZZZZ
            <div id='menu' class='menu'>
                <div class='back-menu'>{$back_menu_out}</div>
                <div class='live-menu'>{$live_menu_out}</div>
                <div class='main-menu'>{$main_menu_out}</div>
                <form name='Suche' method='get' action='{$code_href}suche'>
                    <input
                        type='text'
                        name='anfrage'
                        id='site-search'
                        title='Suche auf olzimmerberg.ch'
                        placeholder='Suchen...'
                        value=''
                    />
                </form>
                <div class='feedback-mail'>
                    <script type='text/javascript'>
                        olz.MailTo("website", "olzimmerberg.ch", "Feedback geben", "Homepage%20OL%20Zimmerberg");
                    </script>
                </div>
                <div class='platform-links'>
                    <a
                        href='https://github.com/olzimmerberg/olz-website'
                        target='_blank'
                        rel='noreferrer noopener'
                        title='OL Zimmerberg auf GitHub'
                        class='platform-link'
                    >
                        <img src='{$code_href}assets/icns/github_16.svg' alt='g' class='noborder' />
                    </a>
                    <a
                        href='https://www.youtube.com/channel/UCMhMdPRJOqdXHlmB9kEpmXQ'
                        target='_blank'
                        rel='noreferrer noopener'
                        title='OL Zimmerberg auf YouTube'
                        class='platform-link'
                    >
                        <img src='{$code_href}assets/icns/youtube_16.svg' alt='Y' class='noborder' />
                    </a>
                    <a
                        href='https://www.facebook.com/olzimmerberg'
                        target='_blank'
                        rel='noreferrer noopener'
                        title='OL Zimmerberg auf Facebook'
                        class='platform-link'
                    >
                        <img src='{$code_href}assets/icns/facebook_16.svg' alt='f' class='noborder' />
                    </a>
                    <a
                        href='https://www.instagram.com/olzimmerberg'
                        target='_blank'
                        rel='noreferrer noopener'
                        title='OL Zimmerberg auf Instagram'
                        class='platform-link'
                    >
                        <img src='{$code_href}assets/icns/instagram_16.svg' alt='i' class='noborder' />
                    </a>
                    <a
                        href='https://www.strava.com/clubs/olzimmerberg'
                        target='_blank'
                        rel='noreferrer noopener'
                        title='OL Zimmerberg auf Strava'
                        class='platform-link'
                    >
                        <img src='{$code_href}assets/icns/strava_16.svg' alt='s' class='noborder' />
                    </a>
                </div>
            </div>
            ZZZZZZZZZZ;

        return $out;
    }

    /** @param array<array<string>> $menu */
    protected static function getMenu(array $menu, string $code_href): string {
        $out = '';
        for ($i = 0; $i < count($menu); $i++) {
            $menupunkt = $menu[$i];
            $name = $menupunkt[0];
            $href = $menupunkt[1];
            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            $is_active = (
                preg_match("/^\\/{$menupunkt[1]}(\\/|\\?|#|$)/", $request_uri)
                || ($menupunkt[1] === '' && $request_uri === '')
            );
            $active_class = $is_active ? ' active' : '';
            if ($name != '') {
                $out .= <<<ZZZZZZZZZZ
                    <a href='{$code_href}{$href}' id='menu_a_page_{$href}' class='menu-link'>
                        <div class='menutag{$active_class}'>
                            {$name}
                        </div>
                    </a>
                    ZZZZZZZZZZ;
            } else {
                $out .= <<<'ZZZZZZZZZZ'
                    <div class='separator'></div>
                    ZZZZZZZZZZ;
            }
        }
        return $out;
    }
}
