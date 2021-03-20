<?php

// =============================================================================
// Wiederverwendbare Komponenten der Website.
// =============================================================================

require_once __DIR__.'/../config/paths.php';
require_once __DIR__.'/../config/date.php';

//----------------------------------
//EMAILADRESSE MASKIEREN
//----------------------------------
function olz_mask_email($string, $name, $subject) {
    if ($name == "") {
        $name = "Email senden";
    }
    $res = preg_match_all("/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i", $string, $matches);
    foreach (array_unique($matches[0]) as $email) {
        $p1 = substr($email, 0, strpos($email, "@"));
        $p2 = substr($email, strpos($email, "@") + 1, strlen($email));
        $string = str_replace($email, "<script type='text/javascript'>MailTo('".$p1."', '".$p2."', '{$name}', '{$subject}');</script>", $string);
    }
    return $string;
}

//----------------------------------
//URL ERKENNEN
//----------------------------------
function olz_find_url($string) {
    if ($name == "") {
        $name = "Email senden";
    }
    $string = str_replace("%20", "x4x8x", $string);
    $res = preg_match_all("@(https?://([-\\w\\.]+)+(:\\d+)?(/([\\w/_\\.]*(\\?\\S+)?)?)?)@", $string, $matches);
    //$res = preg_match_all('/[a-z]+:\/\/\S+/', $string, $matches);

    foreach (array_unique($matches[0]) as $url) {
        $linkname = parse_url($url, PHP_URL_HOST);
        $string = str_replace($url, "<a href='".$url."' target='_blank' class='linkext'><b>".$linkname."</b></a>", $string);
    }
    return str_replace("x4x8x", "%20", $string);
}

//----------------------------------
//FUNKTION IST GANZZAHL
//----------------------------------
function is_ganzzahl($string) {
    $tmp = $string;
    settype($string, 'integer');
    return $string."x" == $tmp."x";
}

//----------------------------------
//FUNKTION MONATS-ZWISCHENTITEL
//----------------------------------
function olz_monate($datum) {
    global $monat, $_DATE_UTILS;
    $monatstitel = '';
    $entry_month = $_DATE_UTILS->olzDate("M", $datum);
    if ($monat != $entry_month) {
        $monatstitel = "<tr><td colspan='3' style='border:0px; padding:10px 0px 0px 0px;'><a name=monat".$entry_month."></a><h3 class='tablebar'>".$_DATE_UTILS->olzDate("MM jjjj", $datum)."</h3></td></tr>\n";
    }
    $monat = $entry_month;
    return $monatstitel;
}

//----------------------------------
//FUNKTION Button-Menu
//----------------------------------
function olz_buttons($name, $buttons, $off) {
    global $code_href;
    // Icons: 0=neu, 1=edit, 2=Abbrechen, 3=Vorschau
    $icons = ["new_16.svg", "edit_16.svg", "cancel_16.svg", "preview_16.svg", "save_16.svg", "delete_16.svg"];
    $html_menu = [];
    foreach ($buttons as $tmp_button) {
        if (is_array($tmp_button)) {
            $button = $tmp_button[0];
            $icon_nr = $tmp_button[1];
            $icon = "<img src=\"".$code_href."icns/".$icons[$icon_nr]."\" class=\"noborder\" style='vertical-align:middle;padding-left:2px;' alt=''>";
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

//----------------------------------
// FUNKTION Ampersand austauschen
//----------------------------------
function olz_amp($text) {
    return str_replace(["&amp;", "&"], ["&", "&amp;"], $text);
}

//----------------------------------
// Variablen Text editieren
//----------------------------------
function get_olz_text($id_text, $editable = true) {
    global $db_edit,$db,$buttonolz_text;
    require_once __DIR__.'/../config/database.php';

    $id_edit = $_GET['id_edit'];
    $html_out = "";

    //Konstanten
    $db_table = "olz_text";

    // ZUGRIFF
    if (($_SESSION['auth'] == "all") or (in_array($db_table."_".$id_text, preg_split("/ /", $_SESSION['auth'])))) {
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
            include 'admin/admin_db.php';
            $html_out .= ob_get_contents();
            ob_end_clean();
        }
        if ($_SESSION['edit']['table'] == $db_table) {
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
        $html_out .= "<div class='olz_text_insert' id='id_edit".$id_text."'>";
    } else {
        if ($is_empty) {
            return '';
        }
        $html_out .= "<div>";
    }

    // Anzeige - Vorschau
    if (($db_edit == "0") || ($do == "vorschau") || $_SESSION[$db_table.'id_text_'] != $id_text || !$editable) {
        if ($do == "vorschau") {
            $row = $vorschau;
        }
        $id_tmp = $row['id'];
        $text = $row['text'];
        if ($zugriff && ($do != 'vorschau') && $editable) {
            $edit_admin = "<p style='border-bottom:solid 1px;'><a href='?id_edit={$id_text}&amp;button{$db_table}=start#id_edit{$id_text}' class='linkedit' id='olz-text-edit-{$id_text}'>Text bearbeiten (ID:{$id_text})</a></p>";
        } else {
            $edit_admin = "";
        }
        $html_out .= $edit_admin.$text;
    }
    $html_out .= "</div>";
    return $html_out;
}

//----------------------------------
//NEWS-FEED ANZEIGEN
//----------------------------------
function get_eintrag($icon, $datum, $titel, $text, $link, $pic = "") {
    global $_DATE_UTILS;
    echo "<div style='position:relative; clear:left; overflow:hidden; border-radius:3px; padding:5px;' onmouseover='this.style.backgroundColor=\"#D4E7CE\";' onmouseout='this.style.backgroundColor=\"\";'>
    <span style='position:relative; float:right; padding-left:2px; text-align:right; color:#000;'><span style='float:left; margin-right:10px;'>".$pic."</span><span style='cursor:pointer;' class='titel' onclick='javascript:location.href=\"".$link."\";return false;'>".$_DATE_UTILS->olzDate("tt.mm.jj", $datum)."</span></span>
    <div style='cursor:pointer;' class='titel' onclick='javascript:location.href=\"".$link."\";return false;'><img src='".$icon."' style='width:20px; height:20px;' class='noborder' alt='' /> ".$titel."</div>
    <div style='clear:left; margin-top:0px;' class='paragraf'>".$text."</div></div>";
}
function make_expandable($text) {
    global $textlaenge_def;
    $text_orig = $text;
    $resized = ($textlaenge_def <= mb_strlen($text));
    $text = mb_substr($text, 0, $textlaenge_def);
    $text = preg_replace("/\\s*\\n\\s*/", "\n", $text);
    $num_br = preg_match_all("/\\n/", $text, $tmp);
    if ($num_br < 3) {
        $text = olz_br($text);
    } else {
        $text = str_replace("\n", " &nbsp; ", $text);
    }
    if ($resized) {
        $pos = mb_strrpos($text, " ");
        $postmp = mb_strrpos($text, "<br>");
        if (($postmp > $pos || $pos === false) && $postmp !== false) {
            $pos = $postmp;
        }
        $ident = "expandable".md5($text_orig.rand().time());
        $num_br = preg_match_all("/\\n/", $text_orig, $tmp);
        if ($num_br < 3) {
            $text_orig = olz_br($text_orig);
        } else {
            $text_orig = str_replace("\n", " &nbsp; ", $text_orig);
        }
        $text = "<span id='".$ident."'>".mb_substr($text, 0, $pos)." <a href='javascript:' onclick='document.getElementById(&quot;".$ident."&quot;).innerHTML = ".str_replace(["\"", "'"], ["&quot;", "&#39;"], json_encode($text_orig)).";'>[...]</a></span>";
    } else {
        $text = $text;
    }
    return $text;
}

//----------------------------------
// BR Korrekt setzen
//----------------------------------
function olz_br($text) {
    return str_replace(["\n"], ["<br>"], $text);
}

//----------------------------------
// KORREKTE EMAILADRESSE ÜBERPRÜFEN
//----------------------------------
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

//----------------------------------
//FUNKTION Uid GENERIEREN
//----------------------------------
function olz_create_uid($db_table) {
    global $db;

    require_once __DIR__.'/../config/database.php';

    $uid = "";
    do {
        for ($f = 1; $f <= 10; $f++) {
            $uid .= substr("abcdefghijklmnopqrstuvwxyz0123456789", rand(0, 35), 1);
        }
        $result = $db->query("SELECT * FROM {$db_table} WHERE (uid='{$uid}')");
    } while ($result->num_rows !== 0);
    return $uid;
}
