<?php

require_once __DIR__.'/../../../config/paths.php';

$js_modified = filemtime("{$code_path}jsbuild/olz.min.js");

// TODO: Remove all of this, once the index.php?page=... syntax is not used anymore.
$canonical_tag = '';
$is_insecure_nonlocal = !$_SERVER['HTTPS'] && preg_match('/olzimmerberg\.ch/', $_SERVER['HTTP_HOST']);
if ($is_insecure_nonlocal) {
    $host = $_SERVER['HTTP_HOST'];
    $request_uri = $_SERVER['REQUEST_URI'];
    $canonical_tag = "<link rel='canonical' href='https://{$host}{$request_uri}'>";
}
$is_request_to_index_php = preg_match("/\\/index.php/", $_SERVER['REQUEST_URI']);
if ($is_request_to_index_php) {
    $pages = [
        "0" => "error.php", // TO DO
        "1" => "startseite.php",
        "2" => "aktuell.php",
        "3" => "termine.php",
        "4" => "galerie.php",
        "5" => "forum.php",
        "6" => "kontakt.php",
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
        "99" => "results.php",
        "100" => "profil.php",
        "mail" => "divmail.php",
        "ftp" => "webftp.php",
        "tools" => "termine_helper.php",
    ];
    $canonical_page = $pages[$_GET['page']];
    if ($canonical_page) {
        $host = $_SERVER['HTTP_HOST'];
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
        $canonical_tag = "<link rel='canonical' href='https://{$host}{$code_href}{$canonical_page}{$query}'>";
    }
}
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
        \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv='cache-control' content='public'>
<meta http-equiv='content-type' content='text/html;charset=utf-8'>
<meta name='Keywords' content='OL, Orientierungslauf, Sport, Laufsport, Gruppe, Klub, Verein, Zimmerberg, linkes Zürichseeufer, Sihltal, Kilchberg, Rüschlikon, Thalwil, Gattikon, Oberrieden, Horgen, Au ZH, Wädenswil, Richterswil, Schönenberg, Hirzel, Langnau am Albis, Adliswil, Stadt Zürich, Leimbach, Wollishofen, Enge, Friesenberg, Üetliberg, Entlisberg, Albis, Buchenegg, Landforst, Kopfholz, Chopfholz, Reidholz, Schweiz, OLZ, OLG'>
<meta name='Description' content='Die OL Zimmerberg ist ein Orientierungslauf-Verein in der Region Zimmerberg am linken Zürichseeufer und im Sihltal.'>
<meta name='Content-Language' content='de'>".$refresh."
".(isset($_GET['archiv']) ? "<meta name='robots' content='noindex, nofollow'>" : "")."
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>OL Zimmerberg{$html_titel}</title>
<link rel='shortcut icon' href='".$code_href."favicon.ico'>
{$canonical_tag}
<script type='text/javascript' src='jsbuild/olz.min.js?modified={$js_modified}' onload='olz.loaded()'></script>
</head>";
echo "<body class='olz-override-root'>\n";
echo "<a name='top'></a>";

include __DIR__.'/../olz_header_bar/olz_header_bar.php';

echo "<div class='site-container'>";
echo "<div class='site-background'>";
