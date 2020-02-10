<?php

$db_table = "karten";
$karten_typ = [
    'OL-Karten' => 'ol',
    'Dorf-Karten' => 'stadt',
    'sCOOL-Karten' => 'scool', ];

//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth'] == "all") or (in_array($db_table, preg_split("/ /", $_SESSION['auth'])))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}
$button_name = "button".$db_table;
if (isset(${$button_name})) {
    $_SESSION['edit']['db_table'] = $db_table;
}

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($id) and is_ganzzahl($id)) {
    $_SESSION[$db_table."id_"] = $id;
}
$id = $_SESSION[$db_table.'id_'];

//-------------------------------------------------------------
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
$function = array_search(${$button_name}, $functions);
if ($function != "") {
    include 'admin/admin_db.php';
}
if ($_SESSION['edit']['table'] == $db_table) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}

//-------------------------------------------------------------
// MENÜ
if ($zugriff and $db_edit == "0") {
    if ($alert != "") {
        echo "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
    }
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neue Karte", "0"]], "")."</div>";
}

//-------------------------------------------------------------
//  VORSCHAU - LISTE
if (($db_edit == "0") or ($do == "vorschau")) {
    if ($do == "vorschau") {
        $sql = "SELECT * FROM {$db_table} WHERE (id = ".$_SESSION[$db_table."id"].")";
    } // Proforma-Abfrage
    else {
        $sql = "SELECT * FROM {$db_table} ORDER BY CASE WHEN `typ` = 'ol' THEN 1 WHEN `typ` = 'stadt' THEN 2 WHEN `typ` = 'scool' THEN 3 ELSE 4 END,ort ASC, position ASC";
    }
    //echo $sql;
    $result = $db->query($sql);
    $tmp_typ = "";
    $tmp_tag = "";

    while ($row = mysqli_fetch_array($result)) {
        if ($do == "vorschau") {
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
            $edit_admin = "<a href='index.php?id={$id}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
        } else {
            $edit_admin = "";
        }

        if ($massstab == "") {
            $massstab = "&nbsp;";
        }

        //$thumb_name = strtolower(str_replace(array("ä","ö","ü","-"," ","/"),array("ae","oe","ue","_","_","_"),$name)."_".$jahr."_".preg_replace("[^0-9]", "",substr($massstab,2))).".jpg";
        //if (file_exists("img/karten/".$thumb_name)){
        if ($thumb > "") {
            $img_info_gross = getimagesize("img/karten/".$thumb);
            $img_width = $img_info_gross[0];
            $img_height = $img_info_gross[1];
            $map = "<img src='icns/lupe.gif' style='float:right;border:none;' onmouseover=\"trailOn('{$data_href}img/karten/{$thumb}','{$name}','{$jahr}','','','','','{$img_width}','{$img_height}','','','".str_replace("'", "\\'", $massstab)."',".$kartennr.");\" onmouseout=\"hidetrail();\">";
        //$map = "<img src='icns/lupe.gif' style='float:right;border:none;' onmouseover=\"trailOn('{$data_href}img/karten/$thumb','$name','$jahr','','','','','$center_x','$center_y','','','$massstab','---');\" onmouseout=\"hidetrail();\">";}
        } else {
            $map = '';
        }

        if ($typ == 'ol') {
            $icon = 'ol.gif';
        } elseif ($typ == 'stadt') {
            $icon = 'ol2.gif';
        } elseif ($typ == 'scool') {
            $icon = 'ol1.gif';
        }
        if ($typ != $tmp_typ) {
            echo $tmp_tag."<h2><img src='icns/".$icon."' class='noborder' style='margin-right:10px;vertical-align:bottom;'>".array_search($typ, $karten_typ)."</h2><table class='liste'>";
        }
        if ($center_x > 0) {
            echo "<tr><td>{$edit_admin}<a href='#{$name}' onclick='goto({$center_x},{$center_y},{$zoom},\"".$name."\");return false' class='linkmap'>{$name}</a>{$map}</td><td>{$massstab}</td><td>{$jahr}</td></tr>";
        } else {
            echo "<tr><td>{$edit_admin}{$name}</td><td>{$massstab}</td><td>{$jahr}</td></tr>";
        }
        $tmp_tag = "</table>";
        $tmp_typ = $typ;
    }
    echo "</table>";
}
