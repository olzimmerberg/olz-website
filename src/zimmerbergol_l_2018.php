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
<h2>11. Zimmerberg OL, Sonntag, 27. Mai 2018</h2>
<h3 class='nobox'>Regionaler Lauf (*25) / Lauf der Jugend-OL-Meisterschaft ZH/SH / Stadt OL Cup 2018<br>Sprint-Staffel für alle -  Start 13.30 Uhr</h3>
<table style='border-spacing:1px;'>
	<tr>
		<td style='display:none;text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='?page=4&id=663' class='linkint'>Impressionen</a></td>
		<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%;'><a href='#ausschreibung' class='linkint'>Ausschreibung Zimmerberg-OL</a></td>
		<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%;'><a href='#ausschreibung_ss' class='linkint'>Ausschreibung Sprint-Staffel</a></td>
		<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%;'><a href='#bahndaten' class='linkint'>Bahndaten</a></td>
		<td style='display:none;text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%;'><a href='?page=4&id=808' class='linkint'>Galerie</a></td>
		<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='?page=99&event=zol_180527' class='linkint'>Resultate</a></td>
		<td style='display:none;text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='http://www.routegadget.ch/binperl/reitti.cgi?act=map&id=209&kieli=' target='_blank' class='linkext'>RouteGadget</a> </td>
		<td style='display:none;text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='#fundsachen' class='linkint'>Fundgegenstände</a></td>
		<td style='display:none;text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='#fotool' class='linkint'>Foto-OL</a></td>
	</tr>
</table>

<!--BILDER-->
<?php
// Bilder als 'BildnameBildnr_thumb.jpg' (110x73) und 'BildnameBildnr_gross.jpg' (800x533) abspeichern
$pfad_galerie = "img/zol_waedenswil_2018/" ;
$bild_name = "waedenswil";
$breite = 4;
$file_list = scandir($pfad_galerie);
$img_list = array();
foreach($file_list as $this_file){
	if(is_array(getimagesize($pfad_galerie.$this_file))) array_push($img_list,$this_file);
	}
$groesse = count($img_list)/2;

if($groesse > 0 ) {
	echo "<a href='#top' name='impressionen'><h3 style='margin-top:10px;margin-bottom:10px;'>Impressionen</h3></a>";
	echo "<table class='liste'>";
	$reihen = ($groesse - ($groesse%$breite))/$breite;
	if ($groesse%$breite != 0) $reihen = $reihen + 1;
	echo "<tbody id='galerieindex'>";
	for ($i=0;$i<$reihen;$i++)
		{echo "<tr class='thumbs'>";
		for ($n=0; $n<$breite; $n++)
			{$foto_000 = str_pad(($i*$breite+$n+1) ,3, '0', STR_PAD_LEFT);
			if (($i*$breite+$n+1) > $groesse)
				{echo "<td id='galerietd".($i*$breite+$n+1)."'>&nbsp;</td>";
				}
			else
				{
				$bild_nr = substr("0".($i*$breite+$n+1),-2);
				$pfad_thumb = $pfad_galerie.$bild_name.$bild_nr."_thumb.jpg";
				$pfad_img = $pfad_galerie.$bild_name.$bild_nr."_gross.jpg";
				echo "<td id='galerietd".($i*$breite+$n+1)."'>";
				echo "<a href='".$pfad_img."' class='lightview' rel='gallery[myset]'><img src='".$pfad_thumb."' alt='' onerror='onimageloaderror(this)' id='".($foto_000)."'></a>";
				echo "</td>";
				}
			}
		if ($i >= $groesse) break;
		}
	echo "</tr></table>";
}
?>

<!-- FUNDGEGENSTÄNDE -->
<!--<h3 style='margin-top:20px;'>Fundgegenstände</h3>
<table style='margin-top:20px;margin-bottom:20px;'>
	<tr>
		<td><a href='https://www.dropbox.com/sh/gdz3upcreo3p4kl/AABw10hQzE1MKLTjV-SPr3fMa?dl=0' class='linkext' target='_blank'>Fundsachen</a><p>
Bitte bei mir melden: Marlies Laager, Tel 044 725 88 09 zol@olzimmerberg.ch</td></tr></table>-->

<!-- LIVE-RESULTATE -->
<!--<a href='index.php?page=99&event=zol_170521'><h3 style='margin-top:30px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Live-Resultate</h3></a>-->

<!-- BAHNDATEN -->
<!--<a href='#Bahndaten'><h3 style='margin-top:10px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Bahndaten</h3></a>-->

<!-- AUSSCHREIBUNG -->
<!--<a href='http://www.o-l.ch/cgi-bin/results?type=start&year=2013&event=Zimmerberg+OL/JOM+Schlusslauf&kind=all' target='_blank'><h3 style='margin-top:10px;'><img src='icns/pfeil_ext.png' class='noborder' style='margin-right:8px;'>Startliste JOM-Kategorien</h3></a>-->
<a href='#top' name='ausschreibung'><h3 style='margin-top:30px;margin-bottom:10px;'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'>Ausschreibung Zimmerberg-OL</h3></a>
<a href="pdf/AusschreibungZimmerbergOL2018.pdf" class="linkpdf" target="_blank">Ausschreibung Zimmerberg-OL und Sprint-Staffel als PDF (Stand 14.3.18)</a><p>
<table class='liste'>
	<tr style='display:none;'>
		<td colspan="2"><a href="pdf/AusschreibungZimmerbergOL2017.pdf" class="linkpdf" target="_blank">Ausschreibung als PDF (Stand 11.5.17)</a>
		</td>
	</tr>
	<tr><td><b>Veranstalter</b></td><td>OL Zimmerberg</td></tr>
	<tr>
		<td><b>Laufleitung und Auskunft</b></td>
		<td>Marlies Laager, Tel. 044 725 88 09<br>
			<script type="text/javascript">document.write(MailTo("zol", "olzimmerberg.ch", "Laufleitung", "11. Zimmerberg OL 2018"));
			</script>
		</td>
	</tr>
	<tr>
		<td><b>Laufform</b></td>
		<td>Stadt-OL, Sprintdistanz</td>
	</tr>
	<tr>
		<td><b>Karte</b></td>
		<td>Wädenswil 1:4'000, Stand Mai 2018</td>
	</tr>
	<tr>
		<td><b>Bahnlegung/Kontrolle</b></td>
		<td>Marc Bitterli, Arlette Piguet / Hansjörg Gasser</td>
	</tr>
	<tr>
		<td><b>Anmeldung</b></td>
		<td>Nur am Lauftag von 9.00 – 12.00 Uhr, 1. Start 9.30 Uhr</td>
	</tr>
	<tr>
		<td><b>Wettkampfzentrum</b></td>
		<td>Schulanlage Eidmatt, Eidmattstrasse 15b, 8820 Wädenswil
			<div id="map_"><a href="http://map.search.ch/693396,231506" target="_blank" onclick="map('dummy',693396,231506);return false;" class="linkmap">Karte zeigen</a>
			</div>
			<div id='map' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div>
		</td>
	</tr>
	<tr>
		<td><b>Anreise</b></td>
		<td>Bitte ÖV benutzen! Wenig Parkplätze<br>
		<b>ÖV:</b> Markierter Weg ab Bahnhof, darf nicht verlassen werden! 8 Min. bis WKZ<br>
		<b>Auto:</b> Markiert ab Zentrum:<br>
		• Ab Autobahn: Parkhaus Migros, Oberdorfstr. (kostenpflichtig 3 Fr./Tag)<br>
		• Ab Seestrasse: Parkplatz Weinrebe (60 Rp./Std.)<br>
		• Es dürfen nur die vorgegebenen PP benutzt werden.<br>
		• Fussweg ins WKZ 8/10 Min., markierter Weg darf nicht verlassen werden!
		</td>
	</tr>
	<tr>
		<td><b>Kategorien</b></td>
		<td>Gemäss WO (ausser D/H 20)</td>
	</tr>
	<tr>
		<td><b>Einsteigerbahnen</b></td>
		<td>Offen kurz, mittel , lang / Familien /sCOOL<br></td>
	</tr>
	<tr>
		<td><b>Air+ SI Einheiten</b></td>
		<td>Posten für berührungsloses Stempeln freigeschaltet (Modus AIR+ aktiviert)<br></td>
	</tr>
	<tr>
		<td><b>Postenbeschreibungen</b></td>
		<td>Abgabe am Start.<br></td>
	</tr>
	<tr style='display:none;'>
		<td><b>Live-Resultate</b></td>
		<td>Live-Resultate können über das Internet auf olzimmerberg.ch oder im WKZ im lokalen WLAN abgefragt werden.<br></td>
	</tr>
	<tr>
		<td><b>Distanzen</b></td>
		<td>WKZ-Start 10 Min., Ziel im WKZ</td>
	</tr>
	<tr>
		<td><b>Start</b></td>
		<td>Fliegender Start, eingedruckte Bahnen für alle Kategorien<br>
		</td>
	</tr>
	<tr>
		<td><b>Max. Laufzeit</b></td>
		<td>60 Min., Zielschluss 13.30 Uhr oder 1 h nach letztem Start.
		</td>
	</tr>
	<tr>
		<td><b>Startgeld</b></td>
		<td>GÜNSTIGE Startgelder dank Gemeinde und Schule Wädenswil
			<p>
			1997 und älter: Fr. 18.--<br>
			1998 - 2001: Fr. 12.-- <br>
			2002 und jünger: Fr. 10.-- <br>
			Kategorie offen: gemäss Jahrgang <br>
			WädenswilerInnen: Kinder und Jugendliche 7.-- / Erwachsene 10.--<br>
			Badge-Miete: Fr. 2.--<br>
			zusätzliche Karte: Fr. 3.--
		</td>
	</tr>
	<tr>
		<td><b>Kinder</b></td>
		<td>Kinderhort (ab 2 Jahre) / Für Nachmittag anmelden: <script type="text/javascript">document.write(MailTo("heidi.gross", "gmx.ch", "Zimmerberg OL Kinderhort", "12. Zimmerberg OL 2018"));
			</script><br>
		Kinder-OL</td>
	</tr>
	<tr>
		<td><b>Verkehr</b></td>
		<td><b>Der grösste Teil des Laufgebiets ist für den Verkehr freigegeben. Die Verkehrsregeln sind zu beachten! Gesperrte Strassen dürfen nur an den gekennzeichneten Stellen überquert werden! (Keine Zeitneutralisierung)</b>
		</td>
	</tr>
	<tr style='display:none;'>
		<td><b>Altersheime</b></td>
		<td>Einige Posten sind im Bereich von Altersheimen. Beim Queren dieser Areale ist besondere Vorsicht geboten.
		</td>
	</tr>
	<tr>
		<td><b>Disqualifikation</b></td>
		<td>Die Missachtung der Verkehrsregeln und das Betreten von oliv-grün (Privatgebiet) und violett (Sperrgebiet) markiertem Gelände führen zur Disqualifikation.
		</td>
	</tr>
	<tr>
		<td><b>Versicherung</b></td>
		<td>Die Versicherung ist Sache der Teilnehmer. Der Veranstalter lehnt soweit gesetzlich zulässig, jede Haftung ab.
		</td>
	</tr>
	<tr>
		<td><b>Verpflegung</b></td>
		<td>Getränke am Ziel, attraktive Festwirtschaft im Laufzentrum
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
						<td style='width:25%;background-color:#CCC'>Posten</td>
					</tr>
				</thead>
				<tbody class='bahndaten'>
					<tr><td>D10</td><td>1.7</td><td>20</td><td>18</td></tr>
					<tr><td>D12</td><td>1.8</td><td>20</td><td>19</td></tr>
					<tr><td>D14</td><td>2.0</td><td>30</td><td>18</td></tr>
					<tr><td>D16</td><td>2.2</td><td>30</td><td>21</td></tr>
					<tr><td>D18</td><td>2.4</td><td>40</td><td>19</td></tr>
					<tr><td>D18K</td><td>2.3</td><td>40</td><td>23</td></tr>
					<tr><td>D35</td><td>2.4</td><td>40</td><td>19</td></tr>
					<tr><td>D40</td><td>2.1</td><td>40</td><td>19</td></tr>
					<tr><td>D45</td><td>2.1</td><td>40</td><td>19</td></tr>
					<tr><td>D50</td><td>1.9</td><td>20</td><td>17</td></tr>
					<tr><td>D55</td><td>1.9</td><td>20</td><td>17</td></tr>
					<tr><td>D60</td><td>1.5</td><td>20</td><td>15</td></tr>
					<tr><td>D65</td><td>1.5</td><td>20</td><td>15</td></tr>
					<tr><td>D70</td><td>1.3</td><td>20</td><td>15</td></tr>
					<tr><td>D75</td><td>1.3</td><td>20</td><td>15</td></tr>
					<tr><td>DAK</td><td>2.1</td><td>30</td><td>16</td></tr>
					<tr><td>DAM</td><td>2.4</td><td>40</td><td>19</td></tr>
					<tr><td>DAL</td><td>2.7</td><td>50</td><td>20</td></tr>
					<tr><td>DB</td><td>2.0</td><td>30</td><td>19</td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
					<tr><td>H10</td><td>1.7</td><td>20</td><td>18</td></tr>
					<tr><td>H12</td><td>2.0</td><td>30</td><td>22</td></tr>
					<tr><td>H14</td><td>2.3</td><td>30</td><td>21</td></tr>
					<tr><td>H16</td><td>2.6</td><td>60</td><td>22</td></tr>
					<tr><td>H18</td><td>2.8</td><td>60</td><td>20</td></tr>
					<tr><td>H18K</td><td>2.6</td><td>60</td><td>23</td></tr>
					<tr><td>H35</td><td>2.8</td><td>60</td><td>20</td></tr>
					<tr><td>H40</td><td>2.8</td><td>60</td><td>20</td></tr>
					<tr><td>H45</td><td>2.5</td><td>40</td><td>20</td></tr>
					<tr><td>H50</td><td>2.5</td><td>40</td><td>20</td></tr>
					<tr><td>H55</td><td>2.3</td><td>30</td><td>20</td></tr>
					<tr><td>H60</td><td>2.1</td><td>30</td><td>16</td></tr>
					<tr><td>H65</td><td>2.1</td><td>30</td><td>18</td></tr>
					<tr><td>H70</td><td>2.1</td><td>30</td><td>18</td></tr>
					<tr><td>H75</td><td>1.8</td><td>30</td><td>17</td></tr>
					<tr><td>H80</td><td>1.8</td><td>30</td><td>17</td></tr>
					<tr><td>HAK</td><td>2.1</td><td>30</td><td>18</td></tr>
					<tr><td>HAM</td><td>2.8</td><td>60</td><td>20</td></tr>
					<tr><td>HAL</td><td>3.3</td><td>60</td><td>20</td></tr>
					<tr><td>HB</td><td>2.1</td><td>30</td><td>19</td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>Einsteigerbahnen</td></tr>
					<tr><td>Offen kurz</td><td>1.5</td><td>20</td><td>16</td></tr>
					<tr><td>Offen mittel</td><td>2.1</td><td>30</td><td>17</td></tr>
					<tr><td>Offen lang</td><td>2.7</td><td>60</td><td>22</td></tr>
					<tr><td>sCOOL</td><td>1.3</td><td>20</td><td>16</td></tr>
					<tr><td>Familie</td><td>1.7</td><td>30</td><td>17</td></tr>
				</tbody>
			</table>
		</td>
	</tr>

<!--Rangliste
<tr><td colspan='2'><a name='rangliste'></a>

</td></tr>
-->
</table>
<a href='#top' name='ausschreibung_ss'><h3 style='margin-top:30px;margin-bottom:10px;'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'>Ausschreibung Sprint-Staffel</h3></a>
<table class='liste'>
	<tr>
		<td><b>Laufleitung und Auskunft</b></td>
		<td>Jan Hug<br>
			<script type="text/javascript">document.write(MailTo("jan.hug", "olzimmerberg.ch", "Laufleitung", "11. Zimmerberg OL 2018 - Sprint-Staffel"));
			</script>
		</td>
	</tr>
	<tr>
		<td><b>Bahnlegung</b></td>
		<td>Jan Hug, Moritz Oetiker<br></td>
	</tr>
	<tr>
		<td><b>Anmeldung</b></td>
		<td>Online über <a href='http://entry.picotiming.ch' target='_blank'>entry.picotiming.ch</a> bis am 21. Mai erwünscht.<br>
		Am Lauftag solange Startplätze<br></td>
	</tr>
		<tr>
		<td><b>Kategorien</b></td>
		<td>
		<table style='margin:0px;padding:0px;'>
		<thead><tr><td>Kategorie</td><td>Alter</td><td>Läufer</td><td>Beschränkung</td><td>Streckenlänge</td></tr></thead>
		<tr><td><b>SSA</b></td><td>offen</td><td>4 Läufer (DHHD)</td><td>Nur Vereinsteams</td><td>ca. 1.7 km</td></tr>
		<tr><td><b>SS16</b></td><td>bis 16 J.</td><td>4 Läufer (DHHD)</td><td>Nur Vereinsteams</td><td>ca. 1.5 km</td></tr>
		<tr><td><b>Offen</b></td><td>offen</td><td>3 Läufer, frei</td><td>Keine Beschränkung</td><td>ca. 1.5 km</td></tr>
		</table>
		SS = Sprint-Staffel, DHHD = Läufer-Reihenfolge: Dame/Herr/Herr/Dame</td>
	</tr>
	<tr>
		<td><b>Startgeld</b></td>
		<td>Mit Voranmeldung: 15.-- pro Team<br>
		Am Lauftag: 25.-- pro Team<br>
		Startgeld wird am Lauftag am Sprintstaffel-Desk bezahlt<br></td>
	</tr>
	<tr>
		<td><b>Startzeit</b></td>
		<td>Startzeit ca. 13.30 Uhr, genauere Infos in den Weisungen<br></td>
	</tr>
	<tr>
		<td><b>Startnummer</b></td>
		<td>Startnummer kann gegen Bezahlung des Startgeldes am Sprintstaffeldesk bezogen werden. Ohne Startnummer kein Start!<br></td>
	</tr>
	<tr>
		<td><b>Nachmeldungen</b></td>
		<td>Nachmeldungen können am Sprintstaffeldesk solange Startplätze vorhanden getätigt werden. Es wird auch eine Läuferbörse angeboten für Läufer, welche kein ganzes Team zusammenbringen. Nachmeldungen sind bis 12:30 möglich.<br></td>
	</tr>
	<tr>
		<td><b>Mutationen</b></td>
		<td>Möglich am Lauftag am Sprintstaffeldesk<br></td>
	</tr>
	<tr>
		<td><b>Massenstart</b></td>
		<td>SSA: 13:30<br>SS16: 13:35<br>Offen: 13:40<br></td>
	</tr>
	<tr>
		<td><b>Start und Übergabe im Zielraum</b></td>
		<td>Besammlung ab 13:15 Uhr. Beim Betreten des Start- und Übergaberaumes ist die SI-Card zu löschen und zu testen. Es gibt keinen Überlauf, aber einen Zuschauerposten für alle Bahnen – Schlussschlaufe 1 bis 2 min. Der einlaufende Läufer stempelt den Zielposten auf der Ziellinie und übergibt anschliessend seine Karte als Pfand an den nächsten Läufer. Dieser läuft los Richtung Startpunkt, wirft die Karte in den dafür vorgesehenen Behälter und behändigt seine Laufkarte selbständig ab der Kartenwand. Die Eigenverantwortung liegt beim Läufer, wer eine falsche Karte nimmt wird nicht klassiert.<br></td>
	</tr>
	<tr>
		<td><b>Massenstart für die nicht abgelösten Läufer</b></td>
		<td>Ca. 14:40 für alle Kategorien<br></td>
	</tr>
	<tr>
		<td><b>Rangliste</b></td>
		<td>Auf olzimmerberg.ch<br></td>
	</tr>
	<tr>
		<td><b>Weisungen</b></td>
		<td>Werden in der Vorwoche veröffentlicht<br></td>
	</tr>
	<tr>
		<td><b>Sicherheit</b></td>
		<td>Die Sicherheit für Passanten und Läufer hat höchste Priorität.<br>
		Wir appellieren an die Vernunft aller Teilnehmer, dass dieser Pionierlauf ohne grössere Zusammenstösse über die Runden geht.<br></td>
	</tr>

</table>
