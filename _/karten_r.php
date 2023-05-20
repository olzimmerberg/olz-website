<?php

// =============================================================================
// Das Verzeichnis unserer Karten.
// =============================================================================

use Olz\Components\Schema\OlzMapData\OlzMapData;
use Olz\Utils\DbUtils;

require_once __DIR__.'/config/paths.php';

$db = DbUtils::fromEnv()->getDb();

$karten_typ = [
    'OL-Karten' => 'ol',
    'Dorf-Karten' => 'stadt',
    'sCOOL-Karten' => 'scool', ];

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
}
$id = $_SESSION[$db_table.'id_'] ?? null;

// -------------------------------------------------------------
// BEARBEITEN
if ($zugriff) {
    $functions = ['neu' => 'Neue Karte',
        'edit' => 'Bearbeiten',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
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

// -------------------------------------------------------------
// MENÜ
if ($zugriff and $db_edit == "0") {
    if (($alert ?? "") != "") {
        echo "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
    }
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neue Karte", "0"]], "")."</div>";
}

// -------------------------------------------------------------
//  VORSCHAU - LISTE
if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
    if (($do ?? null) == 'vorschau') {
        $sql = "SELECT * FROM {$db_table} WHERE (id = ".$_SESSION[$db_table."id"].")";
    } // Proforma-Abfrage
    else {
        $sql = "SELECT * FROM {$db_table} ORDER BY CASE WHEN `typ` = 'ol' THEN 1 WHEN `typ` = 'stadt' THEN 2 WHEN `typ` = 'scool' THEN 3 ELSE 4 END,ort ASC, position ASC";
    }
    // echo $sql;
    $result = $db->query($sql);
    $tmp_typ = "";
    $tmp_tag = "";

    while ($row = mysqli_fetch_array($result)) {
        if (($do ?? null) == 'vorschau') {
            $row = $vorschau;
        }
        $name = $row['name'];
        $typ = $row['typ'];
        $id = $row['id'];
        $position = $row['position'];
        $massstab = $row['massstab'];
        $jahr = $row['jahr'];
        $kartennr = ($row['kartennr'] > 0) ? $row['kartennr'] : "'---'";
        $center_x = $row['center_x'];
        $center_y = $row['center_y'];
        $zoom = $row['zoom'];
        $ort = $row['ort'];
        $thumb = $row['vorschau'];

        if ($typ == "scool") {
            $name = $name." (".$ort.")";
        }

        if ($zugriff) {
            $edit_admin = "<a href='karten.php?id={$id}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
        } else {
            $edit_admin = "";
        }

        if ($massstab == "") {
            $massstab = "&nbsp;";
        }

        // $thumb_name = strtolower(str_replace(array("ä","ö","ü","-"," ","/"),array("ae","oe","ue","_","_","_"),$name)."_".$jahr."_".preg_replace("[^0-9]", "",substr($massstab,2))).".jpg";
        // if (file_exists("img/karten/".$thumb_name)){
        if ($thumb > "") {
            $img_info_gross = getimagesize($data_path."img/karten/".$thumb);
            $img_width = $img_info_gross[0];
            $img_height = $img_info_gross[1];
            $img_href = "{$data_href}img/karten/{$thumb}";
            $map = "<span class='lightgallery'><a href='{$img_href}' data-src='{$img_href}'><img src='/assets/icns/magnifier_16.svg' style='float:right;border:none;'></a></span>";
        // $map = "<img src='/assets/icns/magnifier_16.svg' style='float:right;border:none;' onmouseover=\"olz.trailOn('{$data_href}img/karten/$thumb','$name','$jahr','','','','','$center_x','$center_y','','','$massstab','---');\" onmouseout=\"olz.hidetrail();\">";}
        } else {
            $map = '';
        }

        if ($typ == 'ol') {
            $icon = 'orienteering_forest_16.svg';
        } elseif ($typ == 'stadt') {
            $icon = 'orienteering_village_16.svg';
        } elseif ($typ == 'scool') {
            $icon = 'orienteering_scool_16.svg';
        }
        if ($typ != $tmp_typ) {
            echo $tmp_tag."<h2><img src='/assets/icns/".$icon."' class='noborder' style='margin-right:10px;vertical-align:bottom;'>".array_search($typ, $karten_typ)."</h2><table class='liste'>";
        }
        echo OlzMapData::render([
            'name' => $name,
            'year' => $jahr,
            'scale' => $massstab,
        ]);
        if ($center_x > 0) {
            echo <<<ZZZZZZZZZZ
            <tr>
                <td>{$edit_admin}<a href='#{$name}' onclick='goto({$center_x},{$center_y},{$zoom},&quot;{$name}&quot;);return false' class='linkmap' itemprop='name'>{$name}</a>{$map}</td>
                <td>{$massstab}</td>
                <td>{$jahr}</td>
            </tr>
            ZZZZZZZZZZ;
        } else {
            echo <<<ZZZZZZZZZZ
            <tr>
                <td>{$edit_admin}<span class='linkmap' itemprop='name'>{$name}</span></td>
                <td>{$massstab}</td>
                <td>{$jahr}</td>
            </tr>
            ZZZZZZZZZZ;
        }
        $tmp_tag = "</table>";
        $tmp_typ = $typ;
    }
    echo "</table>";
}
