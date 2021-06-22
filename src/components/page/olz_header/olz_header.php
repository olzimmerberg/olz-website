<?php

function olz_header($args = []): string {
    global $_CONFIG, $_SERVER;

    require_once __DIR__.'/../../../config/server.php';

    $is_insecure_nonlocal = !($_SERVER['HTTPS'] ?? false) && preg_match('/olzimmerberg\.ch/', $_SERVER['HTTP_HOST']);
    $host_has_www = preg_match('/www\./', $_SERVER['HTTP_HOST']);
    $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    if ($is_insecure_nonlocal || $host_has_www) {
        $request_uri = $_SERVER['REQUEST_URI'];
        require_once __DIR__.'/../../../utils/client/HttpUtils.php';
        HttpUtils::fromEnv()->redirect("https://{$host}{$request_uri}", 308);
    }

    // TODO: Remove this, once the index.php?page=... syntax is not used anymore.
    $canonical_tag = '';
    $is_request_to_index_php = preg_match("/\\/index.php/", $_SERVER['REQUEST_URI']) || preg_match("/(\\?|\\&)page=/", $_SERVER['REQUEST_URI']);
    if ($is_request_to_index_php) {
        $pages = [
            "0" => "error.php", // TO DO
            "1" => "startseite.php",
            "2" => "aktuell.php",
            "3" => "termine.php",
            "4" => "galerie.php",
            "5" => "forum.php",
            "6" => "verein.php",
            "7" => "blog.php",
            "8" => "service.php",
            "9" => "search.php",
            "10" => "login.php",
            "11" => "zimmerbergol.php",
            "12" => "karten.php",
            "15" => "termine_tools_DEV.php",
            "16" => "zol/index.php",
            "18" => "fuer_einsteiger.php",
            "19" => "zol/karten.php",
            "20" => "trophy.php",
            "21" => "material.php",
            "100" => "profil.php",
            "mail" => "divmail.php",
            "ftp" => "webftp.php",
            "tools" => "termine_helper.php",
        ];
        $canonical_page = $pages[$_GET['page']];
        if ($canonical_page) {
            $get_params = [];
            foreach ($_GET as $key => $value) {
                if ($key != 'page') {
                    $get_params[$key] = $value;
                }
            }
            $query = http_build_query($get_params);
            if (strlen($query) > 0) {
                $query = "?{$query}";
            }
            $redirect_uri = "https://{$host}{$_CONFIG->getCodeHref()}{$canonical_page}{$query}";
            require_once __DIR__.'/../../../utils/client/HttpUtils.php';
            HttpUtils::fromEnv()->redirect($redirect_uri, 308);
        }
    }

    return olz_header_without_routing($args);
}

function olz_header_without_routing($args = []): string {
    global $_CONFIG, $_DATE, $_SERVER, $entityManager;
    $out = '';

    require_once __DIR__.'/../../../config/date.php';
    require_once __DIR__.'/../../../config/doctrine_db.php';
    require_once __DIR__.'/../../../config/server.php';
    require_once __DIR__.'/../../../model/index.php';

    $css_path = "{$_CONFIG->getCodePath()}jsbuild/main.min.css";
    $js_path = "{$_CONFIG->getCodePath()}jsbuild/main.min.js";
    $css_modified = is_file($css_path) ? filemtime($css_path) : 0;
    $js_modified = is_file($js_path) ? filemtime($js_path) : 0;

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
        $description_arg = htmlspecialchars($args['description']);
        $html_description = "<meta name='Description' content='{$description_arg}'>";
    }

    $no_robots = isset($_GET['archiv']) || ($args['norobots'] ?? false);

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
    <link rel='manifest' href='{$_CONFIG->getRootHref()}manifest.json' />
    {$additional_headers}
    <link rel='stylesheet' href='{$_CONFIG->getCodeHref()}jsbuild/main.min.css?modified={$css_modified}' />
    <script type='text/javascript' src='{$_CONFIG->getCodeHref()}jsbuild/main.min.js?modified={$js_modified}' onload='olz.loaded()'></script>
    </head>";
    $out .= "<body class='olz-override-root'>\n";
    $out .= "<a name='top'></a>";

    require_once __DIR__.'/../olz_header_bar/olz_header_bar.php';
    $out .= olz_header_bar();

    $out .= "<div class='site-container'>";
    $out .= "<div class='site-background'>";

    $counter_repo = $entityManager->getRepository(Counter::class);
    $counter_repo->record(
        $_SERVER['REQUEST_URI'] ?? '',
        $_DATE,
        $_SERVER['HTTP_REFERER'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    );

    return $out;
}
