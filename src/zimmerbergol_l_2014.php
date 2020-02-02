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
<h2>7. Zimmerberg OL, Sonntag, 18. Mai 2014</h2>
<p>Regionaler Lauf (*25) / Lauf der Jugend-OL-Meisterschaft ZH/SH (JOM)<br>zählt zum 14. Stadt-OL Cup
</p>

<!--FUNDGEGENSTÄNDE-->
<h3 style='margin-top:20px;'>Fundgegenstände</h3>
<table style='margin-top:20px;margin-bottom:20px;'>
	<tr>
		<td><ul>
<li>- Goldener Badge</li>
<li>- 2 Kompasse, 1x für links , 1x für rechts</li>
<li>- Armbanduhr: Bronze-farbiger Rand, schwarzes Uhrenband</li>
<li>- Polar-Brustband</li>
<li>- Turnschuhe Oasics, grau/schwarz mit leuchtblauen Schuhbändeln, Grösse 38</li>

Bitte bei mir melden: Marlies Laager, zol@olzimmerberg.ch</td></tr></table>


<h3 style='margin-top:30px;margin-bottom:10px;'>Impressionen</h3>

<?php
echo "<table class='liste'>";
$groesse = 15;
$breite = 4;
$bild_name = "waedenswil";
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
?>



<!--LIVERESULTATE-->
<!--<a href='index.php?page=99&event=zol_131027'><h3 style='margin-top:30px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Live-Resultate</h3></a>-->

<!--BAHNDATEN-->
<a href='#Bahndaten'><h3 style='margin-top:10px;'><img src='icns/pfeil_gruen.png' class='noborder' style='margin-right:8px;'>Bahndaten</h3></a>

<!--STARTLISTE-->
<!--<a href='http://www.o-l.ch/cgi-bin/results?type=start&year=2013&event=Zimmerberg+OL/JOM+Schlusslauf&kind=all' target='_blank'><h3 style='margin-top:10px;'><img src='icns/pfeil_ext.png' class='noborder' style='margin-right:8px;'>Startliste JOM-Kategorien</h3></a>-->

<!--AUSSCHREIBUNG-->
<h3 style='margin-top:30px;margin-bottom:10px;'>Ausschreibung</h3>
<table class='liste'>
	<tr>
		<td colspan="2"><a href="pdf/AusschreibungZimmerbergOL2014.pdf" class="linkpdf" target="_blank">Ausschreibung als PDF (21.3.2014)</a>
		</td>
	</tr>
	<tr><td><b>Veranstalter</b></td><td>OL Zimmerberg</td></tr>
	<tr>
		<td><b>Laufleitung und Auskunft</b></td>
		<td>Marlies Laager, Tel. 044 725 88 09<br>
			<script type="text/javascript">document.write(MailTo("zol", "olzimmerberg.ch", "Laufleitung", "7. Zimmerberg OL 2014"));
			</script>
		</td>
	</tr>
	<tr>
		<td><b>Laufform</b></td>
		<td>Stadt-OL, Mitteldistanz</td>
	</tr>
 	<tr>
		<td><b>Bahnlegung/Kontrolle</b></td>
		<td>Jan Hug / Hansjörg Gasser</td>
	</tr>
	<tr>
		<td><b>Anmeldung</b></td>
		<td>nur am Lauftag von 9.00 – 12.00 Uhr, 1. Start 9.30 Uhr</td>
	</tr>
	<tr>
		<td><b>Distanzen</b></td>
		<td>WKZ-Start 20 Min., Ziel im WKZ</td>
	</tr>
	<tr>
		<td><b>Besammlung</b></td>
		<td>Schulanlage Eidmatt, Eidmattstrasse 15, 8820 Wädenswil
			<div id="map_"><a href="http://map.search.ch/693431,231531?z=512&poi=zug,haltestelle&b=high" target="_blank" onclick="map('dummy',693431,231531);return false;" class="linkmap">Karte zeigen</a>
			</div>
			<div id='map' style='display:none;width=100%;text-align:left;margin:0px;padding-top:4px;'></div>
		</td>
	</tr>
	<tr>
		<td><b>ÖV</b></td>
		<td>Markierter Weg ab Bahnhof, darf nicht verlassen werden! 7 Min.</td>
	</tr>
	<tr>
		<td><b>Parkplätze</b></td>
		<td>Markiert ab Zentrum: Parkhaus Migros, Oberdorfstrasse (kostenpflichtig: 2 Fr.<br>
		Es dürfen nur die vorgegebenen PP benutzt werden: Fussweg ins WKZ 8 Min, markierter Weg darf nicht verlassen werden!.
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
		<td>Jahrgang 1993 und älter: Fr. 18.--<br>
			Jahrgang 1994 - 1997: Fr. 12.-- <br>
			Jahrgang 1998 und jünger: Fr. 10.-- <br>
			Kategorie offen: gemäss Jahrgang <br>
			WädenswilerInnen: Kinder und Jugendliche 7.-- / Erwachsene 10.--
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
		<td><a name='Bahndaten'><b>Bahndaten</b></a><br>(Änderungen vorbehalten)</td>
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
					<tr><td>D10</td><td>1.9</td><td>20</td><td>17</td></tr>
					<tr><td>D12</td><td>2.5</td><td>20</td><td>18</td></tr>
					<tr><td>D14</td><td>2.8</td><td>35</td><td>18</td></tr>
					<tr><td>D16</td><td>3.7</td><td>45</td><td>22</td></tr>
					<tr><td>D18</td><td>4.1</td><td>45</td><td>27</td></tr>
					<tr><td>DAL</td><td>4.3</td><td>75</td><td>26</td></tr>
					<tr><td>DAK</td><td>3.0</td><td>40</td><td>20</td></tr>
					<tr><td>DB</td><td>3.0</td><td>40</td><td>21</td></tr>
					<tr><td>D35</td><td>4.3</td><td>55</td><td>25</td></tr>
					<tr><td>D40</td><td>4.3</td><td>55</td><td>25</td></tr>
					<tr><td>D45</td><td>3.9</td><td>45</td><td>24</td></tr>
					<tr><td>D50</td><td>3.7</td><td>45</td><td>24</td></tr>
					<tr><td>D55</td><td>3.3</td><td>45</td><td>21</td></tr>
					<tr><td>D60</td><td>3.3</td><td>45</td><td>21</td></tr>
					<tr><td>D65</td><td>3.0</td><td>45</td><td>21</td></tr>
					<tr><td>D70</td><td>2.9</td><td>25</td><td>20</td></tr>
					<tr><td>D75</td><td>2.9</td><td>25</td><td>20</td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
					<tr><td>H10</td><td>1.9</td><td>20</td><td>17</td></tr>
					<tr><td>H12</td><td>2.5</td><td>20</td><td>18</td></tr>
					<tr><td>H14</td><td>3.1</td><td>30</td><td>20</td></tr>
					<tr><td>H16</td><td>4.5</td><td>70</td><td>26</td></tr>
					<tr><td>H18</td><td>5.0</td><td>80</td><td>28</td></tr>
					<tr><td>HAL</td><td>5.4</td><td>100</td><td>29</td></tr>
					<tr><td>HAM</td><td>4.7</td><td>95</td><td>25</td></tr>
					<tr><td>HAK</td><td>3.6</td><td>45</td><td>24</td></tr>
					<tr><td>HB</td><td>3.4</td><td>45</td><td>23</td></tr>
					<tr><td>H35</td><td>4.7</td><td>95</td><td>25</td></tr>
					<tr><td>H40</td><td>4.7</td><td>95</td><td>25</td></tr>
					<tr><td>H45</td><td>4.5</td><td>75</td><td>26</td></tr>
					<tr><td>H50</td><td>4.5</td><td>75</td><td>26</td></tr>
					<tr><td>H55</td><td>4.3</td><td>55</td><td>26</td></tr>
					<tr><td>H60</td><td>4.3</td><td>55</td><td>25</td></tr>
					<tr><td>H65</td><td>3.9</td><td>45</td><td>24</td></tr>
					<tr><td>H70</td><td>3.3</td><td>45</td><td>21</td></tr>
					<tr><td>H75</td><td>3.0</td><td>40</td><td>20</td></tr>
					<tr><td>H80</td><td>2.9</td><td>25</td><td>20</td></tr>
					<tr><td colspan=4 style='background-color:#CCC'>&nbsp;</td></tr>
					<tr><td>OL</td><td>4.5</td><td>50</td><td>25</td></tr>
					<tr><td>OM</td><td>3.0</td><td>40</td><td>21</td></tr>
					<tr><td>OK</td><td>2.1</td><td>20</td><td>16</td></tr>
					<tr><td>sCOOL</td><td>1.8</td><td>20</td><td>15</td></tr>
					<tr><td>Familie</td><td>2.4</td><td>15</td><td>20</td></tr>
				</tbody>
			</table>
		</td>
	</tr>

<!--Rangliste
<tr><td colspan='2'><a name='rangliste'></a>

</td></tr>
-->
</table>
