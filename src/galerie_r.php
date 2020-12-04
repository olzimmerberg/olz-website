<?php

// =============================================================================
// Fotogalerie mit Bildern von Anlässen.
// =============================================================================

// KONSTANTEN
$tmp_jahr = olz_date("jjjj", "");
$db_imgpath = $tables_img_dirs[$db_table];

//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth'] == "all") or (in_array($db_table, preg_split("/ /", $_SESSION['auth'])))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if ($zugriff and isset($_SESSION['edit'])) {
    $sql = "SELECT datum FROM {$db_table} WHERE (id='{$id}')";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    $datum = $row['datum'];
}
if (isset($_GET["id"]) and is_ganzzahl($_GET["id"])) {
    $_SESSION[$db_table."id_"] = $id;
    $sql = "SELECT datum FROM {$db_table} WHERE (id='".intval($_GET["id"])."')";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row['datum']));
} else {
    $id = $_SESSION[$db_table.'id_'];
}
if (isset($_GET["jahr"])) {
    $_SESSION[$db_table."jahr_"] = $_GET["jahr"];
} else {
    $jahr = $_SESSION[$db_table.'jahr_'];
}
//if ($jahr = "") $_SESSION[$db_table.'jahr_'] = olz_date("jjjj","");
if ($id == "") { // Jüngste Nachricht
    $sql = "SELECT id,datum FROM {$db_table} WHERE (on_off = '1') ORDER BY datum DESC LIMIT 1";
    $result = $db->query($sql);
    $row = mysqli_fetch_array($result);
    $_SESSION[$db_table.'id_'] = $row['id'];
    $_SESSION[$db_table.'jahr_'] = date("Y", strtotime($row['datum']));
}
$id = $_SESSION[$db_table.'id_'];
$jahr = $_SESSION[$db_table.'jahr_'];

echo "<h2>Galerien</h2>";

//if ($db_edit=="0") $db->query("DELETE FROM $db_table WHERE titel='' AND autor='' AND on_off='0' AND typ='foto' AND datum<'".date("Y-m-d")."'");

while ($tmp_jahr >= $end_jahr) {
    echo "<a href='?jahr=".$tmp_jahr."' onclick='runAccordion(\"".$tmp_jahr."\"); return false;'><div class='AccordionTitle' onselectstart='return false;' name='accordionlink'>".$tmp_jahr."</div></a>
<div id='Accordion".$tmp_jahr."Content' class='AccordionContent'".($_SESSION[$db_table.'jahr_'] == $tmp_jahr ? " style='height:auto;'" : "")."><div id='Accordion".$tmp_jahr."Content_' class='AccordionContent_'>";
    if ($zugriff) {
        $sql = "SELECT * from {$db_table} WHERE (YEAR(datum) = '{$tmp_jahr}') ORDER BY datum DESC";
    } else {
        $sql = "SELECT * from {$db_table} WHERE (on_off = '1') AND (YEAR(datum) = '{$tmp_jahr}') ORDER BY datum DESC";
    }

    $javascript = "";
    $result = $db->query($sql);
    echo "<ul>";
    while ($row = mysqli_fetch_array($result)) {
        $datum = $row['datum'];
        $titel = $row['titel'];
        $autor = $row['autor'];
        $typ = $row['typ'];
        $id_tmp = $row['id'];
        $content = $row['content'];
        $on_off = $row['on_off'];

        $groesse = galerie_groesse($data_path.$db_imgpath."/".$id_tmp."/img/");

        if ($typ == "movie") {
            $res0 = preg_match("/^https\\:\\/\\/(www\\.)?youtu\\.be\\/([a-zA-Z0-9]{6,})/", $content);
            $res1 = preg_match("/^https\\:\\/\\/(www\\.)?youtube\\.com\\/watch\\?v\\=([a-zA-Z0-9]{6,})/", $content);
            $groesse = $res0 || $res1 ? "YouTube" : $content;
            $linkclass = "linkmovie";
        } else {
            $linkclass = "linkimg";
        }

        if ($zugriff and ($do != 'vorschau')) {
            $edit_admin = "<li style='opacity:".($on_off ? "1" : "0.5").";'><a href='galerie.php?id=".$id_tmp."&amp;button{$db_table}=start' class='linkedit'>&nbsp;</a>";
        } else {
            $edit_admin = "<li>";
        }

        if ($id == $id_tmp) {
            echo "{$edit_admin}<span class='linkblack test-flaky' style='font-weight:bold;'>".date("j", strtotime($datum)).". ".ucfirst($monate[date("n", strtotime($datum)) - 1]).": ".$titel." (".$groesse.")</span></li>";
        } else {
            echo "<li>{$edit_admin}<a href='galerie.php?id=".$id_tmp."".(isset($_GET["archiv"]) ? "&amp;archiv" : "")."' class='{$linkclass} test-flaky' id='galerie_r_a_".$id_tmp."'>".date("j", strtotime($datum)).". ".ucfirst($monate[date("n", strtotime($datum)) - 1]).": ".$titel." (".$groesse.")</a></li>";
        }
        if ($do == "edit") {
            $ident = "olzimgedit".md5($db_table."-".$id);
            $javascript .= "var elem = document.getElementById(\"galerie_r_a_".$id_tmp."\");
if (elem) elem.onmousedown = (function (elem) { return function (e) {
    e = e || event;
    e.preventDefault();
    olz_images_edit[\"".$ident."\"][\"dragindex\"] = -1;
    olz_images_edit[\"".$ident."\"][\"draggalery\"] = ".$id_tmp.";
    var delem = document.createElement(\"div\");
    delem.style.pointerEvents = \"none\";
    delem.style.position = \"absolute\";
    delem.style.zIndex = 1003;
    delem.style.left = (e.clientX-32)+\"px\";
    delem.style.top = (e.clientY+window.pageYOffset+5)+\"px\";
    delem.style.width = \"64px\";
    delem.style.height = \"64px\";
    delem.innerHTML = elem.innerHTML;
    document.getElementsByTagName(\"body\")[0].appendChild(delem);
    olz_images_edit[\"".$ident."\"][\"dragelem\"] = delem;
}; })(elem);
";
        }
    }
    echo "</ul>";
    echo "</div></div>";
    echo "<script type='text/javascript'>".$javascript."</script>";
    $tmp_jahr = $tmp_jahr - 1;
}
if (!isset($_GET["archiv"])) {
    echo "<a href='?archiv'><div class='AccordionTitle' onselectstart='return false;'>ältere...</div></a>";
}
echo "<script type='text/javascript'>openAccordion = \"Accordion".$_SESSION[$db_table.'jahr_']."Content\";</script>";

function galerie_groesse($path) { // Effizienter Algorithmus, um Grösse einer Galerie zu finden
    $begin = 16;
    $min = 0;
    $max = -1;
    $cur = $begin;
    for ($i = 0; $i < 100 && $max == -1; $i++) {
        $imgfile = $path."/".str_pad($cur, 3, "0", STR_PAD_LEFT).".jpg";
        if (is_file($imgfile)) {
            $min = $cur;
            $cur = $cur * 2;
        } else {
            $max = $cur;
            break;
        }
    }
    for ($i = 0; $i < 100 && $min != $max; $i++) {
        $cur = ceil(($min + $max) / 2);
        $imgfile = $path."/".str_pad($cur, 3, "0", STR_PAD_LEFT).".jpg";
        if (is_file($imgfile)) {
            $min = $cur;
        } else {
            $max = $cur - 1;
        }
    }
    return $min;
}
