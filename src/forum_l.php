<?php

// =============================================================================
// Unser Forum, wo Mitglieder und Besucher Einträge schreiben können.
// =============================================================================

require_once __DIR__.'/config/database.php';
require_once __DIR__.'/config/date.php';

//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth'] == "all") or (in_array($db_table, preg_split("/ /", $_SESSION['auth'])))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($code)) {
    $uid = $code;
}
if (isset($id) and is_ganzzahl($id)) {
    $_SESSION[$db_table."id_"] = $id;
} else {
    $id = $_SESSION[$db_table."id_"];
}
if (isset($jahr) and in_array($jahr, $jahre)) {
    $_SESSION[$db_table."jahr_"] = $jahr;
} else {
    $jahr = $_SESSION[$db_table."jahr_"];
}
if ($jahr == "") {
    $_SESSION[$db_table.'jahr_'] = $_DATE_UTILS->olzDate("jjjj", "");
}
if (isset($monat) and in_array($monat, $monate)) {
    $_SESSION[$db_table."monat_"] = $monat;
} else {
    $monat = $_SESSION[$db_table."monat_"];
}
if ($monat == "") {
    $_SESSION[$db_table.'monat_'] = "alle";
}
$id = $_SESSION[$db_table.'id_'];
$jahr = $_SESSION[$db_table.'jahr_'];
$monat = $_SESSION[$db_table.'monat_'];

//-------------------------------------------------------------
// BEARBEITEN
if ($zugriff) {
    $functions = ['neu' => 'Neuer Eintrag',
        'code' => 'Eintrag bearbeiten',
        'edit' => 'Bearbeiten',
        'start_user' => 'Weiter',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'start' => 'start',
        'undo' => 'undo',
        'zurück' => 'Zurück', ];
} else {
    $functions = ['neu' => 'Neuer Eintrag',
        'code' => 'Eintrag bearbeiten',
        'edit' => 'Bearbeiten',
        'start_user' => 'Weiter',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'undo' => 'undo', ];
}
$function = array_search($_POST[$button_name], $functions);
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
if ($db_edit == "0") {
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"], ["Eintrag bearbeiten", "1"]], "")."</div>";
    if ($zugriff) {
        echo "<div class='buttonbar'>".olz_buttons("jahr", $jahre, $_SESSION[$db_table."jahr_"])."</div>";
        echo "<div class='buttonbar'>".olz_buttons("monat", $monate, $_SESSION[$db_table."monat_"])."</div>";
    }
}

//-------------------------------------------------------------
//  VORSCHAU - LISTE
if (($db_edit == "0") or ($do == "vorschau")) {
    if (isset($id_forum)) {
        $sql = "WHERE (id={$id_forum})";
    } elseif ($zugriff) {
        if ($_SESSION[$db_table."monat_"] == "alle") {
            $sql = "WHERE (YEAR(datum)='".$jahr."')";
        } else {
            $sql = "WHERE (MONTH(datum)='".(array_search($monat, $monate) + 1)."') AND (YEAR(datum)='{$jahr}')";
        }
    } else {
        $sql = "WHERE (datum >= '".(date("Y") - 1).date("-m-d")."') AND (on_off = '1') AND (email > '') AND (name > '') AND (eintrag != '')";
    }
    if ($do == "vorschau") {
        $sql = "WHERE (id = ".$_SESSION[$db_table.'id'].")";
    }
    $sql = "SELECT * FROM {$db_table} ".$sql." ORDER BY datum DESC, zeit DESC";
    $result = $db->query($sql);
    echo "<table style='width:100%; table-layout:fixed;' class='liste'><tr><td style='width:20%; border:0px;'></td><td style='width:80%; border:0px;'></td></tr>";
    //echo $vorschau[0]['name']."***";
    while ($row = mysqli_fetch_array($result)) {
        if ($do == "vorschau") {
            $row = $vorschau;
        }
        $titel = $row['name'];
        $name = $row['name2'];
        $email = $row['email'];
        $eintrag = olz_mask_email(strip_tags($row['eintrag']), "", "");
        $datum = $row['datum'];
        $zeit = $row['zeit'];
        $id = $row['id'];
        $on_off = $row['on_off'];
        $eintrag = str_replace("<br />", "<br>", stripslashes(nl2br($eintrag)));
        // simon, 17.5.2011 LINKS ERKENNEN
        $matches = "";
        preg_match_all("/(https?\\:\\/\\/[^ \\<\\>]*)/", $eintrag, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $class = "linkext";
            if (strpos($matches[0][$i], "olzimmerberg.ch") !== false) {
                $class = "linkint";
            } elseif (strpos($matches[0][$i], "maps.google") !== false) {
                $class = "linkmap";
            } elseif (strpos($matches[0][$i], "map.search.ch") !== false) {
                $class = "linkmap";
            } elseif (strpos($matches[0][$i], "sbb.ch") !== false) {
                $class = "linkoev";
            } elseif (strpos($matches[0][$i], ".pdf") === strlen($matches[0][$i]) - 4) {
                $class = "linkpdf";
            } elseif (strpos($matches[0][$i], ".png") === strlen($matches[0][$i]) - 4) {
                $class = "linkimg";
            } elseif (strpos($matches[0][$i], ".jpg") === strlen($matches[0][$i]) - 4) {
                $class = "linkimg";
            } elseif (strpos($matches[0][$i], ".jpeg") === strlen($matches[0][$i]) - 5) {
                $class = "linkimg";
            } elseif (strpos($matches[0][$i], ".gif") === strlen($matches[0][$i]) - 4) {
                $class = "linkimg";
            } elseif ($row['allowHTML']) {
                $class = 'linkext';
            }

            if ($class == 'linkimg' and $row['allowHTML']) {
                $eintrag = str_replace($matches[0][$i], "<img src='".$matches[0][$i]."'>", $eintrag);
            } else {
                $eintrag = str_replace($matches[0][$i], "<a href='".$matches[0][$i]."' class='".$class."' target='_blank'>".$matches[0][$i]."</a>", $eintrag);
            }
        }
        $zeit = date("G:i", strtotime($zeit));

        if ($zugriff and ($do != 'vorschau')) {
            $edit_admin = "<a href='forum.php?id={$id}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
        } else {
            $edit_admin = "";
        }

        if (($on_off == 0) and ($do != "vorschau")) {
            $class = " class='error'";
        } else {
            $class = "";
        }

        echo "<div>".olz_monate($datum)."</div>";
        echo "<tr{$class}><td style='{$style}'>".$edit_admin."<a name='id{$id}'></a><b>".$_DATE_UTILS->olzDate("tt. MM", $datum)."</b><br>(".$zeit.")</td>\n<td style='overflow-x:auto; {$style}'><b>\n";
        //echo olz_mask_email($email,$titel,"Forumeintrag OL Zimmerberg")."</b><p>".$name.$eintrag."</p></td></tr>\n";
        if ($name > "") {
            echo $titel."</b><p>".olz_mask_email($email, $name, $titel)."| ".$eintrag."</p></td></tr>\n";
        } else {
            echo olz_mask_email($email, $titel, "Forumeintrag OL Zimmerberg")."</b><p>".$eintrag."</p></td></tr>\n";
        }
    }
    echo "</table>";
}
