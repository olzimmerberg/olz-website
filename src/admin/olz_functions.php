<?php

//----------------------------------
//EMAILADRESSE MASKIEREN
//----------------------------------
function olz_mask_email($string,$name,$subject)
{if ($name=="") $name = "Email senden";
$res = preg_match_all("/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i",$string,$matches);
foreach(array_unique($matches[0]) as $email)
    {$p1 = substr($email,0,strpos($email,"@"));
    $p2 = substr($email,strpos($email,"@")+1,strlen($email));
    $string = str_replace($email,"<script type='text/javascript'>document.write(MailTo('".$p1."', '".$p2."', '$name', '$subject'));</script>",$string);
    }
return $string;
}

//----------------------------------
//URL ERKENNEN
//----------------------------------
function olz_find_url($string)
{if ($name=="") $name = "Email senden";
$string = str_replace ("%20","x4x8x",$string ) ;
$res = preg_match_all("@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@",$string,$matches);
//$res = preg_match_all('/[a-z]+:\/\/\S+/', $string, $matches);

foreach(array_unique($matches[0]) as $url)
    {$linkname = parse_url($url,PHP_URL_HOST);
    $string = str_replace($url,"<a href='".$url."' target='_blank' class='linkext'><b>".$linkname."</b></a>",$string);
    }
$string = str_replace ("x4x8x","%20",$string);
return $string;
}

//----------------------------------
//FUNKTION IST GANZZAHL
//----------------------------------
function is_ganzzahl($string)
{$tmp = $string;
settype($string,'integer');
return ($string."x" == $tmp."x");
}

//----------------------------------
//FUNKTION MONATS-ZWISCHENTITEL
//----------------------------------
function olz_monate($datum)
{global $monat;
if ($monat != olz_date("M",$datum))
    {$monatstitel = "<tr><td colspan='3' style='border:0px; padding:10px 0px 0px 0px;'><a name=monat".olz_date("M",$datum)."></a><h3 class='tablebar'>".olz_date("MM jjjj",$datum)."</h3></td></tr>\n";
    }
$monat = olz_date("M",$datum);
return $monatstitel;
}

//----------------------------------
//FUNKTION Button-Menu
//----------------------------------
function olz_buttons($name,$buttons,$off) {
    global $code_href;
    // Icons: 0=neu, 1=edit, 2=Abbrechen, 3=Vorschau
    $icons = array("neu.gif","edit.gif","cancel.gif","preview.gif","save.gif","delete.gif");
    $html_menu = array();
    foreach ($buttons as $tmp_button)
        {if (is_array($tmp_button))
            {$button = $tmp_button[0];
            $icon_nr = $tmp_button[1];
            $icon = "<img src=\"".$code_href."icns/".$icons[$icon_nr]."\" class=\"noborder\" style='vertical-align:middle;padding-left:2px;' alt=''>";
            }
        else
            {$button = $tmp_button;
            $icon = "";}
        if ($tmp_button == $off)
            {array_push($html_menu,$icon."<input type='submit' value='".$button."' name='".$name."' class='button' style='color:black;'>");}
        else
            {array_push($html_menu,$icon."<input type='submit' value='".$button."' name='".$name."' class='button'>");}
        }
    return "|".implode("|", $html_menu)."|";
}

//----------------------------------
// FUNKTION Ampersand austauschen
//----------------------------------
function olz_amp ($text) {
    $text = str_replace(array("&amp;","&"),array("&","&amp;"),$text) ;
    return $text;
}

//----------------------------------
// Variablen Text editieren
//----------------------------------
function olz_text_insert($id_text, $editable=true)
{global $id_edit,$db_edit,$db,$buttonolz_text;

//Konstanten
$db_table = "olz_text";

// ZUGRIFF
if (($_SESSION['auth'] == "all") OR (in_array($db_table."_".$id_text ,preg_split("/ /",$_SESSION['auth'])))) $zugriff = "1";
else $zugriff = "0";
$button_name = "button".$db_table;
if(isset($$button_name) AND $_SESSION[$db_table.'id_text_']==$id_text) $_SESSION['edit']['db_table'] = $db_table;
if (isset($id_edit) AND is_ganzzahl($id_edit)) $_SESSION[$db_table."id_text_"] = $id_edit;
else $id_edit = $_SESSION[$db_table.'id_text_'];
$_SESSION['id_edit'] = $_SESSION[$db_table.'id_text_'];

if ($zugriff && $editable) echo "<div class='olz_text_insert' id='id_edit".$id_text."'>";
else echo "<div>";

if($_SESSION[$db_table.'id_text_']==$id_text)
    {// DATENSATZ EDITIEREN
    $id = $id_edit;
    if ($zugriff)
        {$functions = array('neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'start' => 'start',
                'undo' => 'undo');}
    else $functions = array();

    $function = array_search($$button_name,$functions);
    if ($zugriff && ($function!="") && $editable)
        {include 'admin/admin_db.php';}
    if ($_SESSION['edit']['table']==$db_table) $db_edit = "1";
    else $db_edit = "0";
    }

// Tabelle auslesen
$sql = "select * from $db_table WHERE (id = '$id_text')" ;
if (!$zugriff) $sql = "select * from $db_table WHERE (id = '$id_text') AND (on_off = '1' )" ;
$result = $db->query($sql);
$row = $result->fetch_assoc();

// Anzeige - Vorschau
if (($db_edit=="0") || ($do=="vorschau") || $_SESSION[$db_table.'id_text_']!=$id_text || !$editable)
    {if ($do=="vorschau") $row = $vorschau;
    $id_tmp = $row['id'];
    $text = $row['text'];
    if ($zugriff && ($do != 'vorschau') && $editable)
        {$edit_admin = "<p style='border-bottom:solid 1px;'><a href='index.php?id_edit=$id_text&amp;button$db_table=start#id_edit$id_text' class='linkedit'>Text bearbeiten (ID:$id_text)</a></p>";}
    else
        {$edit_admin = "";}
    echo $edit_admin.$text;
    }
echo "</div>";
}



//----------------------------------
//VORSTANDSPERSON ANZEIGEN
//----------------------------------
function olz_vorstand_insert($id, $mode=0) {
    global $db, $data_path, $data_href, $code_href;
    $result = $db->query("SELECT * FROM vorstand WHERE id='".intval($id)."'");
    $row = $result->fetch_assoc();
    if ($mode==0) {
        $ident = md5(rand().$id.rand().microtime(true));
        return "<div style='position:absolute; display:none; padding:5px 10px 5px 5px; background-color:#D4E7CE; border:1px solid #007521;' id='popup".$ident."'>".olz_vorstand_insert($id, 2)."</div><a href='javascript:olz_toggle_vorstand(&quot;".$ident."&quot;)' id='source".$ident."'>".$row["name"]."</a>";
    } else if ($mode==1) {
        $ident = md5(rand().$id.rand().microtime(true));
        return "<div style='position:relative;'><div style='position:absolute; display:none; padding:5px 10px 5px 5px; background-color:#D4E7CE; border:1px solid #007521; white-space:nowrap;' id='popup".$ident."'>".olz_vorstand_insert($id, 2)."</div></div><a href='javascript:olz_toggle_vorstand(&quot;".$ident."&quot;)' id='source".$ident."' style='display:block; text-align:center;'><img src='".($row["bild"]?$data_href."olz_mitglieder/".$row["bild"]:$code_href."icns/user.jpg")."' alt=''><br><div style='text-align:center;'>".$row["name"]."</div></a>";
    } else if ($mode==2) {
        return "<table><tr><td style='width:1px;'>".(is_file($data_path."olz_mitglieder/".$row["bild"])?"<img src='".$data_href."olz_mitglieder/".$row["bild"]."' alt='' style='height:64px;'>":"&nbsp;")."</td><td style='padding-left:10px;'><b>".$row["name"]."</b>".($row["adresse"]?"<br>".$row["adresse"]:"").($row["tel"]?"<br>Tel. ".$row["tel"]:"").($row["email"]?"<br>".olz_mask_email($row["email"], "Email", ""):"")."</td></tr></table>";
    } else {
        return "person_anzeigen: mode ".$mode." nicht definiert";
    }
}
function olz_funktion_insert($id, $mode=0, $sep=" ") {
    global $db;
    $result = $db->query("SELECT vf.vorstand FROM vorstand_funktion vf JOIN vorstand v ON (vf.vorstand = v.id) WHERE vf.funktion='".intval($id)."' ORDER BY v.name ASC");
    $out = "";
    $bisher = array();
    while ($row = $result->fetch_assoc()) {
        if ($out!="") $out .= $sep;
        $bisher[] = intval($row["vorstand"]);
        $out .= olz_vorstand_insert($row["vorstand"], $mode);
    }
    if (($_SESSION['auth']=="all") || (in_array("vorstand", preg_split("/ /", $_SESSION['auth'])))) {
        $out .= "<div>ID: ".$id."</div>";
    }
    return $out;
}


//----------------------------------
//NEWS-FEED ANZEIGEN
//----------------------------------
function get_eintrag($icon, $datum, $titel, $text, $link, $pic="") {
    echo "<div style='position:relative; clear:left; overflow:hidden; border-radius:3px; padding:5px;' onmouseover='this.style.backgroundColor=\"#D4E7CE\";' onmouseout='this.style.backgroundColor=\"\";'>
    <span style='position:relative; float:right; padding-left:2px; text-align:right; color:#000;'><span style='float:left; margin-right:10px;'>".$pic."</span><span style='cursor:pointer;' class='titel' onclick='javascript:location.href=\"".$link."\";return false;'>".olz_date("tt.mm.jj",$datum)."</span></span>
    <div style='cursor:pointer;' class='titel' onclick='javascript:location.href=\"".$link."\";return false;'><img src='".$icon."' style='width:20px; height:20px;' class='noborder' alt='' /> ".$titel."</div>
    <div style='clear:left; margin-top:0px;' class='paragraf'>".$text."</div></div>";
}
function make_expandable($text) {
    global $textlaenge_def;
    $text_orig = $text;
    $resized = ($textlaenge_def<=mb_strlen($text));
    $text = mb_substr($text, 0, $textlaenge_def);
    $text = preg_replace("/\s*\\n\s*/","\n", $text);
    $num_br = preg_match_all("/\\n/", $text, $tmp);
    if ($num_br<3) $text = olz_br($text);
    else $text = str_replace("\n", " &nbsp; ", $text);
    if ($resized) {
        $pos = mb_strrpos($text," ");
        $postmp = mb_strrpos($text,"<br>");
        if (($postmp>$pos || $pos===false) && $postmp!==false) $pos = $postmp;
        $ident = "expandable".md5($text_orig.rand().time());
        $num_br = preg_match_all("/\\n/", $text_orig, $tmp);
        if ($num_br<3) $text_orig = olz_br($text_orig);
        else $text_orig = str_replace("\n", " &nbsp; ", $text_orig);
        $text = "<span id='".$ident."'>".mb_substr($text, 0, $pos)." <a href='javascript:' onclick='document.getElementById(&quot;".$ident."&quot;).innerHTML = ".str_replace(array("\"", "'"), array("&quot;", "&#39;"), json_encode($text_orig)).";'>[...]</a></span>";
    } else {
        $text = $text;
    }
    return $text;
}



//----------------------------------
// BR Korrekt setzen
//----------------------------------
function olz_br ($text) {
    $text = str_replace(array("\n"),array("<br>"),$text);
    return $text;
}



//----------------------------------
// KORREKTE EMAILADRESSE ÜBERPRÜFEN
//----------------------------------
function olz_is_email($v) {
        $v=trim($v);
        $nonascii      = "\x80-\xff"; # Non-ASCII-Chars are not allowed

        $nqtext        = "[^\\\\$nonascii\015\012\"]";
        $qchar         = "\\\\[^$nonascii]";

        $protocol      = '(?:mailto:)';
        $normuser      = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
        $quotedstring  = "\"(?:$nqtext|$qchar)+\"";
        $user_part     = "(?:$normuser|$quotedstring)";

        $dom_mainpart  = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
        $dom_subpart   = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
        $dom_tldpart   = '[a-zA-Z]{2,5}';
        $domain_part   = "$dom_subpart$dom_mainpart$dom_tldpart";

        $regex         = "$protocol?$user_part\@$domain_part";
        if (preg_match("/^$regex$/",$v)) return "1";
        else {
                return "0";
        }
}


//----------------------------------
//FUNKTION Uid GENERIEREN
//----------------------------------
function olz_create_uid($db_table) {
global $db;
$uid = "";
do
    {
    for($f=1;$f<=10;$f++) $uid.=substr("abcdefghijklmnopqrstuvwxyz0123456789", rand(0,35), 1);
    $result = $db->query("SELECT * FROM $db_table WHERE (uid='$uid')");
    }
while($result->num_rows!==0);
return $uid;
}

//----------------------------------
//DATUM UMWANDELN
//----------------------------------
function olz_date($format,$datum)
{global $monate, $wochentage_lang,$wochentage;
if ($datum=="") $datum = date("Y-m-d");
if (checkdate(substr($datum,5,2),substr($datum,8,2),substr($datum,0,4)))
    {//Mysql-Datum
    $datum = strtotime(substr($datum,0,4)."-".substr($datum,5,2)."-".substr($datum,8,2));}
else
    {//Unix-Datum
    }
return str_replace(array("tt","t","mm","m","MM","M","xxxxx","jjjj","jj","w","WW","W"),array(date("d",$datum),date("j",$datum),date("m",$datum),date("n",$datum),"xxxxx",$monate[date("n",$datum)-1],strftime("%B",$datum),date("Y",$datum),date("y",$datum),date("w",$datum),$wochentage_lang[date("w",$datum)],$wochentage[date("w",$datum)]),$format);

}

?>
