<?php

namespace Olz\Components\Page\OlzHeaderWithoutRouting;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzHeaderBar\OlzHeaderBar;
use Olz\Components\Schema\OlzOrganizationData\OlzOrganizationData;
use Olz\Utils\StandardSession;

/** @extends OlzComponent<array<string, mixed>> */
class OlzHeaderWithoutRouting extends OlzComponent {
    public function getHtml(mixed $args): string {
        global $_SESSION;

        StandardSession::session_start_if_cookie_set();

        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();
        $css_path = "{$data_path}jsbuild/olz/main.min.css";
        $js_path = "{$data_path}jsbuild/olz/main.min.js";
        $css_modified = is_file($css_path) ? filemtime($css_path) : 0;
        $js_modified = is_file($js_path) ? filemtime($js_path) : 0;
        $css_href = "/jsbuild/olz/main.min.css?modified={$css_modified}";
        $js_href = "/jsbuild/olz/main.min.js?modified={$js_modified}";
        $code_href_json = json_encode($code_href);
        $data_href_json = json_encode($data_href);
        $user_json = json_encode([
            'permissions' => $_SESSION['auth'] ?? null,
            'root' => $_SESSION['root'] ?? null,
            'username' => $_SESSION['user'] ?? null,
            'id' => intval($_SESSION['user_id'] ?? null),
        ]);
        $iso_now_json = json_encode($this->dateUtils()->getIsoNow());

        $html_title = "OL Zimmerberg";
        if (isset($args['title'])) {
            $title_arg = htmlspecialchars($args['title']);
            $html_title = "{$title_arg} - OL Zimmerberg";
        }
        $html_description = "";
        if (isset($args['description'])) {
            $description_arg = htmlspecialchars(str_replace("\n", " ", $args['description']));
            $html_description = "<meta name='Description' content='{$description_arg}'>";
        }
        $html_canonical = "";
        if (isset($args['canonical_url'])) {
            $host = str_replace(['www.', '/'], ['', ''], $_SERVER['HTTP_HOST']);
            $canonical_url = "https://{$host}{$args['canonical_url']}";
            $esc_canonical_url = htmlspecialchars($canonical_url);
            $html_canonical = "<link rel='canonical' href='{$esc_canonical_url}'/>";
        }
        $no_robots = isset($_GET['archiv']) || ($args['norobots'] ?? false);
        $robots_html = $no_robots ? "<meta name='robots' content='noindex, nofollow'>" : "";
        $olz_organization_data = OlzOrganizationData::render([], $this);
        $additional_headers = implode("\n", $args['additional_headers'] ?? []);
        $header_bar = OlzHeaderBar::render([
            'back_link' => $args['back_link'] ?? null,
            'skip_auth_menu' => $args['skip_auth_menu'] ?? false,
        ], $this);

        return <<<ZZZZZZZZZZ
            <!DOCTYPE html>
            <html lang='de'>
            <head>
            <meta http-equiv='cache-control' content='public'>
            <meta http-equiv='content-type' content='text/html;charset=utf-8'>
            <meta name='Keywords' content='OL, Orientierungslauf, Sport, Laufsport, Gruppe, Klub, Verein, Zimmerberg, linkes Zürichseeufer, Sihltal, Kilchberg, Rüschlikon, Thalwil, Gattikon, Oberrieden, Horgen, Au ZH, Wädenswil, Richterswil, Schönenberg, Hirzel, Langnau am Albis, Adliswil, Stadt Zürich, Leimbach, Wollishofen, Enge, Friesenberg, Üetliberg, Entlisberg, Albis, Buchenegg, Landforst, Kopfholz, Chopfholz, Reidholz, Schweiz, OLZ, OLG'>
            {$html_description}
            <meta name='Content-Language' content='de'>
            {$robots_html}
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$html_title}</title>
            <link rel='icon' href='{$code_href}favicon.ico' type='image/x-icon' />
            {$html_canonical}
            {$olz_organization_data}
            {$additional_headers}
            <link rel='stylesheet' href='{$css_href}' />
            <script type='text/javascript'>
                window.olzCodeHref = {$code_href_json};
                window.olzDataHref = {$data_href_json};
                window.olzUser = {$user_json};
                window.olzIsoNow = {$iso_now_json};
            </script>
            <script type='text/javascript' src='{$js_href}' onload='olz.loaded()'></script>
            </head>
            <body class='olz-override-root'>
                <a name='top'></a>
                {$header_bar}
                <div class='site-container'>
                    <div class='site-background'>
            ZZZZZZZZZZ;
    }
}
