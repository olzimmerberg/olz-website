<?php

// =============================================================================
// Neuigkeiten von unseren Leistungssportlern.
// =============================================================================

use Olz\Utils\DbUtils;
use Olz\Utils\FileUtils;
use Olz\Utils\ImageUtils;

global $_DATE;

require_once __DIR__.'/config/paths.php';
require_once __DIR__.'/config/date.php';

$db = DbUtils::fromEnv()->getDb();
$image_utils = ImageUtils::fromEnv();
$file_utils = FileUtils::fromEnv();

// echo "<h2></h2><div style='overflow-x:auto;'><table class='galerie'><tr class='thumbs blog'>";
// $kader = array(array("lilly","Lilly Gross"),array("julia","Julia Gross"),array("tanja","Tanja Frey"),array("sara","Sara Würmli"),array("paula","Paula Gross"));
// $kader = array(array("lilly","Lilly Gross"),array("julia","Julia Gross"),array("florian","Florian Attinger"),array("sara","Sara Würmli"),array("paula","Paula Gross"));
// $kader = array(array("lilly","Lilly Gross"),array("julia","<a href='http://juliagross.ch' class='linkext' target='blank'>Julia Gross</a>"),array("florian","Florian Attinger"),array("paula","Paula Gross"),array("michael","Michael Felder"));
// shuffle($kader);
// foreach($kader as $member)
//    {echo "<td><img src='".$data_href."img/".$member[0].".jpg' alt=''><div style='padding-top:5px;text-align:center;'>".$member[1]."</div></td>";
//    }
// echo "</tr></table></div><h2></h2>";

// -------------------------------------------------------------
// ZUGRIFF
if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}

// -------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($id) and is_ganzzahl($id)) {
    $_SESSION[$db_table."id_"] = $id;
    $sql = "UPDATE {$db_table} SET counter=(counter+1) WHERE (id = '{$id}')";
    $db->query($sql);
} else {
    $id = $_SESSION[$db_table.'id_'] ?? null;
}

// -------------------------------------------------------------
// BEARBEITEN
if ($zugriff) {
    $functions = ['neu' => 'Neuer Eintrag',
        'edit' => 'Bearbeiten',
        'abbruch' => 'Abbrechen',
        'replace' => 'Überschreiben',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'deletebild1' => 'Bild 1 entfernen',
        'deletebild2' => 'Bild 2 entfernen',
        'deletefile1' => 'Download 1 entfernen',
        'deletefile2' => 'Download 2 entfernen',
        'start' => 'start',
        'undo' => 'undo',
        'zurück' => 'Zurück', ];
} else {
    $functions = [];
}
$function = array_search($_POST[$button_name] ?? null, $functions);
if ($function != "") {
    include __DIR__.'/admin/admin_db.php';
}
if (($_SESSION['edit']['table'] ?? null) == $db_table) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}

echo "<div class='alert alert-info' role='alert'><b>Der Kaderblog zieht um, er ist jetzt unter \"Aktuell > Format: Kaderblog\" zu finden.</b><br>Im Moment bitte keine neuen Beiträge erstellen.</div>";

// -------------------------------------------------------------
// MENÜ
if ($zugriff and $db_edit == "0") {
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
}

// -------------------------------------------------------------
//  VORSCHAU - LISTE
if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
    if ($zugriff) {
        if (($do ?? null) == 'vorschau') {
            $sql = "WHERE (id = ".$id.")";
        } elseif (($_SESSION['auth'] ?? null) == 'all') {
            $sql = "";
        } else {
            $sql = " WHERE (autor='".ucwords($_SESSION['user'])."') OR (on_off='1')";
        }
    } else {
        $sql = "WHERE (on_off = '1') AND (text != '')";
    }
    $sql = "SELECT * FROM {$db_table} ".$sql." ORDER BY datum DESC, zeit DESC";

    $result = $db->query($sql);
    while ($row = mysqli_fetch_array($result)) {
        if (($do ?? null) == 'vorschau') {
            $row = $vorschau;
        }
        $autor = ucwords($row['autor']);
        $titel = $row['titel'];
        $text = $row['text'];
        $bild1 = $row['bild1'] ?? '';
        $bild2 = $row['bild2'] ?? '';
        $file1 = $row['file1'] ?? '';
        $file1_name = $row['file1_name'] ?? '';
        $file2 = $row['file2'] ?? '';
        $file2_name = $row['file2_name'] ?? '';
        $datum = $row['datum'];
        $zeit = $row['zeit'];
        $id_tmp = $row['id'];
        $on_off = $row['on_off'];
        $counter = $row['counter'] ?? 0;
        $linkext = $row['linkext'] ?? '';

        $text = str_replace(["<br />", "<br>\r\n<br>"], ["<br>", "<p/>"], stripslashes(nl2br($text)));
        // $text = stripslashes(nl2br($text));
        $text = olz_find_url($text);
        $zeit = date("G:i", strtotime($zeit));

        if ((($do ?? null) != 'vorschau') and ((($_SESSION['auth'] ?? null) == 'all') or (ucwords($_SESSION['user'] ?? '') == ucwords($autor)))) {
            $edit_admin = "<a href='blog.php?id={$id_tmp}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
        }
        // if ($zugriff AND (($do ?? null) != 'vorschau')) $edit_admin = "<a href='blog.php?id=$id_tmp&$button_name=start' class='linkedit'>&nbsp;</a>";
        else {
            $edit_admin = "";
        }

        if ($on_off == 0) {
            $class = " class='error'";
        } else {
            $class = "";
        }

        // Bildcode einfügen
        $tmp_html = $image_utils->olzImage($db_table, $id_tmp, 1, 240, "gallery[blog".$id_tmp."]", " style='float:left; margin:3px 5px 3px 0px;'");
        $text = str_replace("<BILD1>", $tmp_html, $text);
        $tmp_html = $image_utils->olzImage($db_table, $id_tmp, 2, 240, "gallery[blog".$id_tmp."]", " style='float:left; margin:3px 5px 3px 0px;'");
        $text = str_replace("<BILD2>", $tmp_html, $text);

        // Dateicode einfügen
        preg_match_all("/<datei([0-9]+)(\\s+text=(\"|\\')([^\"\\']+)(\"|\\'))?([^>]*)>/i", $text, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $tmptext = $matches[4][$i];
            if (mb_strlen($tmptext) < 1) {
                $tmptext = "Datei ".$matches[1][$i];
            }
            $tmp_html = $file_utils->olzFile($db_table, $id_tmp, intval($matches[1][$i]), $tmptext);
            $text = str_replace($matches[0][$i], $tmp_html, $text);
        }

        include_once __DIR__.'/library/phpWebFileManager/icons.inc.php';
        if ($file1 != "") {
            $ext = strtolower(end(explode(".", $file1)));
            $icon = $fm_cfg['icons']['ext'][$ext];
            if ($icon != "" and $ext !== 'pdf') {
                $icon = "<img src='/assets/icns/".$icon."' class='noborder' style='margin-right:4px;vertical-align:middle;'>";
            } else {
                $icon = "";
            }
            if (file_exists($def_folder."/".$file1)) {
                $path = $def_folder;
            } else {
                $path = "temp";
            }
            if ($file1_name == "") {
                $file1_name = "Download";
            }
            $text = str_replace("<DL1>", $icon."<a href='{$path}/{$file1}' target='_blank'>{$file1_name}</a>", $text);
        }
        if ($file2 != "") {
            $ext = strtolower(end(explode(".", $file2)));
            $icon = $fm_cfg['icons']['ext'][$ext];
            if ($icon != "" and $ext !== 'pdf') {
                $icon = "<img src='/assets/icns/".$icon."' class='noborder' style='margin-right:4px;vertical-align:middle;'>";
            } else {
                $icon = "";
            }
            if (file_exists($def_folder."/".$file2)) {
                $path = $def_folder;
            } else {
                $path = "temp";
            }
            if ($file2_name == "") {
                $file1_name = "Download";
            }
            $text = str_replace("<DL2>", $icon."<a href='{$path}/{$file2}' target='_blank'>{$file2_name}</a>", $text);
        }

        echo "<h2 style='clear:left;padding-top:20px;' id='id{$id_tmp}'>".$edit_admin.$autor.": ".$titel."</h2>";
        echo "<div class='nobox'><p><b>(".$_DATE->olzDate("t.m.jj", $datum)."/{$zeit})</b></p><div class='lightgallery'>".$text."</div>";
        if ($linkext > '') {
            echo "<br><a href='{$linkext}' target='_blank' class='linkext'>... weiterlesen</a>";
        }
        echo "</div>";
    }
}
