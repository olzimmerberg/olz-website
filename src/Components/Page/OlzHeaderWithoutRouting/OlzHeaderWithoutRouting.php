<?php

namespace Olz\Components\Page\OlzHeaderWithoutRouting;

use Olz\Components\Page\OlzHeaderBar\OlzHeaderBar;
use Olz\Components\Schema\OlzOrganizationData\OlzOrganizationData;
use Olz\Entity\Counter;
use Olz\Utils\EnvUtils;

class OlzHeaderWithoutRouting {
    public static function render($args = []) {
        global $_CONFIG, $_DATE, $_SERVER, $entityManager;
        $out = '';

        require_once __DIR__.'/../../../../_/config/date.php';
        require_once __DIR__.'/../../../../_/config/doctrine_db.php';
        require_once __DIR__.'/../../../../_/config/server.php';

        $env_utils = EnvUtils::fromEnv();
        $data_path = $env_utils->getDataPath();
        $css_path = "{$data_path}jsbuild/olz/main.min.css";
        $js_path = "{$data_path}jsbuild/olz/main.min.js";
        $css_modified = is_file($css_path) ? filemtime($css_path) : 0;
        $js_modified = is_file($js_path) ? filemtime($js_path) : 0;
        $css_href = "/jsbuild/olz/main.min.css?modified={$css_modified}";
        $js_href = "/jsbuild/olz/main.min.js?modified={$js_modified}";

        if (!isset($refresh)) {
            $refresh = '';
        }
        $html_title = "OL Zimmerberg";
        if (isset($args['title'])) {
            $title_arg = htmlspecialchars($args['title']);
            $html_title = "OL Zimmerberg - {$title_arg}";
        }
        $html_description = "";
        if (isset($args['description'])) {
            $description_arg = htmlspecialchars(str_replace("\n", " ", $args['description']));
            $html_description = "<meta name='Description' content='{$description_arg}'>";
        }

        $no_robots = isset($_GET['archiv']) || ($args['norobots'] ?? false);
        $olz_organization_data = OlzOrganizationData::render([]);

        $additional_headers = implode("\n", $args['additional_headers'] ?? []);

        $out .= "<!DOCTYPE html>
        <html lang='de'>
        <head>
        <meta http-equiv='cache-control' content='public'>
        <meta http-equiv='content-type' content='text/html;charset=utf-8'>
        <meta name='Keywords' content='OL, Orientierungslauf, Sport, Laufsport, Gruppe, Klub, Verein, Zimmerberg, linkes Zürichseeufer, Sihltal, Kilchberg, Rüschlikon, Thalwil, Gattikon, Oberrieden, Horgen, Au ZH, Wädenswil, Richterswil, Schönenberg, Hirzel, Langnau am Albis, Adliswil, Stadt Zürich, Leimbach, Wollishofen, Enge, Friesenberg, Üetliberg, Entlisberg, Albis, Buchenegg, Landforst, Kopfholz, Chopfholz, Reidholz, Schweiz, OLZ, OLG'>
        {$html_description}
        <meta name='Content-Language' content='de'>
        {$refresh}
        ".($no_robots ? "<meta name='robots' content='noindex, nofollow'>" : "")."
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>{$html_title}</title>
        <link rel='shortcut icon' href='{$_CONFIG->getCodeHref()}favicon.ico' />
        {$olz_organization_data}
        {$additional_headers}
        <link rel='stylesheet' href='{$css_href}' />
        <script type='text/javascript' src='{$js_href}' onload='olz.loaded()'></script>
        </head>";
        $out .= "<body class='olz-override-root'>\n";
        $out .= "<a name='top'></a>";

        $out .= OlzHeaderBar::render([
            'skip_auth_menu' => $args['skip_auth_menu'] ?? false,
            'skip_top_boxes' => $args['skip_top_boxes'] ?? false,
        ]);

        $out .= "<div class='site-container'>";
        $out .= "<div class='site-background'>";

        if (!($args['skip_counter'] ?? false)) {
            $counter_repo = $entityManager->getRepository(Counter::class);
            $counter_repo->record(
                $_SERVER['REQUEST_URI'] ?? '',
                $_DATE,
                $_SERVER['HTTP_REFERER'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            );
        }

        return $out;
    }
}
