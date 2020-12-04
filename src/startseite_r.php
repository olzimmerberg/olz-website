<?php

// =============================================================================
// Zeigt die wichtigsten Informationen möglichst übersichtlich an.
// =============================================================================

require_once __DIR__.'/tickers.php';

// echo "<form name='Formular2' method='post' action='index.php' enctype='multipart/form-data'>
// <h2>GESUCHT 'BILD DER WOCHE'</h2>
// <p>Ihr habt sicher gemerkt, dass hier seit unzähligen Wochen dasselbe Bild erschienen ist. Leider hat niemand den Wink mit dem Zaunpfahl bemerkt. Deshalb nun hier explizit der Aufruf: Bitte liefert mir Bilder, am liebsten natürlich von irgendeinem OL, einem Training oder sonst einem Klubanlass. Format, Grösse und Qualität sind nicht so wichtig (ich nehme auch Handybilder). Zückt also eure Kameras und schiesst los ...</p>
// <script type='text/javascript'>document.write(MailTo(\"olz_uu_01\", \"olzimmerberg.ch\", \"Bild mailen\", \"Bild%20der%20Woche\"));</script>";
require_once "image_tools.php";
//Konstanten
$db_table = 'bild_der_woche';
$img_folder = "img";
$id = $_GET['id'];
//-------------------------------------------------------------
// ZUGRIFF
if (($_SESSION['auth'] == "all") or (in_array($db_table, preg_split("/ /", $_SESSION['auth'])))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}
$button_name = 'button'.$db_table;
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($id) and (is_ganzzahl($id) or in_array($id, $aktuell_special))) {
    $_SESSION[$db_table."id_"] = $id;
}
$id = $_SESSION[$db_table.'id_'];
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

//-------------------------------------------------------------
// DATENSATZ EDITIEREN
if ($zugriff) {
    $functions = ['neu' => 'Neuer Eintrag',
        'edit' => 'Bearbeiten',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'delete' => 'Löschen',
        'deletebild1' => 'Bild 1 entfernen',
        'start' => 'start',
        'undo' => 'undo', ];
} else {
    $functions = [];
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
if ($zugriff and ($db_edit == '0')) {
    echo "<div class='buttonbar'>\n".olz_buttons("button".$db_table, [["Neuer Eintrag", "0"]], "")."</div>";
}
//-------------------------------------------------------------
// BILD DER WOCHE - VORSCHAU
if (($db_edit == "0") or ($do == "vorschau")) {
    $sql = "SELECT * from {$db_table} WHERE (on_off = 1) ORDER BY id ASC LIMIT 1";
    $result = $db->query($sql);

    echo "<h4 class='tablebar'>Bild der Woche</h4>";
    // simon, 13.4.2011, damit man mehr von den terminen sieht
    // urs, 17.4.2012, finde ich grafisch nicht überzeigend und der Gewinn an Höhe ist minimal
    echo "<div style='text-align:center;'>";
    if (($do == "vorschau") and ($db_edit == "1")) {
        $row = $vorschau;
    } else {
        $row = $result->fetch_assoc();
    }
    $text = $row['text'];
    $titel = $row['titel'];
    $id_tmp = $row['id'];

    if ($zugriff and ($db_edit == "0")) {
        $edit_admin = "<a href='startseite.php?id={$id_tmp}&amp;button{$db_table}=start' class='linkedit'>&nbsp;</a>";
    } else {
        $edit_admin = "";
    }

    if (substr($bild1, 0, 4) != 'img/') {
        $bild1 = 'img/'.$bild1;
    }

    $img2 = olz_image($db_table, $id_tmp, 2, 256);
    if ($img2 == "Bild nicht vorhanden (in olz_image)") {
        echo olz_image($db_table, $id_tmp, 1, 256)."<p style='text-align:center;font-weight:bold;clear:left;'>".$edit_admin.$text."</p>";
    } else {
        echo "<span style='display:inline;' onmouseover='document.getElementById(&quot;bdw1&quot;).style.display = &quot;none&quot;; document.getElementById(&quot;bdw2&quot;).style.display = &quot;inline&quot;;' onclick='document.getElementById(&quot;bdw1&quot;).style.display = &quot;none&quot;; document.getElementById(&quot;bdw2&quot;).style.display = &quot;inline&quot;; return false;' id='bdw1'>".olz_image($db_table, $id_tmp, 1, 256)."</span><span style='display:none;' onmouseout='document.getElementById(&quot;bdw1&quot;).style.display = &quot;inline&quot;; document.getElementById(&quot;bdw2&quot;).style.display = &quot;none&quot;;' onclick='document.getElementById(&quot;bdw1&quot;).style.display = &quot;inline&quot;; document.getElementById(&quot;bdw2&quot;).style.display = &quot;none&quot;; return false;' id='bdw2'>".$img2."</span><p style='text-align:center;font-weight:bold;clear:left;'>".$edit_admin.$text."</p>";
    }
    echo "</div>";
}
echo "<br>";
// tickers.php
termine_ticker([
    "eintrag_laenge" => 80,
    "eintrag_anzahl" => 8,
]);
