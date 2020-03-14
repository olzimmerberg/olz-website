<h2 style='font-size:24px; border:0px; text-align:center;'>OL Zimmerberg Trophy 2020</h2>
<p style='text-align:center; font-size:15px; max-width:600px; margin:0px auto;'>Kleine Abend-OLs für Jung und Alt, für Schülerinnen und Schüler, Familien, Paare, Hobbysportlerinnen und Hobbysportler &mdash; alleine oder im Team</p>
<p style='text-align:center;'><i style='font-size:17px;'>Es sind keine speziellen Vorkenntnisse nötig.</i></p>
<p style='text-align:center;'>Die Versicherung ist Sache der Teilnehmenden. Der Veranstalter lehnt, soweit gesetzlich zulässig, jede Haftung ab.</p>

<h3 style='font-size:18px;'>Etappen</h3>
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

$etappen = [
    ["Dienstag, 31.03.2020", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Richterswil", "Jugendherberge", 695990, 229630,
        "Bahnhof Richterswil",
        "Tiefgarage Horn",
        "Keine, im Freien; WC vorhanden",
        false,
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>",
        "gratis",
        "2020-trophy-richti",
        5753,
        false, ],
    ["Dienstag, 28.04.2020", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Wädenswil", "Schulhaus Rotweg", 693140, 231460,
        "Bushaltestelle Schmiedstube",
        "Tiefgarage Schulhaus Rotweg",
        "Keine, im Freien; WC vorhanden",
        false,
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>",
        "gratis",
        "2020-trophy-waedi",
        5745,
        false, ],
    ["Mittwoch, 13.05.2020", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Kopfholz", "Schulhaus Chopfholz", 682760, 240270,
        "Bushaltestellen Kopfholz oder Loorain",
        "wenige im Quartier",
        "Keine, im Freien; WC vorhanden",
        false,
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>",
        "gratis",
        "2020-trophy-kopfholz",
        5721,
        false, ],
    ["Dienstag, 30.06.2020", "18:00 &ndash; 19:30 (Anmeldung vor Ort)", "Halbinsel Au", "Schulhaus Ort", 691423, 233407,
        "Bahnhof Au ZH",
        "wenige im Quartier",
        "Keine, im Freien; WC vorhanden",
        false,
        "<div style='color:red;'>rot <i>(schwierig, 3-4km)</i></div><div style='color:blue;'>blau <i>(einfach, 2-3km)</i></div><div style='color:green;'>grün <i>(einfach, 1-2km)</i></div>",
        "gratis",
        "2020-trophy-au",
        5751,
        false, ],
];

$gemeindeduell = [
];

echo "<table>";
for ($i = 0; $i < count($etappen); $i++) {
    $etappe = $etappen[$i];
    echo "<tr><td id='id".$etappe[13]."' style='padding:5px 0px;'><div><h4 style='font-size:18px;'>".$etappe[2]."</h4><table>
    <tr><td style='width:100px;'>Datum:</td><td><b>".$etappe[0]."</b></td></tr>
    <tr><td>Besammlung:</td><td>".$etappe[3]."</td></tr>
    <tr><td>Anmeldung:</td><td>".$etappe[1]."</td></tr>
    ".($etappe[14] ? "<tr><td></td><td>".$etappe[14]."</td></tr>" : "")."
    <tr><td>Kategorien:</td><td>".$etappe[10]."</td></tr>
    <tr><td>Kosten:</td><td>".$etappe[11]."</td></tr>
    <tr><td>öV:</td><td>".$etappe[6]."</td></tr>
    <tr><td>Parkplätze:</td><td>".$etappe[7]."</td></tr>
    <tr><td>Garderobe:</td><td>".$etappe[8]."</td></tr>
    <tr><td></td><td><a href='?page=3#id".$etappe[13]."' class='linkint'>Termine-Eintrag</a>".($etappe[9] ? "</td></tr>
    <tr><td></td><td><a href='".$etappe[9]."' class='linkext'>weitere Infos</a>" : "").($etappe[12] && is_file("resultate/data/".$etappe[12].".xml") ? "</td></tr>
    <tr><td></td><td><a href='resultate/?file=data/".$etappe[12].".xml' class='linkint'>Resultate</a>" : "")."</td></tr>
    </table></div></td><td style='width:20%; padding:5px 0px 5px 10px;'>".($etappe[4] != 0 ? "<script>document.write(map(".$etappe[4].",".$etappe[5]."))</script>" : "")."</td></tr>";
    if (isset($_SESSION['auth']) && $_SESSION['auth'] == 'all' && $etappe[12]) {
        if (isset($_FILES["resultate_upload_".$etappe[13]])) {
            move_uploaded_file($_FILES["resultate_upload_".$etappe[13]]['tmp_name'], $etappe[12]);
        }
        echo "<tr><td><b>Resultate hochladen</b><br><input type='file' name='resultate_upload_".$etappe[13]."' /><input type='submit' value='Abschicken' /></td><td>".json_encode($_FILES)."</td></tr>";
    }
}
echo "</table>";

?>

<h3>Weitere Informationen</h3>
<table style='max-width:600px; margin:0px auto;'>
<tr><td>Ausrüstung:</td><td style='padding-left:10px;'> Joggingdress und Joggingschuhe genügen.</td></tr>
<tr><td>Trophy:</td><td style='padding-left:10px;'>Jeder Lauf ist eine eigene abgeschlossene Veranstaltung.<br>
    Zusammen bilden sie die OL Zimmerberg Trophy.</td></tr>
<!--<tr><td>Gemeindeduell:</td><td style='padding-left:10px;'>Jeder Teilnehmende sammelt pro Lauf einen Punkt für seine Gemeinde.<br>
    Die Gemeinde mit den meisten Punkten erhält am Ende einen Wanderpreis!<br><br>
    <b>Rangliste (vorläufig)</b><br>
    <table><tr><th style='width:1%;'>Gemeinde</th><th style='width:40px;'>&nbsp;</th><th style='width:auto;'>Starts</th></tr><?php
    arsort($gemeindeduell);
    foreach ($gemeindeduell as $k => $v) {
        if (strlen($k) > 0) {
            echo "<tr><td>".$k."</td><td></td><td>".$v."</td></tr>";
        }
    }
    ?></table><br><br></td></tr>-->
<tr><td>Preise:</td><td style='padding-left:10px;'>In allen Kategorien gibt es eine Einzelrangliste für jeden Lauf, dem Sieger gebührt Ruhm und Ehre.<br>
    Wer drei oder mehr Läufe absolviert, erhält am dritten Lauf einen Erinnerungspreis.</td></tr>
<tr><td>sCOOL-Cup:</td><td style='padding-left:10px;'>Der 3. Lauf der OL Zimmerberg Trophy ist zugleich ein sCOOL-Cup.</td></tr>
<tr><td>Auskunft:</td><td style='padding-left:10px;'>Martin Gross, Kirchstrasse 7, 8805 Richterswil<br>
044 784 59 77 / <script>document.write(MailTo('martin.gross', 'olzimmerberg.ch', 'E-Mail', 'OL Zimmerberg Trophy'));</script></td></tr>
</table>
