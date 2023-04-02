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
        $icon = "icns/entry_type_kaderblog_20.svg";
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
        $news_entry->setContent($textlang);
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
