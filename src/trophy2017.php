<h2 style='font-size:2em; border:0px; text-align:center;'>OL Zimmerberg Trophy 2017</h2>
<p style='text-align:center; font-size:1.3em; max-width:600px; margin:0px auto;'>Kleine Abend-OLs für Jung und Alt, für Schülerinnen und Schüler, Familien, Paare, Hobbysportlerinnen und Hobbysportler &mdash; alleine oder im Team</p>
<p style='text-align:center;'><i style='font-size:1.4em;'>Es sind keine speziellen Vorkenntnisse nötig.</i></p>
<p style='text-align:center;'>Die Versicherung ist Sache der Teilnehmenden. Der Veranstalter lehnt, soweit gesetzlich zulässig, jede Haftung ab.</p>

<h3 style='font-size:1.7em;'>Etappen</h3>
<script type="text/javascript" src="library/wgs84_ch1903/wgs84_ch1903.js"></script>
<script type="text/javascript">
function map(xkoord, ykoord) {
    var breite = 400;

    // Neue Mapbox Karte
    var lat = CHtoWGSlat(xkoord, ykoord);
    var lng = CHtoWGSlng(xkoord, ykoord);

    // Link (im Moment wird noch auf Search.ch verlinkt, denn dort sieht man öV Haltestellen): https://api.tiles.mapbox.com/v4/allestuetsmerweh.m35pe3he/page.html?access_token=pk.eyJ1IjoiYWxsZXN0dWV0c21lcndlaCIsImEiOiJHbG9tTzYwIn0.kaEGNBd9zMvc0XkzP70r8Q#15/"+lat+"/"+lng+"
    return "<a href='http://map.search.ch/"+xkoord+","+ykoord+"' target='_blank'><img src='https://api.tiles.mapbox.com/v4/allestuetsmerweh.m35pe3he/pin-l+009000("+lng+","+lat+")/"+lng+","+lat+",13/"+breite+"x300.png?access_token=pk.eyJ1IjoiYWxsZXN0dWV0c21lcndlaCIsImEiOiJHbG9tTzYwIn0.kaEGNBd9zMvc0XkzP70r8Q' class='noborder' style='margin:0px;padding:0px;align:center;border:1px solid #000000;'><\/a>";
}
</script>

<?php

$etappen = array(
    array("Dienstag, 11.04.2017", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Richterswil", "Schulhaus Feld", 694830, 229350,
        "Mit SOB bis Bahnhof Burghalde",
        "Schulhausplatz",
        "Keine, im Freien; WC vorhanden",
        false,
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>",
        "Startgeld: 5.-<br>Zusatzkarte: 3.- (für Gruppen)",
        "2017-trophy-richti",
        5187,
        ""),
    array("Sonntag, 21.05.2017", "9:00 &ndash; 12:00 (Anmeldung vor Ort)", "Horgen", "Schulhaus Berghalden", 687780, 234570,
        "Mit Bus 131 bis Horgen Heubach (via Horgen Oberdorf)",
        "Markiert ab Autobahnausfahrt Horgen und Seestrasse",
        "Im Schulhaus",
        false,
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>",
        "7.- – 18.- (siehe <a href='?page=11' class='linkint'>Ausschreibung</a>)",
        "2017-trophy-horgen",
        5192,
        false),
    array("Dienstag, 13.06.2017", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Adliswil", "Freizeitanlage Werd", 682345, 241122,
        "Bahnhof Adliswil oder mit Bus 185 direkt bis Tiefacker, Wege entlang Sihl markiert",
        "Öffentliche Parkplätze Stadthaus Sihlseits oder Parkhaus Bahnhof Adliswil, Wege entlang Sihl markiert",
        "Keine, im Freien; WC vorhanden; keine Sanität, Apotheke vorhanden",
        false,
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>",
        "Startgeld: 5.-<br>Zusatzkarte: 3.- (für Gruppen)",
        "2017-trophy-adliswil",
        5195,
        ""),
    array("Dienstag, 04.07.2017", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Au ZH", "Restaurant Halbinsel Au", 691439, 233849,
        "Mit S8 bis Au ZH",
        "Wenige beim Restaurant Halbinsel Au. Die Anreise mit öV wird empfohlen",
        "Keine, im Freien; WC vorhanden",
        false,
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>",
        "Startgeld: 5.-<br>Zusatzkarte: 3.- (für Gruppen)",
        "2017-trophy-au",
        5201,
        ""),
    array("Sonntag, 24.09.2017", "vorgegebene Startzeit (Voranmeldung erforderlich)", "Horgen Waldegg (Zürcher OL)", "Schulhaus Waldegg, Waldeggstrasse 5, 8810 Horgen", 688450, 233300,
        "Zusatzbusse verkehren ab Bahnhof Horgen bis zum Laufzentrum Waldegg",
        "Eine beschränkte Anzahl Parkplätze ist vorhanden",
        "vorhanden",
        "http://www.zuercherol.ch",
        "<!--<a href='http://www.sport.zh.ch/internet/sicherheitsdirektion/sport/de/zuercher_ol/kategorien.html' target='_blank' class='linkext'>siehe hier</a>, -->es wird in Teams gestartet",
        "ca. 35&ndash;45.- (je nach Kategorie), inklusive Zugticket und Verpflegung",
        "2017-trophy-zuercher",
        5211,
        false),
);

$gemeindeduell = array(
'Horgen' => 150,'Richterswil' => 80,'Wädenswil' => 63,
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
    <tr><td></td><td><a href='".$etappe[9]."' style='font-size:inherit;' class='linkext'>weitere Infos</a>":"").($etappe[12] && is_file("resultate/data/".$etappe[12].".xml")?"</td></tr>
    <tr><td></td><td><a href='resultate/?file=data/".$etappe[12].".xml' style='font-size:inherit;' class='linkint'>Resultate</a>":"")."</td></tr>
    </table></div></td><td style='width:20%; padding:5px 0px 5px 10px;'><script>document.write(map(".$etappe[4].",".$etappe[5]."))</script></td></tr>";
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
    <b>Rangliste (Top 3 nach allen 5 Läufen)</b><br>
    <table><tr><th style='width:1%;'>Gemeinde</th><th style='width:40px;'>&nbsp;</th><th style='width:auto;'>Starts</th></tr><?php
    arsort($gemeindeduell);
    foreach ($gemeindeduell as $k => $v) {
        if (0<strlen($k)) echo "<tr><td>".$k."</td><td></td><td>".$v."</td></tr>";
    }
    ?></table><br><br></td></tr>
<tr><td>Preise:</td><td style='padding-left:10px;'>In allen Kategorien gibt es eine Einzelrangliste für jeden Lauf, dem Sieger gebührt Ruhm und Ehre.<br>
    Wer drei oder mehr Läufe absolviert, erhält am dritten Lauf einen Erinnerungspreis.</td></tr>
<tr><td>Zimmerberg OL:</td><td style='padding-left:10px;'>Der 2. Lauf der OL Zimmerberg Trophy ist zugleich der Zimmerberg OL.<br>
<tr><td>Zürcher OL:</td><td style='padding-left:10px;'>Der 5. Lauf der OL Zimmerberg Trophy ist zugleich der Zürcher OL (separate Ausschreibung).<br>
    Für diesen Lauf ist es von Vorteil, sich im Voraus anzumelden.<br>
    Details: <a href='http://www.zuercherol.ch' class='linkext'>www.zuercherol.ch</a>.</td></tr>
<tr><td>Auskunft:</td><td style='padding-left:10px;'>Martin Gross, Kirchstrasse 7, 8805 Richterswil<br>
044 784 59 77 / <script>document.write(MailTo('martin.gross', 'olzimmerberg.ch', 'E-Mail', 'OL Zimmerberg Trophy'));</script></td></tr>
</table>
