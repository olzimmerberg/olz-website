<h2 style='font-size:2em; border:0px; text-align:center;'>OL Zimmerberg Trophy 2016</h2>
<p style='text-align:center; font-size:1.3em; max-width:600px; margin:0px auto;'>Kleine Abend-OLs für Jung und Alt, für Schülerinnen und Schüler, Familien, Paare, Hobbysportlerinnen und Hobbysportler &mdash; alleine oder im Team</p>
<p style='text-align:center;'><i style='font-size:1.4em;'>Es sind keine speziellen Vorkenntnisse nötig.</i></p>

<h3 style='font-size:1.7em;'>Etappen</h3>
<?php

$etappen = array(
    array("Dienstag, 05.04.2016", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Horgen", "Dorfplatz", 687735, 234975, 
        "Mit S2 oder S8 bis Horgen (ab Passarelle markiert)", 
        "Es hat einige Parkhäuser in der Nähe", 
        "Keine, im Freien", 
        false, 
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>", 
        "Startgeld: 5.-<br>Zusatzkarte: 3.- (für Gruppen)", 
        "trophy2016/resultate/horgen.html", 
        5006,
        "(Das OLZ-Kartentraining findet wie gewohnt 18:30-20:00 statt)"),
    array("Dienstag, 10.05.2016", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Richterswil", "Wysshusplatz", 696075, 229225, 
        "Mit S2 oder S8 bis Richterswil Bahnhof", 
        "Öffentliche Parkplätze beim Bahnhof", 
        "Keine, im Freien", 
        false, 
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>", 
        "Startgeld: 5.-<br>Zusatzkarte: 3.- (für Gruppen)", 
        "trophy2016/resultate/richterswil.html", 
        5012,
        "(Das OLZ-Kartentraining findet wie gewohnt 18:30-20:00 statt)"),
    array("Dienstag, 07.06.2016", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Thalwil", "Chilbiplatz", 685050, 238095, 
        "Mit Bus 140 oder 240 bis Thalwil, Schützenhaus ", 
        "Öffentliche Parkplätze beim Chilbiplatz", 
        "Keine, im Freien", 
        false, 
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>", 
        "Startgeld: 5.-<br>Zusatzkarte: 3.- (für Gruppen)", 
        "trophy2016/resultate/thalwil.html", 
        5015,
        "(Das OLZ-Kartentraining findet wie gewohnt 18:30-20:00 statt)"),
    array("Dienstag, 05.07.2016", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Wädenswil", "Seeplatz", 693675, 231715, 
        "Mit S2, S8, IR bis Wädenswil", 
        "Öffentliche Parkplätze am Bahnhof", 
        "Keine, im Freien", 
        false, 
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>", 
        "Startgeld: 5.-<br>Zusatzkarte: 3.- (für Gruppen)", 
        "trophy2016/resultate/waedenswil.html", 
        5022,
        "(Das OLZ-Kartentraining findet wie gewohnt 18:30-20:00 statt)"),
    array("Sonntag, 25.09.2016", "vorgegebene Startzeit (Voranmeldung erforderlich)", "Zürich (Zürcher OL)", "Europa-Allee", 682805, 248055, 
        "Mit S2, S8, IR bis Zürich HB", 
        "Parkhaus Sihlquai oder Gessnerallee", 
        "In der Pädagogischen Hochschule Zürich (PHZH)", 
        "http://www.zuercherol.ch", 
        "<a href='http://www.sport.zh.ch/internet/sicherheitsdirektion/sport/de/zuercher_ol/kategorien.html' target='_blank' class='linkext'>siehe hier</a>, es wird in Teams gestartet", 
        "35&ndash;45.- (je nach Kategorie), inklusive Zugticket und Verpflegung",
        false, 
        5031,
        false),
);

$gemeindeduell = array(
'Adliswil' => 29,'Einsiedeln' => 14,'Hirzel' => 15,'Horgen' => 166,'Langnau' => 28,'Oberrieden' => 13,'Richti' => 133,'Schönenberg' => 2,'Thalwil' => 99,'Wädi' => 136,'Züri' => 56,
//'Adliswil' => 21,'Einsiedeln' => 7,'Hirzel' => 15,'Horgen' => 122,'Langnau' => 22,'Oberrieden' => 9,'Richti' => 112,'Schönenberg' => 1,'Thalwil' => 71,'Wädi' => 115,'Züri' => 37,'' => 8,'' => 8,'' => 8,'' => 8,'' => 8,'' => 8,'' => 8,'' => 8,
//'Adliswil' => 17,'Einsiedeln' => 5,'Hirzel' => 12,'Horgen' => 89,'Langnau' => 13,'Oberrieden' => 7,'Richti' => 83,'Schönenberg' => 1,'Thalwil' => 56,'Wädi' => 74,'Züri' => 23,'' => 8,'' => 8,'' => 8,'' => 8,'' => 8,'' => 8,'' => 8,'' => 8,
);

echo "<table>";
for ($i=0; $i<count($etappen); $i++) {
    $etappe = $etappen[$i];
    echo "<tr><td id='id".$etappe[13]."' style='padding:5px 0px;'><div style='font-size:1.2em;'><h4 style='font-size:1.3em;'>".$etappe[2]."</h4><table>
    <tr><td style='width:100px;'>Datum:</td><td><b style='font-size:inherit;'>".$etappe[0]."</b></td></tr>
    <tr><td>Besammlung:</td><td>".$etappe[3]."</td></tr>
    <tr><td>Anmeldung:</td><td>".$etappe[1]."</td></tr>
    ".($etappe[14]?"<tr><td></td><td>".$etappe[14]."</td></tr>":"")."
    <tr><td>Kategorien:</td><td>".$etappe[10]."</td></tr>
    <tr><td>Kosten:</td><td>".$etappe[11]."</td></tr>
    <tr><td>öV:</td><td>".$etappe[6]."</td></tr>
    <tr><td>Parkplätze:</td><td>".$etappe[7]."</td></tr>
    <tr><td>Garderobe:</td><td>".$etappe[8]."</td></tr>
    <tr><td></td><td><a href='?page=3#id".$etappe[13]."' class='linkint'>Termine-Eintrag</a>".($etappe[9]?"</td></tr>
    <tr><td></td><td><a href='".$etappe[9]."' style='font-size:inherit;' class='linkext'>weitere Infos</a>":"").($etappe[12]?"</td></tr>
    <tr><td></td><td><a href='".$etappe[12]."' style='font-size:inherit;' class='linkint'>Resultate</a>":"")."</td></tr>
    </table></div></td><td style='width:20%; padding:5px 0px 5px 10px;'><a href='http://map.classic.search.ch/".$etappe[4].",".$etappe[5]."?b=high' target='_blank'><img src='http://map.search.ch/chmap.jpg?x=-200&y=-150&w=400&h=300&poi=&base=".$etappe[4].",".$etappe[5]."&layer=bg,fg,circle&zd=4' class='noborder' style='margin:0px;padding:0px;align:center;border:1px solid #000000;'></a></td></tr>";
    if (isset($_SESSION['auth']) && $_SESSION['auth']=='all' && $etappe[12]) {
        if (isset($_FILES["resultate_upload_".$etappe[13]])) {
            move_uploaded_file($_FILES["resultate_upload_".$etappe[13]]['tmp_name'], $etappe[12]);
        }
        echo "<tr><td><b>Resultate hochladen</b><br><input type='file' name='resultate_upload_".$etappe[13]."' /><input type='submit' value='Abschicken' /></td><td>".json_encode($_FILES)."</td></tr>";
    }
}
echo "</table>";

?>

<h3 style='font-size:1.7em;'>Weitere Informationen</h3>
<table style='max-width:600px; margin:0px auto;'>
<tr><td>Ausrüstung:</td><td style='padding-left:10px;'> Joggingdress und Joggingschuhe genügen.</td></tr>
<tr><td>Trophy:</td><td style='padding-left:10px;'>Jeder Lauf ist eine eigene abgeschlossene Veranstaltung.<br>
    Zusammen bilden sie die OL Zimmerberg Trophy.</td></tr>
<tr><td>Gemeindeduell:</td><td style='padding-left:10px;'>Jeder Teilnehmende sammelt pro Lauf einen Punkt für seine Gemeinde.<br>
    Die Gemeinde mit den meisten Punkten erhält am Ende einen Wanderpreis!<br><br>
    <b>Rangliste (nach allen 5 Läufen)</b><br>
    <table><tr><th style='width:1%;'>Gemeinde</th><th style='width:40px;'>&nbsp;</th><th style='width:auto;'>Starts</th></tr><?php 
    arsort($gemeindeduell);
    foreach ($gemeindeduell as $k => $v) {
        if (0<strlen($k)) echo "<tr><td>".$k."</td><td></td><td>".$v."</td></tr>";
    }
    ?></table><br><br></td></tr>
<tr><td>Preise:</td><td style='padding-left:10px;'>In allen Kategorien gibt es eine Einzelrangliste für jeden Lauf, dem Sieger gebührt Ruhm und Ehre.<br>
    Wer drei oder mehr Läufe absolviert, erhält am dritten Lauf einen Erinnerungspreis.</td></tr>
<tr><td>Zürcher OL:</td><td style='padding-left:10px;'>Der 5. Lauf der OL Zimmerberg Trophy ist zugleich der Zürcher OL (separate Ausschreibung, wird nicht von der OL Zimmerberg organisiert).<br>
    Für diesen Lauf ist es von Vorteil, sich im Voraus anzumelden.<br>
    Details: <a href='http://www.zuercherol.ch' class='linkext'>www.zuercherol.ch</a>.</td></tr>
<tr><td>Hintergrund:</td><td style='padding-left:10px;'> Vor zehn Jahren fusionierten die OL-Gruppen Horgen und Thalwil.  Aus Anlass des 10-jährigen Bestehens organisiert der neu entstandene Verein OL Zimmerberg im Frühling und Sommer 2016 in den Gemeinden Horgen, Richterswil, Wädenswil und Thalwil jeweils an einem Dienstagabend einen Dorf-OL für Jung und Alt. </td></tr>
<tr><td>Auskunft:</td><td style='padding-left:10px;'>Martin Gross, Kirchstrasse 7, 8805 Richterswil<br>
044 784 59 77 / <script>document.write(MailTo('martin.gross', 'olzimmerberg.ch', 'E-Mail', 'OL Zimmerberg Trophy'));</script></td></tr>
</table>