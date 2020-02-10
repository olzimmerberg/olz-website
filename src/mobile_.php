<?php

include_once "admin/check.php";
include_once "admin/olz_init.php";
include_once "admin/olz_functions.php";
include_once "image_tools.php";

echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 TRANSITIONAL//EN'>
<html>

    <head>
        <meta name='viewport' content='width=device-width; initial-scale=1.0; maximum-scale=1.0; user-resizable = no;'>
        <meta http-equiv='content-type' content='text/html;charset=utf-8'>
        <meta name='Keywords' content='OL Orientierungslauf Zimmerberg'>
        <meta name='Description' content='Homepage der Orientierungsl&auml;uferInnen Zimmerberg'>
        <meta name='Content-Language' content='de'>
        <title>OL Zimmerberg</title>
        <link rel='shortcut icon' href='favicon.ico'>
        <link rel='stylesheet' href='mobile.css'>
        <script type='text/javascript' src='mobile.js'></script>
    </head>
    <body>";

if ($_GET["page"] == "aktuell" && isset($_GET["id"])) {
    echo "<div class='header' style='margin-top:0px; text-align:center;'><a href='?'><img src='icns/olzschatten.png' style='height:32px; margin:2px;'></a></div>";

    $res = mysql_query("SELECT id, datum, zeit, titel, text, textlang FROM aktuell WHERE id='".intval($_GET["id"])."'");
    $row = mysql_fetch_array($res);
    echo "<div class='date'>".date("d.m.Y", strtotime($row["datum"]))."</div>";
    echo "<div class='title'>".$row["titel"]."</div>";
    echo "<div class='text'>".$row["text"]."<br><br>".$row["textlang"]."</div>";
} elseif ($_GET["page"] == "galerie" && isset($_GET["id"])) {
    echo "<div class='header' style='margin-top:0px; text-align:center;'><a href='?'><img src='icns/olzschatten.png' style='height:32px; margin:2px;'></a></div>";

    for ($i = 1; $i < 1000; $i++) {
        echo $i."<br>";
    }
} else {
    echo "<div class='header' style='margin-top:0px; text-align:center;'><img src='icns/olzschatten.png' style='height:32px; margin:2px;'></div>";

    echo "<div class='header'>Aktuell</div>";
    $res = mysql_query("SELECT id, datum, zeit, titel, text FROM aktuell WHERE (on_off='1' AND typ NOT LIKE 'box%') ORDER BY datum DESC");
    for ($i = 0; $i < 5; $i++) {
        $row = mysql_fetch_array($res);
        echo "<a href='?page=aktuell&id=".$row["id"]."'><div class='entry'>";
        if (is_file("img/aktuell/".$row["id"]."/img/001.jpg")) {
            echo "<div style='width:64px; height:64px; float:left; margin:0px 5px 0px 0px;'>".olz_image("aktuell", $row["id"], 1, 64, false)."</div>";
        }
        echo "<div class='date'>".date("d.m.Y", strtotime($row["datum"]))."</div>";
        echo "<div class='title'>".$row["titel"]."</div>";
        echo "</div></a>";
    }

    echo "<div class='header'>Galerie</div>";
    $res = mysql_query("SELECT id, datum, titel, autor FROM galerie WHERE (on_off='1' AND typ='foto') ORDER BY datum DESC");
    for ($i = 0; $i < 5; $i++) {
        $row = mysql_fetch_array($res);
        echo "<a href='?page=galerie&id=".$row["id"]."'><div style='overflow-x:hidden;' class='entry'>";
        if (is_file("img/galerie/".$row["id"]."/img/001.jpg")) {
            echo "<div style='width:64px; height:64px; float:left; margin:0px 5px 0px 0px;'>".olz_image("galerie", $row["id"], 1, 64, false)."</div>";
        }
        echo "<div class='date'>".date("d.m.Y", strtotime($row["datum"]))."</div>";
        echo "<div class='title'>".$row["titel"]."</div>";
        echo "</div></a>";
    }

    echo "<div class='header'>Forum</div>";
    $res = mysql_query("SELECT id, datum, name, eintrag FROM forum WHERE on_off='1' ORDER BY datum DESC");
    for ($i = 0; $i < 5; $i++) {
        $row = mysql_fetch_array($res);
        echo "<div style='overflow-x:hidden;' class='entry'>";
        echo "<div class='date'>".date("d.m.Y", strtotime($row["datum"]))."</div>";
        echo "<div class='title'>".$row["name"]."</div>";
        echo "<div class='text'>".$row["eintrag"]."</div>";
        echo "</div>";
    }

    echo "<div class='header'>Termine</div>";
    $res = mysql_query("SELECT id, datum, zeit, titel, text FROM termine WHERE (on_off='1' AND typ NOT LIKE 'box%') ORDER BY datum DESC");
    for ($i = 0; $i < 5; $i++) {
        $row = mysql_fetch_array($res);
        echo "<div class='entry'>";
        if (is_file("img/aktuell/".$row["id"]."/img/001.jpg")) {
            echo "<div style='width:64px; height:64px; float:left; margin:0px 5px 0px 0px;'>".olz_image("aktuell", $row["id"], 1, 64, false)."</div>";
        }
        echo "<div class='date'>".date("d.m.Y", strtotime($row["datum"]))."</div>";
        echo "<div class='title'>".$row["titel"]."</div>";
        echo "</div>";
    }
}

echo "  </body>
</html>";

// Funktionen

function textbeginn($text, $maxlen) {
    if (strlen($text) <= $maxlen or $maxlen == 0) {
        return $text;
    }
    $text_tmp = substr($text, 0, $maxlen - 2);
    for ($i = strlen($text_tmp) - 1; $i > 0; $i--) {
        if (substr($text_tmp, $i, 1) == " ") {
            return substr($text_tmp, 0, $i)."...";
        }
    }

    return "...";
}
