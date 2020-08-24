<?php

// =============================================================================
// Diverse weitere Funktionen.
// =============================================================================

require_once "file_tools.php";

echo "<table><tr><td style='width:50%'><h2>Links</h2>";

$db_table = "links";

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
if (isset($id) and is_ganzzahl($id) and ($_SESSION['edit']['db_table'] == $db_table)) {
    $_SESSION[$db_table."id_"] = $id;
}
//$id = $_SESSION[$db_table.'id_'];

//-------------------------------------------------------------
// BEARBEITEN
if ($zugriff) {
    $functions = ['neu' => 'Neuer Eintrag',
        'edit' => 'Bearbeiten',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'start' => 'start',
        'undo' => 'undo',
        'up' => 'up',
        'down' => 'down',
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
//$_SESSION[$db_table."id_"] = $id;

//-------------------------------------------------------------
// MENÜ
if ($zugriff and $db_edit == "0") {
    if ($alert != "") {
        echo "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
    }
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
}

//-------------------------------------------------------------
//  VORSCHAU - LISTE
$sql = "SELECT * from {$db_table} ORDER BY position";
$result = $db->query($sql);
$next_pos = mysqli_num_rows($result) + 1;

if ($zugriff) {
    if ($db_edit == "1") {
        $sql = "SELECT * FROM {$db_table} WHERE (id='".$_SESSION[$db_table."id_"]."') ORDER BY position ASC";
    } else {
        $sql = "SELECT * FROM {$db_table} ORDER BY  position ASC";
    }
} else {
    $sql = "SELECT * FROM {$db_table} WHERE (on_off='1') ORDER BY  position ASC";
}

if (($db_edit == "0") or ($do == "vorschau")) {
    $result = $db->query($sql);
    echo "<ul class='nobox'>";
    while ($row = mysqli_fetch_array($result)) {
        if ($do == "vorschau") {
            $row = $vorschau;
        }
        $id_tmp = $row['id'];
        $name = $row['name'];
        $url = $row['url'];
        if ($zugriff and ($do != 'vorschau')) {
            $edit_admin = "<a href='index.php?id={$id_tmp}&amp;{$button_name}=up' style='margin-right:4px;'><img src='icns/up_16.svg' class='noborder'></a><a href='index.php?id={$id_tmp}&amp;{$button_name}=down' style='margin-right:4px;'><img src='icns/down_16.svg' class='noborder'></a><a href='index.php?id={$id_tmp}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
        } else {
            $edit_admin = "";
        }

        if ($on_off == 0) {
            $class = " class='error'";
        } else {
            $class = "";
        }

        if ($name == "dummy") {
            if ($zugriff) {
                echo $edit_admin."-----Trennlinie-----";
            } else {
                echo "<br>";
            }
        } elseif ($db_edit == "0") {
            echo "<li>".$edit_admin.$icon."<a href='{$url}' class='linkext' target='_blank'>{$name}</a></li>";
        } else {
            echo "<table class='liste'><tr><td style='font-weight:bold;width:20%;'>Bezeichnung:</td><td>{$name}</td></tr>";
            echo "<tr><td style='font-weight:bold;'>URL:</td><td>{$icon}<a href='{$url}' class='linkext' target='_blank'>{$url}</a></td></tr></table>";
        }
    }
    echo "</ul>";
}
?>

</td><td style='width:50%'><h2>Downloads</h2>

<?php
$db_table = "downloads";
$def_folder = "downloads";

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
if (isset($id) and is_ganzzahl($id) and ($_SESSION['edit']['db_table'] == $db_table)) {
    $_SESSION[$db_table."id_"] = $id;
}
//$id = $_SESSION[$db_table.'id_'];

//-------------------------------------------------------------
// BEARBEITEN
if ($zugriff) {
    $functions = ['neu' => 'Neuer Eintrag',
        'edit' => 'Bearbeiten',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'replace' => 'Überschreiben',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'deletefile' => 'Datei entfernen',
        'start' => 'start',
        'undo' => 'undo',
        'up' => 'up',
        'down' => 'down',
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
//$_SESSION[$db_table."id_"] = $id;

//-------------------------------------------------------------
// MENÜ
if ($zugriff and $db_edit == "0") {
    if ($alert != "") {
        echo "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
    }
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
}

//-------------------------------------------------------------
//  VORSCHAU - LISTE
$sql = "SELECT * from {$db_table} ORDER BY position";
$result = $db->query($sql);
$next_pos = mysqli_num_rows($result) + 1;

if ($zugriff) {
    if ($db_edit == "1") {
        $sql = "SELECT * FROM {$db_table} WHERE (id='".$_SESSION[$db_table."id_"]."') ORDER BY position ASC";
    } else {
        $sql = "SELECT * FROM {$db_table} ORDER BY  position ASC";
    }
} else {
    $sql = "SELECT * FROM {$db_table} WHERE (on_off='1') ORDER BY  position ASC";
}

if (($db_edit == "0") or ($do == "vorschau")) {
    $result = $db->query($sql);
    echo "<ul class='nobox'>";
    while ($row = mysqli_fetch_array($result)) {
        if ($do == "vorschau") {
            $row = $vorschau;
        }
        $id_tmp = $row['id'];
        $name = $row['name'];
        $typ = $row['typ'];
        $file1 = $row['file1'];
        if ($zugriff and ($do != 'vorschau')) {
            $edit_admin = "<a href='index.php?id={$id_tmp}&amp;{$button_name}=up' style='margin-right:4px;'><img src='icns/up_16.svg' class='noborder'></a><a href='index.php?id={$id_tmp}&amp;{$button_name}=down' style='margin-right:4px;'><img src='icns/down_16.svg' class='noborder'></a><a href='index.php?id={$id_tmp}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
        } else {
            $edit_admin = "";
        }

        if ($on_off == 0) {
            $class = " class='error'";
        } else {
            $class = "";
        }

        include 'library/phpWebFileManager/icons.inc.php';
        $var = explode(".", $file1);
        $ext = strtolower(end($var));
        $icon = $fm_cfg['icons']['ext'][$ext];
        if ($ext != "" and $ext !== 'pdf') {
            $icon = "<img src='icns/".$icon."' class='noborder' style='margin-right:6px;vertical-align:middle;'>";
        } else {
            $icon = "";
        }

        if ($name == "dummy") {
            if ($zugriff) {
                echo $edit_admin."-----Trennlinie-----";
            } else {
                echo "<br>";
            }
        } elseif ($db_edit == "0") {
            echo "<li>".$edit_admin./*$icon."<a href='$def_folder/$file1' target='_blank'>$name</a>".*/olz_file($db_table, $id_tmp, 1, $name)."</li>";
        } else {
            echo "<table class='liste'><tr><td style='font-weight:bold;width:20%;'>Bezeichnung:</td><td>{$name}</td></tr>";
            echo "<tr><td style='font-weight:bold;'>Dateiname:</td><td>{$icon}<a href='{$tmp_folder}/{$file1}' target='_blank'>{$file1}</a></td></tr></table>";
        }
    }
    echo "</ul>";
}
    echo "</td></tr></table><br><br>";

    // Zielsprint 2020
    include 'zielsprint20.php';
?>
