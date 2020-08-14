<?php

// =============================================================================
// Zeigt die wichtigsten Informationen möglichst übersichtlich an.
// =============================================================================

?>

<script type='text/javascript'>
function handleClick(ev) {
ev = ev || window.event;
ev.stopPropagation();
ev.cancelBubble = true;
}
</script>
<!--EINSTEIGER-->
<div style='padding-bottom:10px; text-align:center; font-size:14px; font-weight:bold;background-color:#D4E7CE;padding-top:10px;margin-bottom:10px;border-bottom:1px solid #007521;border-top:1px solid #007521;'>
    Neu hier? <a href='?page=18' class='linkint' style='font-size:14px;'>Hier gehts zur Seite für Einsteiger</a>
</div>

<!--ZIMMERBERG OL-->

<!--<div style='margin:0px;padding-bottom:10px;background-color:#D4E7CE;padding-top:10px;margin-bottom:10px;border-bottom:1px solid #007521;border-top:1px solid #007521;'>
    <div style='padding:0px;margin:0px;margin-left:auto;margin-right:auto;text-align:center;font-size:14px;border:solid 0px;overflow:hidden;'>
        <div style='padding:0px;margin:auto;text-align:center;font-weight:bold;overflow:auto;padding-bottom:20px;'>
        <a href='?page=11' style='font-size:18px;' class='linkint'>2. Nationaler OL - 12. Zimmerberg OL - Sprint-Staffel für alle</a></div>

        <table style='table-layout:fixed;'>
            <tr>
                <td style='width:100%;padding:0px 5px;'>
                    <div style='padding:0px;margin:auto;text-align:center;font-weight:bold;overflow:auto;'>-->
<?php
/* 4 Zufallsbilder aus Gallerie anzeigen */
/*$id = "1239";
$link = "?page=4&amp;id=".$id;
$text = "";
$rand = array();
$pfad_galerie = $data_path."img/galerie/";
for ($i=1; is_file($pfad_galerie.$id."/img/".str_pad($i ,3, '0', STR_PAD_LEFT).".jpg"); $i++);$groesse = ($i-1);
    for ($i=0; $i<((4<$groesse)?4:$groesse); $i++) {
        $randtmp = str_pad(rand(1,$groesse),3,"0",STR_PAD_LEFT);
        while (array_search($randtmp,$rand)!==false) {
            $randtmp = rand(1,$groesse);}
        array_push($rand,$randtmp);
        $text .= "<td style='border:0px;margin:0px;padding:0px;height:80px;text-align:center;'>".olz_image("galerie", $id, $randtmp, 110, "image")."</td>";
    }
$text = "<table><tr>".$text."</tr></table>";
echo $text;*/
?>
<!--<a href='http://olzimmerberg.ch/?page=4&id=1239' class='linkint' style='font-size:18px;'>mehr Bilder...</a>
<div style='text-align:center;padding-top:20px;'><a href='https://vimeo.com/camedia/review/336365663/a86a7e1946' target='_blank'><img src='/img/zol_richterswil_2019/zol_movie_1.png' style='width:200px;margin-right:5px;'></a><a href='https://www.dropbox.com/s/oab0bzqgueqpe1q/12.Zimmerberg%20OL.mp4?dl=0' target='_blank'><img src='/img/zol_richterswil_2019/zol_movie_2.png' style='width:200px;margin-left:5px;'></a></div>
</div>
    <div style='padding:0px;margin:auto;text-align:center;font-weight:bold;overflow:auto;padding-top:20px;'>
        <a href='https://www.o-l.ch/cgi-bin/results?rl_id=4971' class='linkext' style='font-size:18px;'>Resultate 2. Nat. OL</a>
        <a href='https://www.o-l.ch/cgi-bin/results?rl_id=4976' class='linkext' style='font-size:18px;' target='_blank'>Resultate Sprintstaffel</a></div>
    <div style='padding:0px;margin:auto;text-align:center;font-weight:bold;overflow:auto;padding-top:20px;'>
        <a href='?page=11&typ=fundgegenstaende' class='linkext' style='font-size:18px;'>Fundgegenstände/Objets trouvés</a></div>
                </td>
            </tr>
        </table>
    </div>
</div>-->

<div style='padding-bottom:10px; text-align:center;font-weight:bold;background-color:#D4E7CE;padding-top:10px;margin-bottom:10px;border-bottom:1px solid #007521;border-top:1px solid #007521'>
    <a href='/files/aktuell/504/008.pdf?modified=1596523116' class='linkpdf' style='font-size:14px;'>1. Zimmerberg Berg-OL - 15. August 2020 (Stand 3.8.2020)</a><br>
    <a href='/files/aktuell/504/009.pdf?modified=1596523121' class='linkpdf' style='font-size:14px;'>Schutzkonzept</a><br>
    <a href='https://www.o-l.ch/cgi-bin/results?type=start&rl_id=5371' class='linkext' style='font-size:14px;'>Startliste</a>
</div>
<!-- <div style='padding-bottom:10px; text-align:center;font-weight:bold;background-color:#D4E7CE;padding-top:10px;margin-bottom:10px;border-bottom:1px solid #007521;border-top:1px solid #007521;height:110px;'><img src='img/richterswil10_thumb.jpg' style='margin-left:25px;float:left;'><img src='img/richterswil07_thumb.jpg' style='margin-right:25px;float:right;'>
<div style='margin-left:140px;margin-right:140px;text-align:center;font-size:14px;border:solid 0px;'>9. Zimmerberg OL - 10. April 2016<br>
<a href='http://www.o-l.ch/cgi-bin/results?rl_id=3373' class='linkext' style='font-size:14px;' target='_blank'>Resultate</a><br>
<a href='?page=4&id=663' class='linkint' style='font-size:14px;'>Fotos</a><br>
<a href='?page=11#fotool' class='linkint' style='font-size:14px;'>Auflösung Foto-OL</a><br>
<a href='?page=11#fundsachen' class='linkint' style='font-size:14px;'>Fundgegenstände</a>
</div>
</div> -->

<?php
require_once "image_tools.php";
require_once "file_tools.php";

// Konstanten
$listenlaenge = 20;
$textlaenge_def = 300;
$aktuell_typ = "aktuell"; // für Spezialseiten z.B. 'lager09', 'jwoc2008'

if ($aktuell_typ != 'aktuell') {
    $sql = "SELECT * from aktuell WHERE typ='{$aktuell_typ}' ORDER BY datum DESC";
} else {
    $sql = "
(SELECT id,datum,zeit,titel,text,'aktuell' AS typ,'' AS f1,'' AS f2,'' AS f3,'' AS f4,'' AS f5,'' AS f6,textlang AS f7,'' AS linkext FROM aktuell WHERE (on_off='1' AND typ NOT LIKE 'box%'))
UNION ALL
(SELECT id,datum,zeit,titel,text,'blog' AS typ,autor AS f1,'' AS f2,'' AS f3,'','','','',linkext FROM blog WHERE (on_off='1') AND (titel!='') AND (text!=''))
UNION ALL
(SELECT id,datum,zeit,'' AS titel,eintrag AS text,'forum' AS typ,name AS f1,name2 AS f2,'' AS f3,'','','','','' FROM forum WHERE (on_off='1' and eintrag!=''))
UNION ALL
(SELECT id,datum,'00:00:00' AS zeit,titel,'' AS text,'galerie' AS typ,'' AS f1,'' AS f2,typ AS f3,'','','','','' FROM galerie WHERE (on_off='1'))
ORDER BY datum DESC, zeit DESC LIMIT {$listenlaenge}";
}

$result = $db->query($sql);
while ($row = $result->fetch_assoc()) {
    $datum = $row['datum'];
    $zeit = $row['zeit'];
    $zugriff = "0";
    $edit_admin = "";
    $id = $row['id'];
    $thistype = $row['typ'];
    $titel = $row['titel'];
    $text = $row['text'];

    if ($thistype == "blog") { // Tabelle 'blog'
        $autor = $row['f1'];
        $linkext = $row['linkext'];
        $link = ($linkext > "") ? $linkext : "index.php?page=7&amp;id=".$id."#id".$id;
        $icon = "icns/blog.png";
        $titel = "Kaderblog ".ucwords($autor).": ".$titel;

        // Dateicode aus Text entfernen
        preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $tmptext = $matches[4][$i];
            $text = str_replace($matches[0][$i], "", $text);
        }

        $text = mb_substr($text, 0, $textlaenge_def);
        $text = mb_substr($text, 0, mb_strrpos($text, " "));
        $text = $text." (...)";
        if (($_SESSION['auth'] == "all") or ((in_array($thistype, preg_split("/ /", $_SESSION['auth']))) and (ucwords($_SESSION['user']) == ucwords($autor)))) {
            $edit_admin = "<img src='icns/edit.gif' onclick='javascript:location.href=\"index.php?page=7&amp;id={$id}&amp;buttonblog=start\";return false;' class='noborder' alt=''>";
        }

        $bild = olz_image("blog", $id, 1, 110, false, " style='float:left; margin:0px 5px 0px 0px;'");
        $text = str_replace("<BILD1>", $bild, $text);

    // Dateicode einfügen
       /* preg_match_all("/<datei([0-9]+)(\s+text=(\"|\')([^\"\']+)(\"|\'))?([^>]*)>/i", $text, $matches);
        for ($i=0; $i<count($matches[0]); $i++) {
            $tmptext = $matches[4][$i];
            $text = str_replace($matches[0][$i], $tmptext, $text);
        }*/
    } elseif ($thistype == "forum") { // Tabelle 'forum'
        $titel = $row['f1'];
        $name = ($row['f2'] > "") ? "(".$row['f2'].") " : "";
        $text = make_expandable($name.$text);
        $link = "?page=5#id".$id;
        $icon = "icns/bubble.png";
        $titel = "Forum: ".$titel;
        if (($_SESSION['auth'] == "all") or (in_array($thistype, preg_split("/ /", $_SESSION['auth'])))) {
            $edit_admin = "<img src='icns/edit.gif' onclick='javascript:location.href=\"index.php?page=5&amp;id={$id}&amp;buttonforum=start\";return false;' class='noborder' alt=''>";
        }
    } elseif ($thistype == "galerie") { // Tabelle 'galerie'
        $pfad = $row['id'];
        $typ = $row['f3'];
        $link = "?page=4&amp;id=".$id;
        $icon = "icns/foto.png";
        if (($_SESSION['auth'] == "all") or (in_array($thistype, preg_split("/ /", $_SESSION['auth'])))) {
            $edit_admin = "<img src='icns/edit.gif' onclick='javascript:location.href=\"index.php?page=5&amp;id={$id}&amp;buttonforum=start\";return false;' class='noborder' alt=''>";
        }
        $text = "";
        if ($pfad && $typ == "foto") {
            $rand = [];
            $pfad_galerie = $data_path."img/galerie/";
            for ($i = 1; is_file($pfad_galerie.$id."/img/".str_pad($i, 3, '0', STR_PAD_LEFT).".jpg"); $i++);
            $groesse = ($i - 1);
            for ($i = 0; $i < (($groesse > 4) ? 4 : $groesse); $i++) {
                $randtmp = str_pad(rand(1, $groesse), 3, "0", STR_PAD_LEFT);
                while (array_search($randtmp, $rand) !== false) {
                    $randtmp = rand(1, $groesse);
                }
                array_push($rand, $randtmp);
                $text .= "<td>".olz_image("galerie", $id, $randtmp, 110, "image")."</td>";
            }
        }
        if ($typ == 'foto') {
            $text = "<table><tr class='thumbs'>".$text."</tr></table>";
            $titel = "Galerie: ".$titel;
        } elseif ($typ == 'movie') {
            $text = "<a href='".$link."' style='width:144px;background-color:#000;padding-top:0;' class='thumb paragraf'>\n
            <span style='display:block;background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;height:24px;'></span>\n
            <span style='display:block;text-align:center;'><img src='".$data_href."img/galerie/".$id."/img/001.jpg' style='width:110px;' class='noborder' alt=''></span>\n
            <span style='display:block;background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;height:24px;'></span>\n
            </a>";
            $titel = "Film: ".$titel;
            $icon = "icns/movie.png";
        }
    } else { // Tabelle 'aktuell'
        $textlang = $row['f7'];
        $link = "?page=2&amp;id=".$id;
        $icon = "icns/star.png";
        $titel = "Aktuell: ".$titel;
        if (($_SESSION['auth'] == "all") or (in_array($thistype, preg_split("/ /", $_SESSION['auth'])))) {
            $edit_admin = "<img src='icns/edit.gif' onclick='javascript:location.href=\"index.php?page=2&amp;id={$id}&amp;buttonaktuell=start\";return false;' class='noborder' alt=''>";
        }
        if ($aktuell_typ != 'aktuell') {
            $text = $row['textlang'];
        }
        if (strip_tags($textlang) > '') {
            $text .= " [...]";
        }

        // Bildercode einfügen
        preg_match_all("/<bild([0-9]+)(\\s+size=([0-9]+))?([^>]*)>/i", $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $size = intval($matches[3][$i]);
            if ($size < 1) {
                $size = 110;
            }
            $tmp_html = olz_image("aktuell", $id, intval($matches[1][$i]), $size, "image", " class='box' style='float:left;clear:left;margin:3px 5px 3px 0px;'");
            $text = str_replace($matches[0][$i], $tmp_html, $text);
        }

        // Dateicode einfügen
        preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $tmptext = $matches[4][$i];
            $text = str_replace($matches[0][$i], $tmptext, $text);
        }
    }
    //if ($thistype!='galerie') $text = "<a href='".$link."' style='display:block;color:#000000;' class='paragraf'>".$text."</a>";

    echo get_eintrag($icon, $datum, $edit_admin.$titel, $text, $link);
    /*
    echo"<div style='clear:left; overflow:hidden; cursor:pointer; border-radius:3px; padding:5px;' onmouseover='this.style.backgroundColor=\"#D4E7CE\";' onmouseout='this.style.backgroundColor=\"\";' onclick='javascript:location.href=\"$link\";return false;'>
    <a href='".$link."' class='titel' style='display:block;'><span style='float:left;width:24px;'><img src='icns/".$icon."' class='noborder' alt=''></span><span style='vertical-align:bottom;color:#000;padding-right:15px;'>".$edit_admin.$titel."</span><span style='float:right;padding-left:2px;text-align:right;color:#000;'>".olz_date("tt.mm.jj",$datum)."</span></a>
    ".$text."</div>";
    */
/* TEST ICONS
    echo"<div style='clear:left; overflow:hidden; cursor:pointer; border-radius:3px; padding:5px;background-image:url(icns/_$icon);background-position:3% 6%;' onmouseover='this.style.backgroundColor=\"#D4E7CE\";' onmouseout='this.style.backgroundColor=\"\";' onclick='javascript:location.href=\"$link\";return false;'>
    <a href='".$link."' class='titel' style='display:block;'><span style='float:left;width:24px;'></span><span style='vertical-align:bottom;color:#000;padding-right:15px;padding-left:75px;'>".$edit_admin.$titel."</span><span style='float:right;padding-left:2px;text-align:right;color:#000;'>".olz_date("tt.mm.jj",$datum)."</span></a>
    ".$text."</div>";*/
}
?>
