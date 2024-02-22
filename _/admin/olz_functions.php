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
