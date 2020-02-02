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
<a name='top'><h2>9. Zimmerberg OL, Sonntag, 10. April 2016</h2></a>
<p>Regionaler Lauf (*15) / Lauf der Jugend-OL-Meisterschaft ZH/SH (JOM)</p>

<table style='border-spacing:1px;'><tr>
<!--
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;widht:20%;'><a href='#ausschreibung' class='linkint'>Ausschreibung</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;widht:20%;'><a href='#bahndaten' class='linkint'>Bahndaten</a></td>
-->
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;widht:25%;'><a href='?page=4&id=808' class='linkint'>Galerie</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='?page=99&event=zol_160410' class='linkint'>Resultate</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='http://www.routegadget.ch/binperl/reitti.cgi?act=viimeiset5&eventid=145' tartet='_blank'  target='_blank' class='linkext'>RouteGadget</a> </td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='#fundsachen' class='linkint'>Fundgegenstände</a></td>
<!--
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='?page=4&id=663' class='linkint'>Fotos</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='#fotool' class='linkint'>Foto-OL</a></td>
-->
</tr></table>

<!--FUNDGEGENSTÄNDE-->

<h3 style='margin-top:20px;' name='fundsachen'>Fundgegenstände</h3>
<table style='margin-top:20px;margin-bottom:20px;'>
	<tr>
		<td><ul>
<li>- Kapreolo-Sack mit Schuhen etc. von Roman Wüst, sCOOL</li>
<li>- Kompass mit Aufschrift Coralie (Coralie Waldner, OL Pfäffikon, D10?)</li>
<li>- OL-Schuhe: Gr. 35, schwarz, orange Sohle und Bändel</li>
<li>- OL-Stulpen-Socken: Gr. S, blau, Aufgestickt FCB</li>
<li>- ¾ OL-Hose ‚noname‘, Gr. 152, schwarz, inkl. Unterhose, Gr. 152 grau</li>
<li>- Trainerjacke: Icebreaker, Woman S, Schwarz (Aubergine), mit pinker Kapuze, Schmuck in der Tasche</li>
<li>- Wolljacke: Bella-Natur, schwarz mit Blumen (grau/rot), Gr. M</li>
<li>- Blaue Jeans mit Gummizug , Gr. 152, H12/D12</li>
<li>- Gestrickte Mütze, hellgrün mit Täschli, grauer Streifen</li>
<li>- Breites Stirnband Odlo, schwarz, (Gr. 152?)</li>
<li>- Blaues Badetuch, aufgestickt ‚orienteering‘</li>
<li>- Schwarze Bionic- Männer-Unterhose, kurze Beine, Gr. L/XL</li>
<li>- Schwarze Sloggi Männer-Unterhose, Grösse EU 6</li>
<li>- Schwarze Männer Boxershorts, gross</li>
Bitte melden bei Marlies Laager, zol@olzimmerberg.ch</td></tr></table>

<!--BILDER-->

<a href='#top' name='impressionen'><h3 style='margin-top:10px;margin-bottom:10px;'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'>Impressionen</h3></a>

<?php
// Bilder als 'BildnameBildnr_thumb.jpg' (110x73) und 'BildnameBildnr_gross.jpg' (800x533) abspeichern


echo "<table class='liste'>";
$groesse = 12;
$breite = 4;
$bild_name = "Buchenegg";
$pfad_galerie = "img/zol_buchenegg_2016/" ;
$reihen = ($groesse - ($groesse%$breite))/$breite;
if ($groesse%$breite != 0) $reihen = $reihen + 1;
echo "<tbody id='galerieindex'>";
if ($groesse != 0)
	{for ($i=0;$i<$reihen;$i++)
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


<!--LIVERESULTATE-->
<!--<a href='index.php?page=99&event=zol_150510' name='resultate'><h3 style='margin-top:30px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Live-Resultate</h3></a>-->

<!--<a href='#top' name='resultate'><h3 style='margin-top:10px;'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'>Live-Resultate</h3></a><p>noch keine Resultate verfügbar</p>-->

<!--STARTLISTE-->
<!--<a href='http://www.o-l.ch/cgi-bin/results?type=start&year=2013&event=Zimmerberg+OL/JOM+Schlusslauf&kind=all' target='_blank'><h3 style='margin-top:10px;'><img src='icns/pfeil_ext.png' class='noborder' style='margin-right:8px;'>Startliste JOM-Kategorien</h3></a>-->

<!--AUSSCHREIBUNG-->

<!--<a href='#top' name='ausschreibung'><h3 style='margin-top:10px;margin-bottom:10px;'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'>Ausschreibung</h3></a>
<table class='liste'>
	<tr>
		<td colspan="2"><a href="pdf/Ausschreibung_ZOL_2016.pdf" class="linkpdf" target="_blank">Ausschreibung als PDF (9.3.2016)</a>
		</td>
	</tr>
	<tr><td><b>Veranstalter</b></td><td>OL Zimmerberg</td></tr>
	<tr>
		<td><b>Laufleitung und Auskunft</b></td>
		<td>Marlies Laager, Tel. 044 725 88 09<br>
			<script type="text/javascript">document.write(MailTo("zol", "olzimmerberg.ch", "Laufleitung", "9. Zimmerberg OL 2016"));
			</script>
		</td>
	</tr>
	<tr>
		<td><b>Laufform</b></td>
		<td>Normaldistanz</td>
	</tr>
	<tr>
		<td><b>Karte</b></td>
		<td>Buchenegg, 1:10'000, Februar 2016<br>eingedruckte Bahnen</td>
	</tr>
 	<tr>
		<td><b>Bahnlegung/Kontrolle</b></td>
		<td>Priska Badertscher, Hansjörg Gasser / Urs Utzinger</td>
	</tr>
	<tr>
		<td><b>Besammlung</b></td>
		<td>Langnau am Albis, Gemeindesaal Schwerzi, In der Schwerzi 4
			<div id="map_"><a href="http://map.search.ch/682707,237965?z=512&poi=zug,haltestelle&b=high" target="_blank" onclick="map('dummy',682707,237965);return false;" class="linkmap">Karte zeigen</a>
			</div>
			<div id='map' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div>
		</td>
	</tr>
	<tr>
		<td><b>Anmeldung</b></td>
		<td>Am Lauftag von 9.00 – 13.00 Uhr, 1. Start 9.30 Uhr</td>
	</tr>
	<tr>
		<td><b>Besonderes</b></td>
		<td>Alle Teilnehmenden können gratis die neue SIAC ausprobieren. Das ist die neueste SI-Card vom berührungslosen SPORTident Air+-System. Sie reagiert auf eine Distanz von ca. 30 cm. Herkömmliche SI-Cards können wie gewohnt verwendet werden </td>
	</tr>
	<tr>
		<td><b>ÖV</b></td>
		<td>Möglichst mit ÖV anreisen!<br>ab Bahnhof Langnau-Gattikon:<br>Postauto 240 bis Schwerzi/Wildpark xx.16<br>Bus 140 bis Langnau am Albis, Altersheim xx.24, xx.54<br>zu Fuss 15 Min. (markiert)<br>ab Bahnhof Thalwil:<br>Postauto 240 xx.04, Bus 140 xx.13, xx.43</td>
	</tr>
	<tr>
		<td><b>Parkplätze</b></td>
		<td>Autos bitte voll besetzen! PP Wildpark Langenberg, kostenpflichtig 10 Fr./Tag (inkl. Gratiseintritt Wildpark), Fussweg zum WKZ: 5-10 Min.
		</td>
	</tr>
	<tr>
		<td><b>Kategorien</b></td>
		<td>Gemäss WO (ausser D/H 20)</td>
	</tr>
	<tr>
		<td><b>Einsteigerbahnen</b></td>
		<td>Offen kurz, mittel, lang / Familien / sCOOL<br></td>
	</tr>
	<tr>
		<td><b>Startgeld</b></td>
		<td>Jahrgang 1995 und älter: Fr. 18.--<br>
			Jahrgang 1996 - 1999: Fr. 12.-- <br>
			Jahrgang 2000 und jünger: Fr. 10.-- <br>
			Kategorie offen: gemäss Jahrgang <br>
			Badge-Miete: Fr. 2.--<br>
			zusätzliche Karte: Fr. 3.--<br>
			(KEIN erhöhtes Startgeld trotz Bustransport!)
		</td>
	</tr>
	<tr>
		<td><b>Start</b></td>
		<td>Freier Start, 9.30 bis 13.30
		</td>
	</tr>
	<tr>
		<td><b>Weg WKZ-Start/Ziel</b></td>
		<td>5 Min zu Fuss zum Bus. Bustransporte WKZ - Start/Ziel, 10 Min Fahrt.<br>Mit kurzen Wartezeiten muss gerechnet werden.<br>(Keine Dobb-Spikes im Bus erlaubt)<br>Bus-Start: 500m,  Ziel-Bus: 550 m</td>
	</tr>
	<tr>
		<td><b>Kleiderdepot</b></td>
		<td>Nähe Bus Aus-/Einladestation Buchenegg</td>
	</tr>
	<tr>
		<td><b>Zielschluss</b></td>
		<td>2 h nach letztem Start.
		</td>
	</tr>
	<tr>
		<td><b>Kinder</b></td>
		<td>Kinderhort (ab 2 Jahre)<br>Schnur-OL / Schulhaus-OL</td>
	</tr>
	<tr>
		<td><b>Versicherung</b></td>
		<td>Die Versicherung ist Sache der Teilnehmer. Der Veranstalter lehnt soweit gesetzlich zulässig, jede Haftung ab.
		</td>
	</tr>
	<tr>
		<td><b>Verpflegung</b></td>
		<td>Getränke am Ziel, Festwirtschaft im Laufzentrum</td>
	</tr>
	<tr>
		<td><b>Tipp!</b></td>
		<td>Besuch Wildpark Langenberg (gratis, gleich nebenan)</td>
	</tr>-->
<!--
<tr>
		<td><a href='#top' name='bahndaten'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'><b>Bahndaten</b></a><br>(Änderungen vorbehalten)</td>
		<td style='border:none;margin:0px;padding:0px;'>
			<table style='border-spacing:0;' border="1">
				<thead style='font-weight:bold;'>
					<tr>
						<td style='width:22%;background-color:#CCC'>Kategorie</td>
						<td style='width:22%;background-color:#CCC'>Länge (km)</td>
						<td style='width:22%;background-color:#CCC'>Steigung (m)</td>
					</tr>
				</thead>
				<tbody class='bahndaten'>
<tr><td>D10</td><td>2,4</td><td>60</td></tr>
<tr><td>D12</td><td>3,1</td><td>110</td></tr>
<tr><td>D14</td><td>3,3</td><td>150</td></tr>
<tr><td>D16</td><td>4,2</td><td>150</td></tr>
<tr><td>D18</td><td>5,3</td><td>240</td></tr>
<tr><td>DAK</td><td>3,2</td><td>140</td></tr>
<tr><td>DAM</td><td>4,7</td><td>225</td></tr>
<tr><td>DAL</td><td>5,3</td><td>230</td></tr>
<tr><td>DB</td><td>3,3</td><td>165</td></tr>
<tr><td>D35</td><td>4,7</td><td>225</td></tr>
<tr><td>D40</td><td>4,7</td><td>225</td></tr>
<tr><td>D45</td><td>4,3</td><td>210</td></tr>
<tr><td>D50</td><td>4,3</td><td>210</td></tr>
<tr><td>D55</td><td>4,4</td><td>200</td></tr>
<tr><td>D60</td><td>3,2</td><td>140</td></tr>
<tr><td>D70</td><td>3,0</td><td>75</td></tr>
<tr><td>D75</td><td>3,0</td><td>75</td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
<tr><td>H10</td><td>2,4</td><td>60</td></tr>
<tr><td>H12</td><td>3,1</td><td>110</td></tr>
<tr><td>H14</td><td>3,8</td><td>160</td></tr>
<tr><td>H16</td><td>5,3</td><td>250</td></tr>
<tr><td>H18</td><td>6,6</td><td>320</td></tr>
<tr><td>HAK</td><td>4,4</td><td>200</td></tr>
<tr><td>HAM</td><td>6,2</td><td>305</td></tr>
<tr><td>HAL</td><td>7,7</td><td>380</td></tr>
<tr><td>HB</td><td>4,3</td><td>180</td></tr>
<tr><td>H35</td><td>6,4</td><td>310</td></tr>
<tr><td>H40</td><td>6,4</td><td>310</td></tr>
<tr><td>H45</td><td>6,2</td><td>305</td></tr>
<tr><td>H50</td><td>6,2</td><td>270</td></tr>
<tr><td>H55</td><td>5,3</td><td>230</td></tr>
<tr><td>H60</td><td>4,4</td><td>220</td></tr>
<tr><td>H65</td><td>4,4</td><td>220</td></tr>
<tr><td>H70</td><td>3,6</td><td>145</td></tr>
<tr><td>H75</td><td>3,2</td><td>100</td></tr>
<tr><td>H80</td><td>3,2</td><td>100</td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
<tr><td>OK</td><td>2,7</td><td>75</td></tr>
<tr><td>FAM</td><td>2,7</td><td>75</td></tr>
<tr><td>OM</td><td>3,3</td><td>165</td></tr>
<tr><td>OL</td><td>4,3</td><td>180</td></tr>
<tr><td>sCOOL</td><td>2,1</td><td>70</td></tr>
				</tbody>
			</table>
		</td>
	</tr>
-->

<!--Rangliste
<tr><td colspan='2'><a name='rangliste'></a>

</td></tr>
-->
</table>
