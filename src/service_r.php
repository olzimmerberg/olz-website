<?php

// =============================================================================
// Diverse weitere Funktionen.
// =============================================================================

?>
<div><b>PC-Konto: 85-256448-8</b></div>

<h2>Newsletter</h2>
<!--<p>Hier hast du die Möglichkeit einen Newsletter zu abonnieren. Damit wirst du automatisch per Mail über wichtige Vorstandsmitteilungen, die nächsten Meldeschlüsse oder Aktualisierungen der Homepage benachrichtigt.</p>
<p>
<b>Datenschutz: Alle Angaben werden vertraulich behandelt und keinesfalls an Dritte weitergegeben. Sie werden ausschliesslich für diesen Newsletter-Versand verwendet.</b>
</p>-->

<?php echo get_olz_text(6); ?>

<?php
$db_table = 'newsletter';
$kategorien = [['aktuell', 'Neuen Nachrichten', ''], ['forum', 'Neuen Forumsbeiträgen', ''], ['termine', 'Wichtige Termine (z.B. Meldeschluss)', ''], ['vorstand', 'Vorstandsmitteilungen', '1']];

//-------------------------------------------------------------
// ZUGRIFF
if ((($_SESSION['auth'] ?? null) == 'all') or (in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? '')))) {
    $zugriff = "1";
} else {
    $zugriff = "0";
}
$button_name = 'button'.$db_table;
if (isset($_GET[$button_name])) {
    $_POST[$button_name] = $_GET[$button_name];
}
if (isset($_POST[$button_name])) {
    $_SESSION['edit']['db_table'] = $db_table;
}

//-------------------------------------------------------------
// USERVARIABLEN PRÜFEN
if (isset($code)) {
    $uid = $code;
}
if (isset($id) and is_ganzzahl($id)) {
    $_SESSION[$db_table."id_"] = $id;
} else {
    $id = ($_SESSION[$db_table.'id_'] ?? null);
}

//-------------------------------------------------------------
// BEARBEITEN
if ($zugriff) {
    $functions = ['neu' => 'Anmelden',
        'code' => 'Einstellungen ändern',
        'edit' => 'Bearbeiten',
        'start_user' => 'Weiter',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'undo' => 'undo',
        'delete' => 'Löschen', ];
} else {
    $functions = ['neu' => 'Anmelden',
        'code' => 'Einstellungen ändern',
        'edit' => 'Bearbeiten',
        'start_user' => 'Weiter',
        'abbruch' => 'Abbrechen',
        'vorschau' => 'Vorschau',
        'save' => 'Speichern',
        'undo' => 'undo',
        'delete' => 'Löschen', ];
}
$function = array_search($_POST[$button_name], $functions);
if ($function != "") {
    include 'admin/admin_db.php';
}
if (($_SESSION['edit']['table'] ?? null) == $db_table) {
    $db_edit = "1";
} else {
    $db_edit = "0";
}
$_SESSION[$db_table.'id_'] = $id;

//-------------------------------------------------------------
// MENÜ
if ($db_edit == "0") {
    echo "<div class='buttonbar'>".olz_buttons("button".$db_table, [["Anmelden", "0"], ["Einstellungen ändern", "1"]], "")."</div>";
}
if ($zugriff) {
    echo "<div class='buttonbar'><a href='divmail.php?buttonrundmail=Neues Rundmail'>Rundmail verschicken</a></div>";
}

//-------------------------------------------------------------
// VORSCHAU
if (($do ?? null) == 'vorschau') {
    $row = $vorschau;
    echo "<table class='liste'><tr>";
    $tmp_html = "";
    $name = $row['name'];
    $email = $row['email'];
    $kategorie = explode(" ", $row['kategorie']);
    $code = $row['uid'];

    foreach ($kategorien as $tmp_kategorie) {
        if (in_array($tmp_kategorie[0], $kategorie)) {
            $tmp_html .= "<div class='linkint' style='font-weight:normal;'> ".$tmp_kategorie[1]."</div>";
        }
    }
    echo "<td><b>Vorname, Name:</b> <span style='font-weight:normal;'>{$name}</span></td></tr>";
    echo "<td><b>Email-Adresse:</b> <span style='font-weight:normal;'>{$email}</span></td></tr>";
    echo "<td><b>Benachrichtigung bei:</b>{$tmp_html}</td></tr>";
    echo "<td class='test-flaky'><b>Code:</b> <span style='font-weight:normal;'>{$code}</span></td></tr>";
    echo "</table>";
}

if (isset($feedback)) {
    echo "<div class='buttonbar error'>".$feedback."</div>";
    $feedback = "";
}
/*
simon, 20.4.2011, RSS war ünnötig. und es existiert ja sowieso nicht mehr.
echo "<p style='height:15px;'>
<h2>Was ist \"RSS\"?</h2>
<p>
    RSS ist ein Service auf Webseiten, der ähnlich einem Nachrichtenticker die Überschriften mit einem kurzen Textanriss und einen Link zur Originalseite enthält. Die Bereitstellung von Daten im RSS-Format bezeichnet man auch als RSS-Feed. Er liefert dem Leser, wenn er einmal abonniert wurde, automatisch neue Einträge.
</p>
<p>
    Den RSS-Feed der OLZ-Homepage erreichst du über das RSS-Symbol <img src='icns/rss_marke.gif' style='height:12px;' class='noborder' alt=''> unten in der Menüspalte.
</p>
<p>
    Mehr Informationen dazu findest du auf <a href='http://de.wikipedia.org/wiki/RSS' target='_blank'>Wikipedia</a>.
</p>";
*/
?>
