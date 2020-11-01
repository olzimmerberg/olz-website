<?php

require_once __DIR__.'/../../../config/paths.php';

$js_modified = filemtime("{$code_path}jsbuild/olz.min.js");
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
        \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv='cache-control' content='public'>
<meta http-equiv='content-type' content='text/html;charset=utf-8'>
<meta name='Keywords' content='OL Orientierungslauf Zimmerberg'>
<meta name='Description' content='Homepage der OrientierungsläuferInnen Zimmerberg'>
<meta name='Content-Language' content='de'>".$refresh."
".(isset($_GET['archiv']) ? "<meta name='robots' content='noindex, nofollow'>" : "")."
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>OL Zimmerberg{$html_titel}</title>
<link rel='shortcut icon' href='".$code_href."favicon.ico'>
<script type='text/javascript' src='jsbuild/olz.min.js?modified={$js_modified}' onload='olz.loaded()'></script>
</head>";
echo "<body class='olz-override-root'>\n";
echo "<a name='top'></a>";

include __DIR__.'/../olz_header_bar/olz_header_bar.php';

echo "<div class='site-container'>";
echo "<div class='site-background'>";
