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
<h2>6. Zimmerberg OL, Sonntag, 27. Oktober 2013</h2>
<h3 class='nobox'><b>Regionaler Lauf (*58) / Schlusslauf der Jugend-OL-Meisterschaft ZH/SH (JOM)</b></h3>
<!--<h3 style='margin-top:20px;'>Fundgegenstände</h3>
<table style='margin-top:20px;margin-bottom:20px;'>
	<tr>
		<td><ul>
		<li>- Weinrote Kinderschuhe, Gr. 30, Marke: Elefanten</li>
		<li>- Blaues, langärmliges Odlo-Shirt, Gr. 164</li>
		<li>- Grosses blaues Badetuch mit Flamingo-Muster</li>
		<li>- OL Socken, Gr. 36-40, 'OL-Tech'</li>
		<li>- Postenmäppchen</li>
		<li>- blaue Sponser-Wasserflasche</li><ul>

Bitte bei mir melden: Marlies Laager, Tel 044 725 88 09 zol@olzimmerberg.ch</td></tr></table>!-->
<a href='index.php?page=99&event=zol_131027'><h3 style='margin-top:30px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Live-Resultate</h3></a>
<a href='#Bahndaten'><h3 style='margin-top:10px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Bahndaten</h3></a>
<a href='http://www.o-l.ch/cgi-bin/results?type=start&year=2013&event=Zimmerberg+OL/JOM+Schlusslauf&kind=all' target='_blank'><h3 style='margin-top:10px;'><img src='icns/pfeil_ext.png' class='noborder' style='margin-right:8px;'>Startliste JOM-Kategorien</h3></a>
<h3 style='margin-top:30px;margin-bottom:10px;'>Ausschreibung</h3>
<table class='liste'>
	<tr>
		<td colspan="2"><a href="pdf/AusschreibungZimmerbergOL2013_2.pdf" class="linkpdf" target="_blank">Ausschreibung als PDF (30.9.2013)</a><!--<a href='#rangliste' class='linkint'>Rangliste</a>-->
		</td>
	</tr>
	<tr><td><b>Veranstalter</b></td><td>OL Zimmerberg</td></tr>
	<tr>
		<td><b>Laufleitung und Auskunft</b></td>
		<td>Frido Koch, Tel. 044 / 788 23 93<br>
			<script type="text/javascript">document.write(MailTo("zol", "olzimmerberg.ch", "Laufleitung", "6. Zimmerberg OL 2013"));
			</script>
		</td>
	</tr>
	<tr>
		<td><b>Laufform</b></td>
		<td>Normaler Orientierungslauf</td>
	</tr>
	<tr>
		<td><b>Bahnlegung/Kontrolle</b></td>
		<td>Pamela Hotz-Capeder / Markus Hotz</td>
	</tr>
	<tr>
		<td><b>Karte</b></td>
		<td>Landforst, 1:10‘000, neu überarbeitete Karte September 2013</td>
	</tr>
	<tr>
		<td><b>Anmeldung</b></td>
		<td>nur am Lauftag von 9.00 – 12.00 Uhr, 1. Start 9.30 Uhr</td>
	</tr>
	<tr>
		<td><b>Distanzen</b></td>
		<td>WKZ-Start 25 Min., Ziel-WKZ 15 Min.</td>
	</tr>
	<tr>
		<td><b>Besammlung</b></td>
		<td>Schulanlage Langweg, 8942 Oberrieden
			<div id="map_"><a href="http://map.search.ch/686043,236774" target="_blank" onclick="map('dummy',686043,236774);return false;" class="linkmap">Karte zeigen</a>
			</div>
			<div id='map' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div>
		</td>
	</tr>
	<tr>
		<td><b>ÖV</b></td>
		<td><b>Empfehlung: Anreise mit SBB/ZVV nach Oberrieden Dorf:</b><br>S24 ab HB Zürich x.02, x.32; S21 ab Thalwil x.05
			<p>
			Markiert ab den Bahnhöfen:<br>
			• Bahnhof Oberrieden Dorf, zu Fuss 5 Min<br>
			• Bahnhof Oberrieden (See), zu Fuss 20 Min.<br>
		</td>
	</tr>
	<tr>
		<td><b>Parkplätze</b></td>
		<td>Markiert ab Dorfeinfahrt Oberrieden / Seestrasse, es dürfen nur die zugewiesenen Parkplätze benutzt werden, Fussweg ins WKZ bis zu 15 Min.
		</td>
	</tr>
	<tr>
		<td><b>Kategorien</b></td>
		<td>Gemäss WO (ausser D/H 20)</td>
	</tr>
	<tr>
		<td><b>Einsteigerbahnen</b></td>
		<td>Offen kurz/Familien/sCOOL (kinderwagentauglich☺) sowie Offen mittel und Offen lang<br></td>
	</tr>
	<tr>
		<td><b>Start</b></td>
		<td>JOM-Kategorien: fixe Startliste Finallauf für alle LäuferInnen mit mehr als 5 Wertungen 2013 am Schluss der Startzeiten 11.30 und 12.30 h, spätestens 1 Woche vor dem Lauf auf der SOLV-Webseite publiziert.<br><a href='http://www.o-l.ch/cgi-bin/results?type=start&year=2013&event=Zimmerberg+OL/JOM+Schlusslauf&kind=all' target='_blank' class='linkext'>Startliste JOM-Kategorien</a><br><br>
			Weitere Kategorien: Startzeit nach Wahl, eingedruckte Bahnen für alle Kategorien, Abgabe der Postenbeschreibungen am Start.
		</td>
	</tr>
	<tr>
		<td><b>Startgeld</b></td>
		<td>Jahrgang 1992 und älter: Fr. 18.--<br>
			Jahrgang 1993 - 1996: Fr. 12.-- <br>
			Jahrgang 1997 und jünger: Fr. 10.-- <br>
			Kategorie offen: gemäss Jahrgang <br>
			Badge-Miete: Fr. 2.--<br>
			zusätzliche Karte: Fr. 3.--
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
		<td>Getränke am Ziel, attraktive Festwirtschaft im Laufzentrum zur Überbrückung der Wartezeit bis zur <b>Rangverkündigung der JOM 2013</b> ca. 14.00 Uhr.
		</td>
	</tr>
	<tr>
		<td><a name='Bahndaten'><b>Bahndaten</b></a><br>(Änderungen vorbehalten)</td>
		<td style='border:none;margin:0px;padding:0px;'>
			<table style='border-spacing:0;' border="1">
				<thead style='font-weight:bold;'>
					<tr>
						<td style='width:25%;background-color:#CCC'>Kategorie</td>
						<td style='width:25%;background-color:#CCC'>Länge (km)</td>
						<td style='width:25%;background-color:#CCC'>Steigung (m)</td>
						<td style='width:25%;background-color:#CCC'></td>
					</tr>
				</thead>
				<tbody class='bahndaten'>
					<tr><td>D10</td><td>2.1</td><td>25</td><td></td></tr>
					<tr><td>D12</td><td>3.0</td><td>30</td><td></td></tr>
					<tr><td>D14</td><td>3.8</td><td>70</td><td></td></tr>
					<tr><td>D16</td><td>5.4</td><td>120</td><td></td></tr>
					<tr><td>D18</td><td>6.8</td><td>250</td><td></td></tr>
					<tr><td>DAL</td><td>7.2</td><td>240</td><td></td></tr>
					<tr><td>DAK</td><td>4.4</td><td>95</td><td></td></tr>
					<tr><td>DB</td><td>3.7</td><td>60</td><td></td></tr>
					<tr><td>D35</td><td>5.8</td><td>210</td><td></td></tr>
					<tr><td>D40</td><td>5.8</td><td>210</td><td></td></tr>
					<tr><td>D45</td><td>5.0</td><td>110</td><td></td></tr>
					<tr><td>D50</td><td>5.0</td><td>110</td><td></td></tr>
					<tr><td>D55</td><td>4.4</td><td>95</td><td></td></tr>
					<tr><td>D60</td><td>4.4</td><td>95</td><td></td></tr>
					<tr><td>D65</td><td>4.2</td><td>95</td><td></td></tr>
					<tr><td>D70</td><td>3.8</td><td>70</td><td></td></tr>
					<tr><td>D75</td><td>3.8</td><td>70</td><td></td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
					<tr><td>H10</td><td>2.1</td><td>25</td><td></td></tr>
					<tr><td>H12</td><td>3.0</td><td>30</td><td></td></tr>
					<tr><td>H14</td><td>4.5</td><td>90</td><td></td></tr>
					<tr><td>H16</td><td>6.8</td><td>130</td><td></td></tr>
					<tr><td>H18</td><td>8.0</td><td>260</td><td></td></tr>
					<tr><td>HAL</td><td>10.0</td><td>290</td><td></td></tr>
					<tr><td>HAM</td><td>7.2</td><td>240</td><td></td></tr>
					<tr><td>HAK</td><td>5.0</td><td>110</td><td></td></tr>
					<tr><td>HB</td><td>5.8</td><td>120</td><td></td></tr>
					<tr><td>H35</td><td>8.4</td><td>270</td><td></td></tr>
					<tr><td>H40</td><td>8.4</td><td>270</td><td></td></tr>
					<tr><td>H45</td><td>7.2</td><td>240</td><td></td></tr>
					<tr><td>H50</td><td>7.2</td><td>240</td><td></td></tr>
					<tr><td>H55</td><td>5.8</td><td>210</td><td></td></tr>
					<tr><td>H60</td><td>5.8</td><td>210</td><td></td></tr>
					<tr><td>H65</td><td>5.1</td><td>150</td><td></td></tr>
					<tr><td>H70</td><td>4.2</td><td>95</td><td></td></tr>
					<tr><td>H75</td><td>3.8</td><td>70</td><td></td></tr>
					<tr><td>H80</td><td>3.8</td><td>70</td><td></td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
					<tr><td>OL</td><td>5.8</td><td>120</td><td></td></tr>
					<tr><td>OM</td><td>3.7</td><td>60</td><td></td></tr>
					<tr><td>OK</td><td>2.3</td><td>25</td><td></td></tr>
					<tr><td>sCOOL</td><td>2.3</td><td>25</td><td></td></tr>
				</tbody>
			</table>
		</td>
	</tr>

<!--Rangliste
<tr><td colspan='2'><a name='rangliste'></a>

</td></tr>
-->
</table>
