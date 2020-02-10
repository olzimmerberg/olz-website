<script type="text/javascript" src="http://map.search.ch/api/map.js"></script>
<script type="text/javascript">
    function map(id,xkoord,ykoord) {
    var div;
    mapid = "map";
    div = document.getElementById(mapid);
    if(div.style.display=="none") {
    div.style.display="";
    breite = document.getElementById('Spalte1').offsetWidth-20;
    div.innerHTML="<a href='http://map.search.ch/"+xkoord+","+ykoord+"' target='_blank'>
        <img src='http://map.search.ch/chmap.jpg?x=-"+breite/2+"&y=-150&w="+breite+"&h=300&poi=&base="+xkoord+","+ykoord+"&layer=sym,fg,bg,copy,circle&zd=8' class='noborder' style='margin:0px;padding:0px;align:center;border:1px solid #000000;'>
        <\/a>";
            mapid = "map_";
            div = document.getElementById(mapid);
            div.innerHTML="<a href='' onclick=\"map('dummy',"+xkoord+","+ykoord+");return false;\" class='linkmap'>Karte ausblenden
                <\/a >"
                    }
                    else {
                    div.style.display="none";
                    div.innerHTML="";
                    mapid = "map_";
                    div = document.getElementById(mapid);
                    div.innerHTML="<a href='' onclick=\"map('dummy',"+xkoord+","+ykoord+");return false;\" class='linkmap'>Karte zeigen
                        <\/a >"
                            }
                            return false;}
</script>
<h2>10. Zimmerberg OL, Sonntag, 21. Mai 2016</h2>
<h3 class='nobox'><b>Regionaler Lauf (*28) / Lauf der Jugend-OL-Meisterschaft ZH/SH / Stadt OL Cup 2017<br> Testlauf Juniorenkategorien (DH16, DH20)<br>
        2. Lauf der OL Zimmerberg Trophy 2017</b></h3>
<table style='border-spacing:1px;'>
    <tr>
        <td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='?page=4&id=663' class='linkint'>Impressionen</a></td>
        <!--<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%;'><a href='#ausschreibung' class='linkint'>Ausschreibung</a></td>
        <td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%;'><a href='#bahndaten' class='linkint'>Bahndaten</a></td>-->

<!--<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%;'><a href='?page=4&id=808' class='linkint'>Galerie</a></td>-->
        <td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='?page=99&event=zol_170521' class='linkint'>Resultate</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='http://www.routegadget.ch/binperl/reitti.cgi?act=map&id=209&kieli=' tartet='_blank'  target='_blank' class='linkext'>RouteGadget</a> </td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='#fundsachen' class='linkint'>Fundgegenstände</a></td>
<!--<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='#fotool' class='linkint'>Foto-OL</a></td>
-->
    </tr>
</table>

<!--BILDER-->
<a href='#top' name='impressionen'><h3 style='margin-top:10px;margin-bottom:10px;'>Impressionen</h3></a>
<?php
// Bilder als 'BildnameBildnr_thumb.jpg' (110x73) und 'BildnameBildnr_gross.jpg' (800x533) abspeichern
echo "<table class='liste'>";
$groesse = 8;
$breite = 4;
$bild_name = "horgen";
$pfad_galerie = "img/zol_horgen_2017/";
$reihen = ($groesse - ($groesse % $breite)) / $breite;
if ($groesse % $breite != 0) {
    $reihen = $reihen + 1;
}
echo "<tbody id='galerieindex'>";
if ($groesse != 0) {
    for ($i = 0; $i < $reihen; $i++) {
        echo "<tr class='thumbs'>";
        for ($n = 0; $n < $breite; $n++) {
            $foto_000 = str_pad(($i * $breite + $n + 1), 3, '0', STR_PAD_LEFT);
            if (($i * $breite + $n + 1) > $groesse) {
                echo "<td id='galerietd".($i * $breite + $n + 1)."'>&nbsp;</td>";
            } else {
                $bild_nr = substr("0".($i * $breite + $n + 1), -2);
                $pfad_thumb = $pfad_galerie.$bild_name.$bild_nr."_thumb.jpg";
                $pfad_img = $pfad_galerie.$bild_name.$bild_nr."_gross.jpg";
                echo "<td id='galerietd".($i * $breite + $n + 1)."'>";
                echo "<a href='".$pfad_img."' class='lightview' rel='gallery[myset]'><img src='".$pfad_thumb."' alt='' onerror='onimageloaderror(this)' id='".($foto_000)."'></a>";
                echo "</td>";
            }
        }
        if ($i >= $groesse) {
            break;
        }
    }
    echo "</tr></table>";
}

?>

<h3 style='margin-top:20px;'>Fundgegenstände</h3>
<table style='margin-top:20px;margin-bottom:20px;'>
    <tr>
        <td><a href='https://www.dropbox.com/sh/gdz3upcreo3p4kl/AABw10hQzE1MKLTjV-SPr3fMa?dl=0' class='linkext' target='_blank'>Fundsachen</a><p>
Bitte bei mir melden: Marlies Laager, Tel 044 725 88 09 zol@olzimmerberg.ch</td></tr></table>

<!--<a href='index.php?page=99&event=zol_170521'><h3 style='margin-top:30px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Live-Resultate</h3></a>-->

<!--<a href='#Bahndaten'><h3 style='margin-top:10px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Bahndaten</h3></a>-->

<!--<a href='http://www.o-l.ch/cgi-bin/results?type=start&year=2013&event=Zimmerberg+OL/JOM+Schlusslauf&kind=all' target='_blank'><h3 style='margin-top:10px;'><img src='icns/pfeil_ext.png' class='noborder' style='margin-right:8px;'>Startliste JOM-Kategorien</h3></a>-->
<!--<a href='#top' name='ausschreibung'><h3 style='margin-top:30px;margin-bottom:10px;'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'>Ausschreibung</h3></a>
<table class='liste'>
    <tr>
        <td colspan="2"><a href="pdf/AusschreibungZimmerbergOL2017.pdf" class="linkpdf" target="_blank">Ausschreibung als PDF (Stand 11.5.17)</a>
        </td>
    </tr>
    <tr><td><b>Veranstalter</b></td><td>OL Zimmerberg</td></tr>
    <tr>
        <td><b>Laufleitung und Auskunft</b></td>
        <td>Marlies Laager, Tel. 044 725 88 09<br>
            <script type="text/javascript">document.write(MailTo("zol", "olzimmerberg.ch", "Laufleitung", "10. Zimmerberg OL 2016"));
            </script>
        </td>
    </tr>
    <tr>
        <td><b>Laufform</b></td>
        <td>Stadt-OL, Sprintdistanz</td>
    </tr>
    <tr>
        <td><b>Karte</b></td>
        <td>Horgen 1:4'000, Stand Mai 2017</td>
    </tr>
    <tr>
        <td><b>Bahnlegung/Kontrolle</b></td>
        <td>Jan Hug, Daniel Rohr / Hansjörg Gasser</td>
    </tr>
    <tr>
        <td><b>Anmeldung</b></td>
        <td>Nur am Lauftag von 9.00 – 12.00 Uhr, 1. Start 9.30 Uhr</td>
    </tr>
    <tr>
        <td><b>Wettkampfzentrum</b></td>
        <td>Oberstufenanlage Berghalden, Rainweg 16, 8810 Horgen
            <div id="map_"><a href="http://map.search.ch/687800,234556" target="_blank" onclick="map('dummy',687800,234556);return false;" class="linkmap">Karte zeigen</a>
            </div>
            <div id='map' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div>
        </td>
    </tr>
    <tr>
        <td><b>ÖV</b></td>
        <td><b>Empfehlung: Anreise über Horgen Oberdorf:</b><br>
            S24 ab HB Zürich x.21, x.51; S24 ab Thalwil x.09, x.39
            <p>
            Markiert ab den Bahnhöfen:<br>
            • Bahnhof Horgen Oberdorf, zu Fuss 15 Min. od. Bus 131 bis Heubach<br>
            • Bahnhof Horgen (See): Bus 131 bis Heubach od. zu Fuss 20 Min.<br>
            Markierter Weg darf nicht verlassen werden!
        </td>
    </tr>
    <tr>
        <td><b>Parkplätze</b></td>
        <td>Markiert ab Autobahnausfahrt Horgen und Seestrasse<br>
            Es dürfen nur die vorgegebenen PP benutzt werden:<br>
            PP bei Allmend, Fussweg ins WKZ 15 Min.
            <div id="map_"><a href="http://map.search.ch/688237,233851" target="_blank" onclick="map('dummy',688237,233851);return false;" class="linkmap">Karte zeigen</a>
            </div>
            <div id='map' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div>
        </td>
    </tr>
    <tr>
        <td><b>Kategorien</b></td>
        <td>Gemäss WO (D/H 20 nur Testlauf)</td>
    </tr>
    <tr>
        <td><b>Kartenwechsel</b></td>
        <td>Für einige Kategorien (siehe Bahndaten) gibt es einen Kartenwechsel. Die Karte ist beidseitig bedruckt. Der letzte Posten auf der ersten Seite entspricht dem Startdreieck auf der Rückseite. Die Nummerierung ist fortlaufend.<br>Zusätzlich ist die Karte an Beispiel von HAL wie folgt markiert: HAL (1) auf der erste Seite und HAL (2) auf der 2. Seite. Die Karten liegen mit der ersten Seite gegen unten in der Kartenbox.</td>
    </tr>
    <tr>
        <td><b>Einsteigerbahnen</b></td>
        <td>Offen kurz, mittel , lang / Familien /sCOOL<br></td>
    </tr>
    <tr>
        <td><b>Air+ SI Einheiten</b></td>
        <td>Posten für berührungsloses Stempeln freigeschaltet (Modus AIR+ aktiviert)<br></td>
    </tr>
    <tr>
        <td><b>Postenbeschreibungen</b></td>
        <td>Abgabe am Start. Nicht auf Karte gedruckt.<br></td>
    </tr>
    <tr>
        <td><b>Live-Resultate</b></td>
        <td>Live-Resultate können über das Internet auf olzimmerberg.ch oder im WKZ im lokalen WLAN abgefragt werden.<br></td>
    </tr>
    <tr>
        <td><b>Distanzen</b></td>
        <td>WKZ-Start 10 Min., Ziel-WKZ 5 Min.</td>
    </tr>
    <tr>
        <td><b>Start</b></td>
        <td>Fliegender Start, eingedruckte Bahnen für alle Kategorien<br>
            einige Kategorien: beidseitig bedruckte Karte
        </td>
    </tr>
    <tr>
        <td><b>Max. Laufzeit</b></td>
        <td>90 Min.
        </td>
    </tr>
    <tr>
        <td><b>Startgeld</b></td>
        <td>GÜNSTIGE Startgelder dank Gemeinde Horgen
            <p>
            1996 und älter: Fr. 18.--<br>
            1997 - 2000: Fr. 12.-- <br>
            2001 und jünger: Fr. 10.-- <br>
            Kategorie offen: gemäss Jahrgang <br>
            HorgnerInnen: Kinder und Jugendliche 7.-- / Erwachsene 10.--
            Badge-Miete: Fr. 2.--<br>
            zusätzliche Karte: Fr. 3.--
        </td>
    </tr>
    <tr>
        <td><b>Kinder</b></td>
        <td>Kinderhort (ab 2 Jahre)<br>Schnur-OL / Schulhaus-OL</td>
    </tr>
    <tr>
        <td><b>Verkehr</b></td>
        <td>Der grösste Teil des Laufgebiets ist für den Verkehr freigegeben. Die Verkehrsregeln sind zu beachten! Gesperrte Strassen dürfen nur an den gekennzeichneten Stellen überquert werden! (Keine Zeitneutralisierung)
        </td>
    </tr>
    <tr>
        <td><b>Altersheime</b></td>
        <td>Einige Posten sind im Bereich von Altersheimen. Beim Queren dieser Areale ist besondere Vorsicht geboten.
        </td>
    </tr>
    <tr>
        <td><b>Disqualifikation</b></td>
        <td>Die Missachtung der Verkehrsregeln und das Betreten von oliv-grün (Privatgebiet) und violett (Sperrgebiet) markiertem Gelände führen zur Disqualifikation.
        </td>
    </tr>
    <tr>
        <td><b>Versicherung</b></td>
        <td>Die Versicherung ist Sache der Teilnehmer. Der Veranstalter lehnt soweit gesetzlich zulässig, jede Haftung ab.
        </td>
    </tr>
    <tr>
        <td><b>Verpflegung</b></td>
        <td>Getränke am Ziel, attraktive Festwirtschaft im Laufzentrum
        </td>
    </tr>
    <tr>
        <td><a name='bahndaten'><b>Bahndaten</b></a><br>(Änderungen vorbehalten)</td>
        <td style='border:none;margin:0px;padding:0px;'>
            <table style='border-spacing:0;' border="1">
                <thead style='font-weight:bold;'>
                    <tr>
                        <td style='width:25%;background-color:#CCC'>Kategorie</td>
                        <td style='width:25%;background-color:#CCC'>Länge (km)</td>
                        <td style='width:25%;background-color:#CCC'>Steigung (m)</td>
                        <td style='width:25%;background-color:#CCC'>Kartenwechsel</td>
                    </tr>
                </thead>
                <tbody class='bahndaten'>
                    <tr><td>D10</td><td>1.43</td><td>30</td><td></td></tr>
                    <tr><td>D12</td><td>1.49</td><td>45</td><td></td></tr>
                    <tr><td>D14</td><td>1.84</td><td>55</td><td></td></tr>
                    <tr><td>D16</td><td>2.06</td><td>60</td><td></td></tr>
                    <tr><td>D18</td><td>2.11</td><td>70</td><td></td></tr>
                    <tr><td>D18K</td><td>1.54</td><td>35</td><td></td></tr>
                    <tr><td>D35</td><td>2.21</td><td>75</td><td></td></tr>
                    <tr><td>D40</td><td>2.21</td><td>75</td><td></td></tr>
                    <tr><td>D45</td><td>2.05</td><td>55</td><td></td></tr>
                    <tr><td>D50</td><td>2.05</td><td>55</td><td></td></tr>
                    <tr><td>D55</td><td>1.63</td><td>40</td><td></td></tr>
                    <tr><td>D60</td><td>1.63</td><td>40</td><td></td></tr>
                    <tr><td>D65</td><td>1.63</td><td>40</td><td></td></tr>
                    <tr><td>D70</td><td>1.54</td><td>35</td><td></td></tr>
                    <tr><td>D75</td><td>1.54</td><td>35</td><td></td></tr>
                    <tr><td>DAK</td><td>1.73</td><td>45</td><td></td></tr>
                    <tr><td>DAM</td><td>2.21</td><td>75</td><td></td></tr>
                    <tr><td>DAL</td><td>2.77</td><td>95</td><td>x</td></tr>
                    <tr><td>DB</td><td>1.85</td><td>45</td><td></td></tr>
                    <tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
                    <tr><td>H10</td><td>1.43</td><td>30</td><td></td></tr>
                    <tr><td>H12</td><td>1.61</td><td>60</td><td></td></tr>
                    <tr><td>H14</td><td>2.26</td><td>70</td><td></td></tr>
                    <tr><td>H16</td><td>2.38</td><td>80</td><td></td></tr>
                    <tr><td>H18</td><td>2.68</td><td>75</td><td></td></tr>
                    <tr><td>H18K</td><td>1.63</td><td>40</td><td></td></tr>
                    <tr><td>H35</td><td>2.77</td><td>95</td><td>x</td></tr>
                    <tr><td>H40</td><td>2.68</td><td>85</td><td>x</td></tr>
                    <tr><td>H45</td><td>2.68</td><td>85</td><td>x</td></tr>
                    <tr><td>H50</td><td>2.29</td><td>70</td><td></td></tr>
                    <tr><td>H55</td><td>2.29</td><td>70</td><td></td></tr>
                    <tr><td>H60</td><td>2.05</td><td>55</td><td></td></tr>
                    <tr><td>H65</td><td>2.05</td><td>60</td><td></td></tr>
                    <tr><td>H70</td><td>1.63</td><td>40</td><td></td></tr>
                    <tr><td>H75</td><td>1.54</td><td>35</td><td></td></tr>
                    <tr><td>H80</td><td>1.54</td><td>35</td><td></td></tr>
                    <tr><td>HAK</td><td>2.05</td><td>60</td><td></td></tr>
                    <tr><td>HAM</td><td>2.68</td><td>85</td><td>x</td></tr>
                    <tr><td>HAL</td><td>3.03</td><td>95</td><td>x</td></tr>
                    <tr><td>HB</td><td>2.32</td><td>60</td><td></td></tr>
                    <tr><td colspan=4 style='background-color:#CCC'>Einsteigerbahnen</td></tr>
                    <tr><td>Offen kurz</td><td>1.68</td><td>30</td><td></td></tr>
                    <tr><td>Offen mittel</td><td>1.85</td><td>45</td><td></td></tr>
                    <tr><td>Offen lang</td><td>2.32</td><td>60</td><td></td></tr>
                    <tr><td>sCOOL</td><td>1.21</td><td>30</td><td></td></tr>
                    <tr><td>Familie</td><td>1.68</td><td>30</td><td></td></tr>
                    <tr><td colspan=4 style='background-color:#CCC'>Testlaufbahnen</td></tr>
                    <tr><td>H16T</td><td>2.22</td><td>65</td><td>x</td></tr>
                    <tr><td>H20T</td><td>2.81</td><td>90</td><td>x</td></tr>
                    <tr><td>D16T</td><td>2.1</td><td>50</td><td>x</td></tr>
                    <tr><td>D20T</td><td>2.43</td><td>70</td><td>x</td></tr>
                </tbody>
            </table>
        </td>
    </tr>-->

<!--Rangliste
<tr><td colspan='2'><a name='rangliste'></a>

</td></tr>
-->
</table>
