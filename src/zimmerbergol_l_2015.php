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
<a name='top'><h2>8. Zimmerberg OL, Sonntag, 10. Mai 2015</h2></a>
<p>Regionaler Lauf (*31) / Lauf der Jugend-OL-Meisterschaft ZH/SH (JOM)<br>zählt zum 15. Stadt-OL Cup</p>

<table style='border-spacing:1px;'><tr>
<!--<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;'><a href='#ausschreibung' class='linkint'>Ausschreibung</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;'><a href='#bahndaten' class='linkint'>Bahndaten</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;'><a href='#impressionen' class='linkint'>Impressionen</a></td>-->
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='http://www.o-l.ch/cgi-bin/results?rl_id=3373' class='linkext' target='_blank'>Resultate</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='#fundsachen' class='linkint'>Fundgegenstände</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='?page=4&id=663' class='linkint'>Fotos</a></td>
<td style='text-align:center;background-color:#D4E7CE;border:solid 1px #007521;padding:3px;font-weight:bold;width:25%'><a href='#fotool' class='linkint'>Foto-OL</a></td>
</tr></table>
<h3>
Liebe OL-Läuferinnen und OL-Läufer
</h3>
<p>Mit über 800 Teilnehmerinnen und Teilnehmer war der 8. Zimmerberg OL ein grosser Erfolg. Leider führte dies aber gegen Ende des Wettkampfes zu einem Engpass beim Start. Unser Drucker war schlicht überfordert. Wir danken allen betroffenen Teilnehmenden für ihre Geduld und hoffen, dass der 8. Zimmerberg OL trotzdem in guter Erinnerung bleibt.
</p>
<p>Martin Gross<br>
Präsident OL Zimmerberg</p>
<p>PS: Wer seine Karte im Ziel verdankenswerterweise zur Verfügung gestellt hat, erhält im Laufe dieser Woche eine neue Karte per Post zugestellt. Sollte die Karte bis Ende Woche nicht eingetroffen sein, meldet euch bitte unter zol@olzimmerberg.ch.
<!--FUNDGEGENSTÄNDE-->
<h3 style='margin-top:20px;' name='fundsachen'>Fundgegenstände</h3>
<table style='margin-top:20px;margin-bottom:20px;'>
	<tr>
		<td><ul>
<li>- Trainingsjacke OLG Stäfa mit Name "Ackeret"</li>
<li>- Schwarz gelbe OL Schuhe VJ Sport Bold Grösse 41</li>
<li>- Hellblaue Nike Air Icarus Laufschuhe Grösse 43</li>
<li>- Graues Jockey Unterleibchen Grösse M</li>
<li>- Blaue Trainerhosen Grösse M</li>

Bitte melden bei Thomas Attinger, zol@olzimmerberg.ch oder 044 725 44 02</td></tr></table>

<!--FUNDGEGENSTÄNDE-->
<h3 style='margin-top:20px;' name='fotool'>Foto-OL</h3>
<table style='margin-top:20px;margin-bottom:20px;'>
	<tr>
		<td><p>Auflösung:<br>1=R, 2=S, 3=Q, 4=Z, 5=A, 6=U, 7=V, 8=W, 9=Ü, 10=§, 11=&, 12=%, 13=T, 14=P, 15=G, 16=K, 17=I, 18=E, 19=F, 20=J, 21=H, 22=Ö, 23=D, 24=L, 25=X, 26=Y, 27=C, 28=N, 29=B, 30=M, 31=A, 32=O</p></td></tr></table>

<!--<a href='#top' name='impressionen'><h3 style='margin-top:10px;margin-bottom:10px;'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'>Impressionen</h3></a>-->

<?php
// Bilder als 'BildnameBildnr_thumb.jpg' (110x73) und 'BildnameBildnr_gross.jpg' (800x533) abspeichern

/*
echo "<table class='liste'>";
$groesse = 11;
$breite = 4;
$bild_name = "richterswil";
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
				$pfad_thumb = "img/".$bild_name.$bild_nr."_thumb.jpg";
				$pfad_img = "img/".$bild_name.$bild_nr."_gross.jpg";
				echo "<td id='galerietd".($i*$breite+$n+1)."'>";
				echo "<a href='".$pfad_galerie.$pfad_img."' class='lightview' rel='gallery[myset]'><img src='".$pfad_galerie.$pfad_thumb."' alt='' onerror='onimageloaderror(this)' id='".($foto_000)."'></a>";
				echo "</td>";
				}
			}
		if ($i >= $groesse) break;
		}
	echo "</tr></table>";
	}
*/
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
		<td colspan="2"><a href="pdf/AusschreibungZimmerbergOL2015.pdf" class="linkpdf" target="_blank">Ausschreibung als PDF (19.4.2015)</a>
		</td>
	</tr>
	<tr><td><b>Veranstalter</b></td><td>OL Zimmerberg</td></tr>
	<tr>
		<td><b>Laufleitung und Auskunft</b></td>
		<td>Thomas Attinger, Tel. 044 725 44 02<br>
			<script type="text/javascript">document.write(MailTo("zol", "olzimmerberg.ch", "Laufleitung", "8. Zimmerberg OL 2015"));
			</script>
		</td>
	</tr>
	<tr>
		<td><b>Laufform</b></td>
		<td>Stadt-OL, Sprint</td>
	</tr>
	<tr>
		<td><b>Karte</b></td>
		<td>Richterswil, 1:4'000, neue Karte Mai 2015</td>
	</tr>
 	<tr>
		<td><b>Bahnlegung/Kontrolle</b></td>
		<td>Julia Gross / Fabio Würmli</td>
	</tr>
	<tr>
		<td><b>Anmeldung</b></td>
		<td>nur am Lauftag von 9.00 – 12.00 Uhr, 1. Start 9.30 Uhr</td>
	</tr>
	<tr>
		<td><b>Distanzen</b></td>
		<td>WKZ-Start 10 Min., Ziel nähe WKZ</td>
	</tr>
	<tr>
		<td><b>Besammlung</b></td>
		<td>Schulanlage Boden, Göldistrasse 19, 8805 Richterswil
			<div id="map_"><a href="http://map.search.ch/695350,229350?z=512&poi=zug,haltestelle&b=high" target="_blank" onclick="map('dummy',695350,229350);return false;" class="linkmap">Karte zeigen</a>
			</div>
			<div id='map' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div>
		</td>
	</tr>
	<tr>
		<td><b>ÖV</b></td>
		<td>Bahnhof Burghalden, Richterswil; 5 Min. zum WKZ; markierter Weg ab Bahnhof darf nicht verlassen werden!</td>
	</tr>
	<tr>
		<td><b>Parkplätze</b></td>
		<td>Markiert ab Autobahnausfahrt Richterswil<br>
		Es dürfen nur die vorgegebenen PP benutzt werden: Fussweg ins WKZ 15 Min, markierter Weg darf nicht verlassen werden!.
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
		<td><b>Start</b></td>
		<td>Fliegender Start, eingedruckte Bahnen für alle Kategorien, Abgabe der Postenbeschreibung am Start.
		</td>
	</tr>
	<tr>
		<td><b>Max. Laufzeit</b></td>
		<td>90 Min., Zielschluss frühestens 14.00 Uhr oder 1,5 h nach letztem Start.
		</td>
	</tr>
	<tr>
		<td><b>Startgeld</b></td>
		<td>Jahrgang 1994 und älter: Fr. 18.--<br>
			Jahrgang 1995 - 1998: Fr. 12.-- <br>
			Jahrgang 1999 und jünger: Fr. 10.-- <br>
			Kategorie offen: gemäss Jahrgang <br>
			RichterswilerInnen: Kinder und Jugendliche 7.-- / Erwachsene 10.--<br>
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
		<td><b>Das Laufgebiet ist für den Verkehr freigegeben. Die Verkehrsregeln sind zu beachten! Gesperrte Strassen dürfen nur an den gekennzeichneten Stellen überquert werden!</b></td>
	</tr>
	<tr>
		<td><b>Disqualifikation</b></td>
		<td>Die Missachtung der Verkehrsregeln, das Betreten von oliv-grün (Privatgebiet) oder violett (Sperrgebiet) markiertem Gelände führen zur Disqualifikation.</td>
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
		<td><a href='#top' name='bahndaten'><img src='icons/up.gif' class='noborder' style='height:18px;padding-right:10px;'><b>Bahndaten</b></a><br>(Änderungen vorbehalten)</td>
		<td style='border:none;margin:0px;padding:0px;'>
			<table style='border-spacing:0;' border="1">
				<thead style='font-weight:bold;'>
					<tr>
						<td style='width:22%;background-color:#CCC'>Kategorie</td>
						<td style='width:22%;background-color:#CCC'>Länge (km)</td>
						<td style='width:22%;background-color:#CCC'>Steigung (m)</td>
						<td style='width:34%;background-color:#CCC'>Schwierigkeit<br>1=tief, 2=mittel, 3=hoch</td>
					</tr>
				</thead>
				<tbody class='bahndaten'>
					<tr><td>D10</td><td>1.2</td><td>25</td><td>1</td></tr>
					<tr><td>D12</td><td>1.3</td><td>30</td><td>1</td></tr>
					<tr><td>D14</td><td>1.7</td><td>35</td><td>2</td></tr>
					<tr><td>D16</td><td>1.9</td><td>60</td><td>3</td></tr>
					<tr><td>D18</td><td>2.2</td><td>65</td><td>3</td></tr>
					<tr><td>DAL</td><td>2.2</td><td>65</td><td>3</td></tr>
					<tr><td>DAM</td><td>2.0</td><td>55</td><td>3</td></tr>
					<tr><td>DAK</td><td>1.9</td><td>55</td><td>3</td></tr>
					<tr><td>DB</td><td>1.5</td><td>50</td><td>1</td></tr>
					<tr><td>D35</td><td>2.2</td><td>65</td><td>3</td></tr>
					<tr><td>D40</td><td>1.9</td><td>55</td><td>3</td></tr>
					<tr><td>D45</td><td>1.9</td><td>55</td><td>3</td></tr>
					<tr><td>D50</td><td>1.9</td><td>55</td><td>3</td></tr>
					<tr><td>D55</td><td>1.5</td><td>50</td><td>3</td></tr>
					<tr><td>D60</td><td>1.5</td><td>50</td><td>3</td></tr>
					<tr><td>D65</td><td>1.4</td><td>45</td><td>3</td></tr>
					<tr><td>D70</td><td>1.4</td><td>45</td><td>3</td></tr>
					<tr><td>D75</td><td>1.4</td><td>45</td><td>3</td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
					<tr><td>H10</td><td>1.2</td><td>25</td><td>1</td></tr>
					<tr><td>H12</td><td>1.3</td><td>30</td><td>1</td></tr>
					<tr><td>H14</td><td>1.9</td><td>40</td><td>2</td></tr>
					<tr><td>H16</td><td>2.0</td><td>65</td><td>3</td></tr>
					<tr><td>H18</td><td>2.5</td><td>80</td><td>3</td></tr>
					<tr><td>HAL</td><td>2.5</td><td>80</td><td>3</td></tr>
					<tr><td>HAM</td><td>2.3</td><td>65</td><td>3</td></tr>
					<tr><td>HAK</td><td>2.0</td><td>55</td><td>3</td></tr>
					<tr><td>HB</td><td>1.5</td><td>40</td><td>1</td></tr>
					<tr><td>H35</td><td>2.5</td><td>80</td><td>3</td></tr>
					<tr><td>H40</td><td>2.3</td><td>65</td><td>3</td></tr>
					<tr><td>H45</td><td>2.3</td><td>65</td><td>3</td></tr>
					<tr><td>H50</td><td>2.0</td><td>55</td><td>3</td></tr>
					<tr><td>H55</td><td>2.0</td><td>55</td><td>3</td></tr>
					<tr><td>H60</td><td>1.7</td><td>50</td><td>3</td></tr>
					<tr><td>H65</td><td>1.7</td><td>50</td><td>3</td></tr>
					<tr><td>H70</td><td>1.5</td><td>50</td><td>3</td></tr>
					<tr><td>H75</td><td>1.5</td><td>50</td><td>3</td></tr>
					<tr><td>H80</td><td>1.4</td><td>45</td><td>3</td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
					<tr><td>OL</td><td>2.3</td><td>65</td><td>3</td></tr>
					<tr><td>OM</td><td>1.7</td><td>50</td><td>3</td></tr>
					<tr><td>OK</td><td>1.5</td><td>40</td><td>1</td></tr>
					<tr><td>sCOOL</td><td>1.3</td><td>25</td><td>1</td></tr>
					<tr><td>Familie</td><td>1.2</td><td>25</td><td>1 (kinderwagentauglich)</td></tr>
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
