<?php

// =============================================================================
// Wiederverwendbare Komponenten der Website.
// =============================================================================

use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

// ----------------------------------
// FUNKTION IST GANZZAHL
// ----------------------------------
function is_ganzzahl($string) {
    $tmp = $string;
    settype($string, 'integer');
    return $string."x" == $tmp."x";
}

// ----------------------------------
// FUNKTION Button-Menu
// ----------------------------------
function olz_buttons($name, $buttons, $off) {
    $code_href = EnvUtils::fromEnv()->getCodeHref();
    // Icons: 0=neu, 1=edit, 2=Abbrechen, 3=Vorschau
    $icons = ["new_16.svg", "edit_16.svg", "cancel_16.svg", "preview_16.svg", "save_16.svg", "delete_16.svg"];
    $html_menu = [];
    foreach ($buttons as $tmp_button) {
        if (is_array($tmp_button)) {
            $button = $tmp_button[0];
            $icon_nr = $tmp_button[1];
            $icon = "<img src=\"{$code_href}assets/icns/".$icons[$icon_nr]."\" class=\"noborder\" style='vertical-align:middle;padding-left:2px;' alt=''>";
        } else {
            $button = $tmp_button;
            $icon = "";
        }
        $id = $name.'-'.str_replace(' ', '-', strtolower($button));
        if ($tmp_button == $off) {
            array_push($html_menu, $icon."<input type='submit' value='".$button."' name='".$name."' id='".$id."' class='button' style='color:black;'>");
        } else {
            array_push($html_menu, $icon."<input type='submit' value='".$button."' name='".$name."' id='".$id."' class='button'>");
        }
    }
    return "|".implode("|", $html_menu)."|";
}

// ----------------------------------
// Variablen Text editieren
// ----------------------------------
function get_olz_text($id_text, $editable = true) {
    global $db_edit,$buttonolz_text;
    $db = DbUtils::fromEnv()->getDb();

    $id_edit = $_GET['id_edit'];
    $html_out = "";

    // Konstanten
    $db_table = "olz_text";

    // ZUGRIFF
    if ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table."_".$id_text, preg_split('/ /', $_SESSION['auth'] ?? ''))) {
        $zugriff = "1";
    } else {
        $zugriff = "0";
    }
    $button_name = 'button'.$db_table;
    $button_value = $_POST[$button_name] ?? $_GET[$button_name] ?? null;
    if ($button_value != null and $_SESSION[$db_table.'id_text_'] == $id_text) {
        $_SESSION['edit']['db_table'] = $db_table;
    }
    if (isset($id_edit) and is_ganzzahl($id_edit)) {
        $_SESSION[$db_table."id_text_"] = $id_edit;
    } else {
        $id_edit = $_SESSION[$db_table.'id_text_'];
    }
    $_SESSION['id_edit'] = $_SESSION[$db_table.'id_text_'];

    if ($_SESSION[$db_table.'id_text_'] == $id_text) {// DATENSATZ EDITIEREN
        $id = $id_edit;
        if ($zugriff) {
            $functions = ['neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'start' => 'start',
                'undo' => 'undo', ];
        } else {
            $functions = [];
        }

        $function = array_search($button_value, $functions);
        if ($zugriff && ($function != "") && $editable) {
            ob_start();
            include __DIR__.'/admin_db.php';
            $html_out .= ob_get_contents();
            ob_end_clean();
        }
        if (($_SESSION['edit']['table'] ?? null) == $db_table) {
            $db_edit = "1";
        } else {
            $db_edit = "0";
        }
    }

    // Tabelle auslesen
    $sql = "select * from {$db_table} WHERE (id = '{$id_text}')";
    if (!$zugriff) {
        $sql = "select * from {$db_table} WHERE (id = '{$id_text}') AND (on_off = '1' )";
    }
    $result = $db->query($sql);
    $row = $result->fetch_assoc();

    $is_empty = !$row || !$row['text'] || strlen($row['text']) == 0;

    if ($zugriff && $editable) {
        $html_out .= "<div class='olz-text-insert' id='id_edit".$id_text."'>";
    } else {
        if ($is_empty) {
            return '';
        }
        $html_out .= "<div>";
    }

    // Anzeige - Vorschau
    if (($db_edit == "0") || (($do ?? null) == 'vorschau') || $_SESSION[$db_table.'id_text_'] != $id_text || !$editable) {
        if (($do ?? null) == 'vorschau') {
            $row = $vorschau;
        }
        $id_tmp = $row['id'];
        $text = $row['text'];
        if ($zugriff && (($do ?? null) != 'vorschau') && $editable) {
            $edit_admin = "<p style='border-bottom:solid 1px;'><a href='?id_edit={$id_text}&amp;button{$db_table}=start#id_edit{$id_text}' class='linkedit' id='olz-text-edit-{$id_text}'>Text bearbeiten (ID:{$id_text})</a></p>";
        } else {
            $edit_admin = "";
        }
        $html_out .= $edit_admin.$text;
    }
    $html_out .= "</div>";
    return $html_out;
}

// ----------------------------------
// KORREKTE EMAILADRESSE ÜBERPRÜFEN
// ----------------------------------
function olz_is_email($v) {
    $v = trim($v);
    $nonascii = "\x80-\xff"; // Non-ASCII-Chars are not allowed

    $nqtext = "[^\\\\{$nonascii}\015\012\"]";
    $qchar = "\\\\[^{$nonascii}]";

    $protocol = '(?:mailto:)';
    $normuser = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
    $quotedstring = "\"(?:{$nqtext}|{$qchar})+\"";
    $user_part = "(?:{$normuser}|{$quotedstring})";

    $dom_mainpart = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
    $dom_subpart = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
    $dom_tldpart = '[a-zA-Z]{2,5}';
    $domain_part = "{$dom_subpart}{$dom_mainpart}{$dom_tldpart}";

    $regex = "{$protocol}?{$user_part}\\@{$domain_part}";
    if (preg_match("/^{$regex}$/", $v)) {
        return "1";
    }

    return "0";
}

// ----------------------------------
// FUNKTION Uid GENERIEREN
// ----------------------------------
function olz_create_uid($db_table) {
    $db = DbUtils::fromEnv()->getDb();

    $uid = "";
    do {
        for ($f = 1; $f <= 10; $f++) {
            $uid .= substr("abcdefghijklmnopqrstuvwxyz0123456789", rand(0, 35), 1);
        }
        $result = $db->query("SELECT * FROM {$db_table} WHERE (uid='{$uid}')");
    } while ($result->num_rows !== 0);
    return $uid;
}
