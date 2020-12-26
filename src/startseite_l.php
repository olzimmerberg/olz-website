<?php

// =============================================================================
// Zeigt die wichtigsten Informationen möglichst übersichtlich an.
// =============================================================================

require_once __DIR__.'/config/paths.php';
require_once __DIR__.'/config/database.php';

?>

<!--EINSTEIGER-->
<div class='banner'>
    Neu hier? <a href='fuer_einsteiger.php' class='linkint'>Hier gehts zur Seite für Einsteiger</a>
</div>

<?php

require_once "image_tools.php";
require_once "file_tools.php";

$banner_text = get_olz_text(22);
if ($banner_text !== '') {
    echo "<div class='banner'>";
    echo $banner_text;
    echo "</div>";
}

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
        $link = ($linkext > "") ? $linkext : "blog.php?id=".$id."#id".$id;
        $icon = "icns/entry_type_blog_20.svg";
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
            $edit_admin = "<img src='icns/edit_16.svg' onclick='javascript:location.href=\"blog.php?id={$id}&amp;buttonblog=start\";return false;' class='noborder' alt=''>";
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
        $link = "forum.php#id".$id;
        $icon = "icns/entry_type_forum_20.svg";
        $titel = "Forum: ".$titel;
        if (($_SESSION['auth'] == "all") or (in_array($thistype, preg_split("/ /", $_SESSION['auth'])))) {
            $edit_admin = "<img src='icns/edit_16.svg' onclick='javascript:location.href=\"forum.php?id={$id}&amp;buttonforum=start\";return false;' class='noborder' alt=''>";
        }
    } elseif ($thistype == "galerie") { // Tabelle 'galerie'
        $pfad = $row['id'];
        $typ = $row['f3'];
        $link = "galerie.php?id=".$id;
        $icon = "icns/entry_type_gallery_20.svg";
        if (($_SESSION['auth'] == "all") or (in_array($thistype, preg_split("/ /", $_SESSION['auth'])))) {
            $edit_admin = "<img src='icns/edit_16.svg' onclick='javascript:location.href=\"galerie.php?id={$id}&amp;buttonforum=start\";return false;' class='noborder' alt=''>";
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
            $icon = "icns/entry_type_movie_20.svg";
        }
    } else { // Tabelle 'aktuell'
        $textlang = $row['f7'];
        $link = "aktuell.php?id=".$id;
        $icon = "icns/entry_type_aktuell_20.svg";
        $titel = "Aktuell: ".$titel;
        if (($_SESSION['auth'] == "all") or (in_array($thistype, preg_split("/ /", $_SESSION['auth'])))) {
            $edit_admin = "<img src='icns/edit_16.svg' onclick='javascript:location.href=\"aktuell.php?id={$id}&amp;buttonaktuell=start\";return false;' class='noborder' alt=''>";
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
