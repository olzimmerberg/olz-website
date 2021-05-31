<?php

// =============================================================================
// Aktuelle Berichte von offiziellen Vereinsorganen.
// =============================================================================

require_once __DIR__.'/config/database.php';
require_once __DIR__.'/config/date.php';

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($_GET["id"]) and is_ganzzahl($_GET["id"])) {
    $_SESSION[$db_table."id_"] = $id;
    $sql = "SELECT datum FROM {$db_table} WHERE (id='".intval($_GET["id"])."')";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row['datum']));
} else {
    $id = $_SESSION[$db_table.'id_'] ?? null;
}
if (isset($jahr) and in_array($jahr, array_merge($_DATE->getYearsForAccordion(), ["box", "special"]))) {
    $_SESSION[$db_table."jahr_"] = $jahr;
} elseif (isset($_SESSION[$db_table.'jahr_']) and $_SESSION[$db_table.'jahr_'] > 1970) {
    $jahr = $_SESSION[$db_table.'jahr_'];
} else {
    $_SESSION[$db_table.'jahr_'] = $_DATE->olzDate("jjjj", $heute);
}

if ($id == "") { // Jüngste Nachricht
    $sql = "SELECT id,datum FROM {$db_table} WHERE (on_off = '1') AND (typ LIKE '%aktuell%') ORDER BY datum DESC LIMIT 1";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    $_SESSION[$db_table.'id_'] = $row['id'];
    $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row['datum']));
}

$id = $_SESSION[$db_table.'id_'] ?? null;
$jahr = $_SESSION[$db_table.'jahr_'];

//-------------------------------------------------------------
// Liste Aktuell
//-------------------------------------------------------------
function olz_aktuell_liste($sql) {
    global $zugriff, $id, $_GET, $db, $_DATE;
    $html_out = "";
    $result = $db->query($sql);
    $html_out .= "<ul>";
    while ($row = mysqli_fetch_array($result)) {
        $titel = strip_tags($row['titel']);
        $autor = $row['autor'];
        $datum = $row['datum'];
        $link = $row['link'];
        $id_tmp = $row['id'];
        $typ = $row['typ'];
        $on_off = $row['on_off'];

        if ($autor == "") {
            $autor = "..";
        }
        $datum = $_DATE->olzDate("tt.mm.jjjj", $datum);

        if ($link == "") {
            $link = "id={$id_tmp}";
        }
        if ($typ == "aktuell") {
            $link = "aktuell.php?{$link}";
        } elseif ($typ == "termin") {
            $link = "termine.php#{$link}";
        } elseif ($typ == "galerie") {
            $link = "galerie.php?datum={$link}";
        } elseif ($typ == "forum") {
            $link = "forum.php#{$link}";
        } else {
            $link = "aktuell.php?{$link}";
        }
        $link .= (isset($_GET["archiv"]) ? "&amp;archiv" : "");

        if ($zugriff) {
            $edit_admin = "<a href='aktuell.php?id={$id_tmp}&amp;buttonaktuell=start' class='linkedit'>&nbsp;</a>";
        } else {
            $edit_admin = "";
        }

        if ($on_off == 0) {
            $style = " style='color:red;'";
        } else {
            $style = "";
        }

        if ($id == $id_tmp) {
            $html_out .= "<li>{$edit_admin}<span class='linkblack' style='font-weight:bold;'>".$titel." (".$datum."/".$autor.")</span></li>";
        } else {
            $html_out .= "<li>{$edit_admin}<a href='".$link."'{$style} class='linkint'>".$titel." (".$datum."/".$autor.")</a></li>";
        }
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
    echo "<a href='?jahr=box' onclick='runAccordion(\"box\"); return false;' name='accordionlink'><div class='accordion-title' onselectstart='return false;'>Box</div></a>
<div id='AccordionboxContent' class='accordion-content'".($_SESSION[$db_table.'jahr_'] == "box" ? " style='height:auto;'" : " style='height:1px;'")."><div id='AccordionboxContent_' class='accordion-content-'>".$box_html."</div></div>";
}

//-------------------------------------------------------------
// JAHRE
foreach ($_DATE->getYearsForAccordion() as $tmp_jahr) {
    if ($zugriff) {
        $sql = "SELECT * FROM aktuell WHERE (datum >= '{$tmp_jahr}-01-01') AND NOT(typ LIKE '%box%') AND (datum <= '{$tmp_jahr}-12-31') ORDER BY datum DESC, id DESC";
    } else {
        $sql = "SELECT * FROM aktuell WHERE (on_off='1') AND (typ = 'aktuell') AND (datum >= '{$tmp_jahr}-01-01') AND (datum<= '{$tmp_jahr}-12-31') ORDER BY datum DESC, id DESC";
    }
    //"<h2><img src='icns/down_16.svg' class='noborder' style='margin-right:10px;' alt=''>".$tmp_jahr."</h2>";
    echo "<a href='?jahr=".$tmp_jahr."' onclick='runAccordion(\"".$tmp_jahr."\"); return false;' name='accordionlink'><div class='accordion-title' onselectstart='return false;'>".$tmp_jahr."</div></a>
<div id='Accordion".$tmp_jahr."Content' class='accordion-content'".($_SESSION[$db_table.'jahr_'] == $tmp_jahr ? " style='height:auto;'" : " style='height:1px;'")."><div id='Accordion".$tmp_jahr."Content_' class='accordion-content-'>".olz_aktuell_liste($sql)."</div></div>";
}
if (!isset($_GET["archiv"])) {
    echo "<a href='?archiv'><div class='accordion-title' onselectstart='return false;'>ältere...</div></a>";
}
echo "<script type='text/javascript'>setOpenAccordion(\"Accordion".$_SESSION[$db_table.'jahr_']."Content\");</script>";
