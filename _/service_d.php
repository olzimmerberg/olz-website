<?php

// =============================================================================
// Diverse weitere Funktionen.
// =============================================================================

use Olz\Components\Apps\OlzAppsList\OlzAppsList;
use Olz\Utils\DbUtils;
use Olz\Utils\FileUtils;

$db = DbUtils::fromEnv()->getDb();
$file_utils = FileUtils::fromEnv();

echo "<form name='Formularl' method='post' action='service.php#id_edit".($_SESSION['id_edit'] ?? '')."' enctype='multipart/form-data'>";

echo "<h1>Service</h1>";
echo "<h2>Apps</h2>";
echo OlzAppsList::render();
echo "<br /><br />";

echo "<div class='responsive-flex'>";
echo "<div class='responsive-flex-2'>";
echo "<h2>Links</h2>";

$db_table = 'links';

// -------------------------------------------------------------
// ZUGRIFF
if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}
$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
    $id = $_GET['id'] ?? null;
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

// -------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($id) and is_ganzzahl($id) and ($_SESSION['edit']['db_table'] == $db_table)) {
    $_SESSION[$db_table."id_"] = $id;
}
// $id = $_SESSION[$db_table.'id_'] ?? null;

// -------------------------------------------------------------
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
$function = array_search($_POST[$button_name] ?? null, $functions);
if ($function != "") {
    include __DIR__.'/admin/admin_db.php';
}
if (($_SESSION['edit']['table'] ?? null) == $db_table) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}
// $_SESSION[$db_table."id_"] = $id;

// -------------------------------------------------------------
// MENÜ
if ($zugriff and $db_edit == "0") {
    if (($alert ?? '') != '') {
        echo "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
    }
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
}

// -------------------------------------------------------------
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

if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
    $result = $db->query($sql);
    echo "<ul class='nobox'>";
    while ($row = mysqli_fetch_array($result)) {
        if (($do ?? null) == 'vorschau') {
            $row = $vorschau;
        }
        $id_tmp = $row['id'];
        $name = $row['name'];
        $url = $row['url'];
        $on_off = $row['on_off'];
        if ($zugriff and (($do ?? null) != 'vorschau')) {
            $edit_admin = "<a href='service.php?id={$id_tmp}&amp;{$button_name}=up' style='margin-right:4px;'><img src='/assets/icns/up_16.svg' class='noborder'></a><a href='service.php?id={$id_tmp}&amp;{$button_name}=down' style='margin-right:4px;'><img src='/assets/icns/down_16.svg' class='noborder'></a><a href='service.php?id={$id_tmp}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
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
            echo "<li>".$edit_admin."<a href='{$url}' class='linkext' target='_blank'>{$name}</a></li>";
        } else {
            echo "<table class='liste'><tr><td style='font-weight:bold;width:20%;'>Bezeichnung:</td><td>{$name}</td></tr>";
            echo "<tr><td style='font-weight:bold;'>URL:</td><td><a href='{$url}' class='linkext' target='_blank'>{$url}</a></td></tr></table>";
        }
    }
    echo "</ul>";
}

echo "</div>";
echo "<div class='responsive-flex-2'>";
echo "<h2>Downloads</h2>";

$db_table = 'downloads';
$def_folder = 'downloads';

// -------------------------------------------------------------
// ZUGRIFF
if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}
$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
    $id = $_GET['id'] ?? null;
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

// -------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($id) and is_ganzzahl($id) and ($_SESSION['edit']['db_table'] == $db_table)) {
    $_SESSION[$db_table."id_"] = $id;
}
// $id = $_SESSION[$db_table.'id_'] ?? null;

// -------------------------------------------------------------
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

$function = array_search($_POST[$button_name] ?? null, $functions);
if ($function != "") {
    include __DIR__.'/admin/admin_db.php';
}
if (($_SESSION['edit']['table'] ?? null) == $db_table) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}
// $_SESSION[$db_table."id_"] = $id;

// -------------------------------------------------------------
// MENÜ
if ($zugriff and $db_edit == "0") {
    if (($alert ?? '') != '') {
        echo "<div class='buttonbar'><span class='error'>".$alert."</span></div>\n";
    }
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
}

// -------------------------------------------------------------
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

if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
    $result = $db->query($sql);
    echo "<ul class='nobox'>";
    while ($row = mysqli_fetch_array($result)) {
        if (($do ?? null) == 'vorschau') {
            $row = $vorschau;
        }
        $id_tmp = $row['id'];
        $name = $row['name'];
        $on_off = $row['on_off'];
        $file1 = $row['file1'] ?? '';
        if ($zugriff and (($do ?? null) != 'vorschau')) {
            $edit_admin = "<a href='service.php?id={$id_tmp}&amp;{$button_name}=up' style='margin-right:4px;'><img src='/assets/icns/up_16.svg' class='noborder'></a><a href='service.php?id={$id_tmp}&amp;{$button_name}=down' style='margin-right:4px;'><img src='/assets/icns/down_16.svg' class='noborder'></a><a href='service.php?id={$id_tmp}&{$button_name}=start' class='linkedit'>&nbsp;</a>";
        } else {
            $edit_admin = "";
        }

        if ($on_off == 0) {
            $class = " class='error'";
        } else {
            $class = "";
        }

        include __DIR__.'/library/phpWebFileManager/icons.inc.php';
        $var = explode(".", $file1);
        $ext = strtolower(end($var));
        $icon = $fm_cfg['icons']['ext'][$ext] ?? '';
        if ($ext != "" and $ext !== 'pdf') {
            $icon = "<img src='/assets/icns/".$icon."' class='noborder' style='margin-right:6px;vertical-align:middle;'>";
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
            echo "<li>".$edit_admin./* $icon."<a href='$def_folder/$file1' target='_blank'>$name</a>". */ $file_utils->olzFile($db_table, $id_tmp, 1, $name)."</li>";
        } else {
            echo "<table class='liste'><tr><td style='font-weight:bold;width:20%;'>Bezeichnung:</td><td>{$name}</td></tr>";
            echo "<tr><td style='font-weight:bold;'>Dateiname:</td><td>{$icon}<a href='{$tmp_folder}/{$file1}' target='_blank'>{$file1}</a></td></tr></table>";
        }
    }
    echo "</ul>";
}
echo "</div></div><br><br>";
echo "</form>";

// Zielsprint 2020
// include __DIR__.'/zielsprint20.php';
