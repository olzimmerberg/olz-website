<?php

require_once("file_tools.php");
require_once("image_tools.php");
$db_table = "aktuell";

//-------------------------------------------------------------
// ZUGRIFF
/*if (($_SESSION['auth']=="all") OR (in_array($db_table ,preg_split("/ /",$_SESSION['auth'])))) $zugriff = "1";
else $zugriff = "0";*/
$zugriff = (($_SESSION['auth']=="all") OR (in_array($db_table ,preg_split("/ /",$_SESSION['auth'])))) ? "1" : "0";
$button_name = "button".$db_table;
if(isset($$button_name)) $_SESSION['edit']['db_table'] = $db_table;

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($_GET["id"]) AND (is_ganzzahl($_GET["id"]) OR in_array($_GET["id"],$aktuell_special))) {
    $_SESSION[$db_table."id_"] = $id;
    $sql = "SELECT datum FROM $db_table WHERE (id='".intval($_GET["id"])."')";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row['datum']));
} else $id = $_SESSION[$db_table.'id_'];
if (isset($jahr) AND in_array($jahr,array_merge($jahre,array("box","special")))) $_SESSION[$db_table."jahr_"] = $jahr;
elseif(isset($_SESSION[$db_table.'jahr_']) AND $_SESSION[$db_table.'jahr_']>1970) $jahr = $_SESSION[$db_table.'jahr_'];
else $_SESSION[$db_table.'jahr_'] = olz_date("jjjj",$heute);

if ($id=="") // Jüngste Nachricht
    {$sql = "SELECT id,datum FROM $db_table WHERE (on_off = '1') AND (typ LIKE '%aktuell%') ORDER BY datum DESC LIMIT 1";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    $_SESSION[$db_table.'id_'] = $row['id'];
    $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row['datum']));
}

$id = $_SESSION[$db_table.'id_'];
$jahr = $_SESSION[$db_table.'jahr_'];

//-------------------------------------------------------------
// Liste Aktuell
//-------------------------------------------------------------
function olz_aktuell_liste($sql)
{
    global $zugriff,$id,$_GET,$db;
    $html_out = "";
    $result = $db->query($sql);
    $html_out .= "<ul>";
    while ($row = mysqli_fetch_array($result))
        {$titel = strip_tags($row['titel']);
        $autor = $row['autor'];
        $datum =$row['datum'];
        $link = $row['link'];
        $id_tmp = $row['id'];
        $typ = $row['typ'];
        $on_off = $row['on_off'];
            
        if ($autor == "") $autor = "..";
        $datum = olz_date("tt.mm.jjjj",$datum);
    
        if ($link == "") $link = "page=2&amp;id=$id_tmp";
        if ($typ == "aktuell") $link = "?$link";
        elseif ($typ == "termin") $link = "?page=3#$link";
        elseif ($typ == "galerie") $link = "?page=4&amp;datum=$link";
        elseif ($typ == "forum") $link = "?page=5#$link";
        else $link = "?page=2&amp;$link";
        $link .= (isset($_GET["archiv"])?"&amp;archiv":"");
    
        if ($zugriff) $edit_admin = "<a href='index.php?id=$id_tmp&amp;buttonaktuell=start' class='linkedit'>&nbsp;</a>";
        else $edit_admin = "";
        
        if ($on_off==0) $style = " style='color:red;'";
        else $style = "";
    
        if ($id == $id_tmp)
            {$html_out .= "<li>$edit_admin<span class='linkblack' style='font-weight:bold;'>".$titel." (".$datum."/".$autor.")</span></li>";}
        else
            {$html_out .= "<li>$edit_admin<a href='index.php".$link."'$style class='linkint'>".$titel." (".$datum."/".$autor . ")</a></li>";}
        }
    $html_out .= "</ul>";
    return $html_out;
}

echo "<h2>Berichte und Mitteilungen</h2>";


//-------------------------------------------------------------
// BOX

if ($zugriff) {
    $sql = "SELECT * FROM aktuell WHERE (typ LIKE '%box%') ORDER BY on_off DESC, datum DESC";
    $box_html = olz_aktuell_liste($sql);
    echo "<a href='?jahr=box' onclick='runAccordion(\"box\"); return false;' name='accordionlink'><div class='AccordionTitle' onselectstart='return false;'>Box</div></a>
<div id='AccordionboxContent' class='AccordionContent'".($_SESSION[$db_table.'jahr_']=="box"?" style='height:auto;'":" style='height:1px;'")."><div id='AccordionboxContent_' class='AccordionContent_'>".$box_html."</div></div>";
}

//-------------------------------------------------------------
// SPEZIAL
$special_html = "<ul>";
foreach($aktuell_special as $special_lang => $special_kurz) {
    if ($id == $special_kurz) {
        $special_html .= "<li><span><b>".$special_lang."</b></span></li>";
    } else {
        $special_html .= "<li><a href='index.php?id=".$special_kurz. "'>".$special_lang."</a></li>";
    }
}
$special_html .= "</ul>";
echo "<a href='?jahr=special' onclick='runAccordion(\"special\"); return false;' name='accordionlink'><div class='AccordionTitle' onselectstart='return false;'>Spezial</div></a>
<div id='AccordionspecialContent' class='AccordionContent'".($_SESSION[$db_table.'jahr_']=="special"?" style='height:auto;'":" style='height:1px;'")."><div id='AccordionspecialContent_' class='AccordionContent_'>".$special_html."</div></div>";

//-------------------------------------------------------------
// JAHRE
foreach ($jahre as $tmp_jahr) {
    if ($zugriff)
        {$sql = "SELECT * FROM aktuell WHERE (datum >= '$tmp_jahr-01-01') AND NOT(typ LIKE '%box%') AND (datum <= '$tmp_jahr-12-31') ORDER BY datum DESC, id DESC";}
    else
        {$sql = "SELECT * FROM aktuell WHERE (on_off='1') AND (typ = 'aktuell') AND (datum >= '$tmp_jahr-01-01') AND (datum<= '$tmp_jahr-12-31') ORDER BY datum DESC, id DESC";}
    //"<h2><img src='icns/ab.gif' class='noborder' style='margin-right:10px;' alt=''>".$tmp_jahr."</h2>";
    echo "<a href='?jahr=".$tmp_jahr."' onclick='runAccordion(\"".$tmp_jahr."\"); return false;' name='accordionlink'><div class='AccordionTitle' onselectstart='return false;'>".$tmp_jahr."</div></a>
<div id='Accordion".$tmp_jahr."Content' class='AccordionContent'".($_SESSION[$db_table.'jahr_']==$tmp_jahr?" style='height:auto;'":" style='height:1px;'")."><div id='Accordion".$tmp_jahr."Content_' class='AccordionContent_'>".olz_aktuell_liste($sql)."</div></div>";
    }
if (!isset($_GET["archiv"])) echo "<a href='?archiv'><div class='AccordionTitle' onselectstart='return false;'>ältere...</div></a>";
echo "<script type='text/javascript'>openAccordion = \"Accordion".$_SESSION[$db_table.'jahr_']."Content\";</script>";
?>