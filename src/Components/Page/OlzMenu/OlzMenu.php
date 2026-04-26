<?php

// =============================================================================
// Das Navigationsmenu der Website.
// =============================================================================

namespace Olz\Components\Page\OlzMenu;

use Olz\Components\Common\OlzComponent;

/**
 * @phpstan-type MenuItem array{name: string, ident: string, href: string}
 *
 * @extends OlzComponent<array<string, mixed>>
 */
class OlzMenu extends OlzComponent {
    public function getHtml(mixed $args): string {
        $out = '';

        $code_href = $this->envUtils()->getCodeHref();
        $data_path = $this->envUtils()->getDataPath();

        $news_utils = $this->newsUtils();
        $enc_news_filter = $news_utils->serialize($news_utils->getDefaultFilter());
        $termine_utils = $this->termineUtils();
        $enc_termine_filter = $termine_utils->serialize($termine_utils->getDefaultFilter());

        $main_menu = [
            ['name' => "Startseite", 'ident' => 'startseite', 'href' => ''],
            null,
            ['name' => "News", 'ident' => 'news', 'href' => "news?filter={$enc_news_filter}&seite=1"],
            ['name' => "Termine", 'ident' => 'termine', 'href' => "termine?filter={$enc_termine_filter}"],
            null,
            ['name' => "Angebot", 'ident' => 'angebot', 'href' => 'angebot'],
            ['name' => "Karten", 'ident' => 'karten', 'href' => 'karten'],
            null,
            ['name' => "Service", 'ident' => 'service', 'href' => 'service'],
            ['name' => "Verein", 'ident' => 'verein', 'href' => 'verein'],
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
                $last_updated_at = strtotime($live['last_updated_at']) ?: 0;
                $now = strtotime($this->dateUtils()->getIsoNow()) ?: 0;
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

        $main_menu_out = $this->getMenu($main_menu);

        $out .= <<<ZZZZZZZZZZ
            <div id='menu' class='menu'>
                <div class='back-menu'>{$back_menu_out}</div>
                <div class='live-menu'>{$live_menu_out}</div>
                <div class='main-menu'>{$main_menu_out}</div>
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

    /** @param array<?MenuItem> $menu */
    protected function getMenu(array $menu): string {
        $out = '';
        for ($i = 0; $i < count($menu); $i++) {
            $menupunkt = $menu[$i];
            $out .= $this->getMenuItem($menupunkt);
        }
        return $out;
    }

    /**
     * @param ?MenuItem $menu_item
     */
    protected function getMenuItem(?array $menu_item): string {
        if ($menu_item === null) {
            return <<<'ZZZZZZZZZZ'
                <div class='separator'></div>
                ZZZZZZZZZZ;
        }
        $code_href = $this->envUtils()->getCodeHref();
        $href = $menu_item['href'];
        $href_path = substr($href, 0, strpos($href, '?') ?: strlen($href));
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_active = (
            preg_match("/^\\/{$href_path}(\\/|\\?|#|$)/", $request_uri)
            || ($href === '' && $request_uri === '')
        );
        $active_class = $is_active ? ' active' : '';
        return <<<ZZZZZZZZZZ
            <a href='{$code_href}{$href}' id='menu_a_page_{$menu_item['ident']}' class='menu-link'>
                <div class='menutag{$active_class}'>
                    {$menu_item['name']}
                </div>
            </a>
            ZZZZZZZZZZ;
    }
}
