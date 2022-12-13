<?php

// =============================================================================
// Zeigt die wichtigsten Informationen möglichst übersichtlich an.
// =============================================================================

use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;
use Olz\Entity\News\NewsEntry;
use Olz\News\Components\OlzNewsListItem\OlzNewsListItem;
use Olz\Utils\DbUtils;
use Olz\Utils\ImageUtils;

global $_DATE;

require_once __DIR__.'/config/paths.php';
require_once __DIR__.'/config/date.php';

$db = DbUtils::fromEnv()->getDb();
$image_utils = ImageUtils::fromEnv();

// Konstanten
$listenlaenge = 20;
$textlaenge_def = 300;
$aktuell_typ = "aktuell"; // für Spezialseiten z.B. 'lager09', 'jwoc2008'

if ($aktuell_typ != 'aktuell') {
    $sql = "SELECT * from aktuell WHERE typ='{$aktuell_typ}' ORDER BY datum DESC";
} else {
    $sql = "
(SELECT id,datum,zeit,titel,text,'aktuell' AS typ,image_ids AS f1,typ AS f2,'' AS f3,'' AS f4,'' AS f5,'' AS f6,textlang AS f7,'' AS linkext FROM aktuell WHERE (on_off='1' AND typ NOT LIKE 'box%'))
UNION ALL
(SELECT id,datum,zeit,titel,text,'blog' AS typ,autor AS f1,'' AS f2,'' AS f3,'','','','',linkext FROM blog WHERE (on_off='1') AND (titel!='') AND (text!=''))
UNION ALL
(SELECT id,datum,zeit,'' AS titel,eintrag AS text,'forum' AS typ,name AS f1,name2 AS f2,'' AS f3,'','','','','' FROM forum WHERE (on_off='1' and eintrag!=''))
ORDER BY datum DESC, zeit DESC LIMIT {$listenlaenge}";
}

echo "<h4 class='tablebar'>News</h4>";

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
        if ((($_SESSION['auth'] ?? null) == 'all') or (in_array($thistype, preg_split('/ /', $_SESSION['auth'] ?? '')) and (ucwords($_SESSION['user']) == ucwords($autor)))) {
            $edit_admin = "<img src='icns/edit_16.svg' onclick='location.href=\"blog.php?id={$id}&amp;buttonblog=start\";return false;' class='noborder' alt=''>";
        }

        $bild = $image_utils->olzImage("blog", $id, 1, 110, 'image', " style='float:left; margin:0px 5px 0px 0px;'");
        $text = str_replace("<BILD1>", $bild, $text);

        // Dateicode einfügen
        /* preg_match_all("/<datei([0-9]+)(\s+text=(\"|\')([^\"\']+)(\"|\'))?([^>]*)>/i", $text, $matches);
         for ($i=0; $i<count($matches[0]); $i++) {
             $tmptext = $matches[4][$i];
             $text = str_replace($matches[0][$i], $tmptext, $text);
         }*/

        echo OlzPostingListItem::render([
            'icon' => $icon,
            'date' => $datum,
            'title' => $edit_admin.$titel,
            'text' => $text,
            'link' => $link,
        ]);
    } elseif ($thistype == "forum") { // Tabelle 'forum'
        $titel = $row['f1'];
        $name = ($row['f2'] > "") ? "(".$row['f2'].") " : "";
        $text = make_expandable($name.$text);
        $link = "forum.php#id".$id;
        $icon = "icns/entry_type_forum_20.svg";
        $titel = "Forum: ".$titel;
        if ((($_SESSION['auth'] ?? null) == 'all') or in_array($thistype, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
            $edit_admin = "<img src='icns/edit_16.svg' onclick='location.href=\"forum.php?id={$id}&amp;buttonforum=start\";return false;' class='noborder' alt=''>";
        }

        echo OlzPostingListItem::render([
            'icon' => $icon,
            'date' => $datum,
            'title' => $edit_admin.$titel,
            'text' => $text,
            'link' => $link,
        ]);
    } elseif ($thistype == "galerie") { // Tabelle 'galerie'
        $pfad = $row['id'];
        $typ = $row['f3'];
        $link = "galerie.php?id=".$id;
        $icon = "icns/entry_type_gallery_20.svg";
        if ((($_SESSION['auth'] ?? null) == 'all') or in_array($thistype, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
            $edit_admin = "<img src='icns/edit_16.svg' onclick='location.href=\"galerie.php?id={$id}&amp;buttonforum=start\";return false;' class='noborder' alt=''>";
        }
        $text = "";
        if ($pfad && $typ == "foto") {
            $rand = [];
            $pfad_galerie = $data_path."img/galerie/";
            for ($i = 1; is_file($pfad_galerie.$id."/img/".str_pad($i, 3, '0', STR_PAD_LEFT).".jpg"); $i++) {
            }
            $groesse = ($i - 1);
            for ($i = 0; $i < (($groesse > 4) ? 4 : $groesse); $i++) {
                $randtmp = str_pad(rand(1, $groesse), 3, "0", STR_PAD_LEFT);
                while (array_search($randtmp, $rand) !== false) {
                    $randtmp = rand(1, $groesse);
                }
                array_push($rand, $randtmp);
                $text .= "<td class='test-flaky'>".$image_utils->olzImage("galerie", $id, $randtmp, 110, 'image')."</td>";
            }
        }
        if ($typ == 'foto') {
            $text = "<table><tr class='thumbs'>".$text."</tr></table>";
            $titel = "Galerie: ".$titel;
        } elseif ($typ == 'movie') {
            $text = "<div href='".$link."' style='background-color:#000;padding-top:0;' class='thumb paragraf'>\n
            <span style='display:block;background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;height:24px;'></span>\n
            <span style='display:block;text-align:center;'><img src='".$data_href."img/galerie/".$id."/img/001.jpg' style='width:110px;' class='noborder' alt=''></span>\n
            <span style='display:block;background-image:url(icns/movie_dot.gif);background-repeat:repeat-x;height:24px;'></span>\n
            </div>";
            $titel = "Film: ".$titel;
            $icon = "icns/entry_type_movie_20.svg";
        }

        echo OlzPostingListItem::render([
            'icon' => $icon,
            'date' => $datum,
            'title' => $edit_admin.$titel,
            'text' => $text,
            'link' => $link,
        ]);
    } else { // Tabelle 'aktuell'
        $textlang = $row['f7'];
        $image_ids = $row['f1'];
        $format = $row['f2'];
        if ((($_SESSION['auth'] ?? null) == 'all') or in_array($thistype, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
            $edit_admin = "<img src='icns/edit_16.svg' onclick='location.href=\"aktuell.php?id={$id}&amp;buttonaktuell=start\";return false;' class='noborder' alt=''>";
        }
        if ($aktuell_typ != 'aktuell') {
            $text = $row['textlang'];
        }
        if (strip_tags($textlang) > '') {
            $text .= " [...]";
        }

        $news_entry = new NewsEntry();
        $news_entry->setFormat($format);
        $news_entry->setDate($datum);
        $news_entry->setTitle($edit_admin.$titel);
        $news_entry->setTeaser($text);
        $news_entry->setId($id);
        $news_entry->setImageIds($image_ids ? json_decode($image_ids, true) : null);

        echo OlzNewsListItem::render(['news_entry' => $news_entry]);
    }
    // if ($thistype!='galerie') $text = "<a href='".$link."' style='display:block;color:#000000;' class='paragraf'>".$text."</a>";

    /*
    echo"<div style='clear:left; overflow:hidden; cursor:pointer; border-radius:3px; padding:5px;' onmouseover='this.style.backgroundColor=\"#D4E7CE\";' onmouseout='this.style.backgroundColor=\"\";' onclick='location.href=\"$link\";return false;'>
    <a href='".$link."' class='titel' style='display:block;'><span style='float:left;width:24px;'><img src='icns/".$icon."' class='noborder' alt=''></span><span style='vertical-align:bottom;color:#000;padding-right:15px;'>".$edit_admin.$titel."</span><span style='float:right;padding-left:2px;text-align:right;color:#000;'>".$_DATE->olzDate("tt.mm.jj",$datum)."</span></a>
    ".$text."</div>";
    */
    /* TEST ICONS
        echo"<div style='clear:left; overflow:hidden; cursor:pointer; border-radius:3px; padding:5px;background-image:url(icns/_$icon);background-position:3% 6%;' onmouseover='this.style.backgroundColor=\"#D4E7CE\";' onmouseout='this.style.backgroundColor=\"\";' onclick='location.href=\"$link\";return false;'>
        <a href='".$link."' class='titel' style='display:block;'><span style='float:left;width:24px;'></span><span style='vertical-align:bottom;color:#000;padding-right:15px;padding-left:75px;'>".$edit_admin.$titel."</span><span style='float:right;padding-left:2px;text-align:right;color:#000;'>".$_DATE->olzDate("tt.mm.jj",$datum)."</span></a>
        ".$text."</div>";*/
}
