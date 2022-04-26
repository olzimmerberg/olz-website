<?php

$event_title = "Nationales OL-Weekend Davos Klosters";
$event_date = "1. und 2. Oktober 2022";
$img_root = "/img/zol_2022/";
$pdf_root = "/pdf/zol_2022/";

echo <<<ZZZZZZZZZZ
<!DOCTYPE html>
<html lang="de">

<head>
	<title>{$event_title}</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="icon" href="./favicon.ico" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="styles.css"> 
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="" />
	<meta name="keywords" content="OL" />
</head>

<body>

	<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light shadow">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		  <span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item dropdown">
					<a class="nav-link" href="#news" role="button">
						News
					</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbar-dropdown-samstag" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						11. Nat. OL (Langdistanz)
					</a>
					<div class="dropdown-menu" aria-labelledby="navbar-dropdown-samstag">
						<a class="dropdown-item" href="#laufgebiet-samstag">Laufgebiet</a>
						<a class="dropdown-item" href="#ausschreibung-samstag">Ausschreibung</a>
						<a class="dropdown-item disabled" href="#anmeldung-samstag">Anmeldung</a>
						<a class="dropdown-item disabled" href="#weisungen-samstag">Weisungen</a>
						<a class="dropdown-item disabled" href="#streckendaten-samstag">Streckendaten</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbar-dropdown-sonntag" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						12. Nat. OL (Mitteldistanz)
					</a>
					<div class="dropdown-menu" aria-labelledby="navbar-dropdown-sonntag">
						<a class="dropdown-item" href="#laufgebiet-sonntag">Laufgebiet</a>
						<a class="dropdown-item" href="#ausschreibung-sonntag">Ausschreibung</a>
						<a class="dropdown-item disabled" href="#anmeldung-sonntag">Anmeldung</a>
						<a class="dropdown-item disabled" href="#weisungen-sonntag">Weisungen</a>
						<a class="dropdown-item disabled" href="#streckendaten-sonntag">Streckendaten</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link" href="https://woc2023.ch/wcf-2022/" role="button">
						World Cup Final 2022
					</a>
				</li>
			</ul>
		</div>
		<div class="logo-container">
			<a href="#">
				<img src="{$img_root}logo_100.png" alt="" class="logo-img">
			</a>
		</div>
	</nav>

	<!-- Header -->

	<div class="header-carousel carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}madrisa_aussicht_2.jpg" alt="Aussicht von Madrisa, mit Posten">
			</div>
			<div id="header-text">
				<h1>{$event_title}</h1>
				<h2>{$event_date}</h2>
			</div>
		</div>
	</div>

	<!-- Content -->

	<div class="content">
		<p></p>
		<div class="organisatoren row">
			<div class="col">
				<img src="{$img_root}organisator-olzimmerberg.png" alt="OL Zimmerberg"/>
			</div>
			<div class="col">
				<img src="{$img_root}organisator-olgdavos.png" alt="OLG Davos"/>
			</div>
		</div>
		<p></p>
		<p><i>Herzlich willkommen am OL-Weekend Klosters &ndash; Davos</i></p>
		<p>Zum Abschluss der ereignisreichen nationalen Saison 2022 bieten dir die OL Zimmerberg und die OLG Davos zwei weitere OL-Leckerbissen an. Im neuen OL-Gelände Madrisa, hoch über Klosters, organisieren wir am Samstag, 1.10. einen Langdistanz-OL. Der letzte nationale OL in diesem Jahr, ein Mitteldistanzlauf, findet dann am Sonntag, 2.10. im Drussatschawald bei Davos Wolfgang statt.</p>
		<p>Ebenfalls in Klosters und Davos trifft sich am gleichen Wochenende die OL Weltelite zum Weltcupfinale, welches als Hauptprobe für die WM 2023 in Flims-Laax-Falera gilt.</p>
		<p>Wir freuen uns auf ein faszinierendes OL-Weekend in den Bündner Bergen.</p>
		<p>Die Co-Laufleiter</p>
		<p>Martin Gross und Thomas Attinger</p>
	</div>

	<!-- News -->

	<div class="title-anchor">
		<span id="news"></span>
	</div>
	<h2 class="section-title shadow">
		News
	</h2>
	<div class="content">
			<ul>
			<li>26.4.2022: Homepage ist online</li>
			</ul>
	</div>


	<!-- Samstag -->

	<div class="title-anchor">
		<span id="samstag"></span>
	</div>
	<h2 class="section-title sticky-top shadow">
		<div class="first-line">
			Samstag, 1. Oktober 2022: 11. Nat. OL Madrisa (Langdistanz)
		</div>
		<div class="second-line">
			15. Zimmerberg OL, Lauf zählt für die Jugend-OL-Meisterschaft ZH/SH (JOM)
		</div>
	</h2>

	<div class="carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}madrisa_posten_2.jpg" alt="Posten Heidelbeer">
			</div>
		</div>
		<p>
		</p>
	</div>

	<!-- Laufgebiet Samstag-->

	<div class="content-anchor">
		<span id="laufgebiet-samstag"></span>
	</div>
	<h2 class="section-title shadow">
		Laufgebiet
	</h2>

	<div class="content">
		<p></p>
		<p>
Abwechslungsreiches Gelände mit Alpweiden, teils felsigen Partien und Blockfeldern<br>mehrheitlich gut bis sehr gut belaufbar; zwischen 1900-2300 m ü.M.		</p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="ausschreibung-samstag"></span>
	</div>
	<h2 class="section-title shadow">Ausschreibung (Stand 27.4.22)</h2>

	<div class="content">
		<p></p>

		<table class="info-table">
		<tr>
			<td>Auskunft/Laufleitung</td>
			<td>Martin Gross, m.gross@active.ch, 079 827 59 53</td>
		</tr>
		<tr>
			<td>Wettkampf</td>
			<td>Langdistanz mit Einzelstart, Distanzen gemäss WO</td>
		</tr>
		<tr>
			<td>Karte</td>
			<td>
				Madrisa, 1:10'000/1:7'500, Äquidistanz 5m, Stand Sommer 2022<br>
				Kartografie: Flavio Poltera<br>
				1:7'500 und grössere Postenbeschreibung ab D/H 50<p>
				(Versuchsbewilligung 2021/2022 Swiss Orienteering und Unterstützung OL-Gönnerclub)<br>
				1:10'000 auch für D/HE und D/H 18-20: Ausnahmebewilligung!
			</td>
		</tr>
		<tr>
			<td>Kategorien</td>
			<td>
				Alle nach WO plus D80/H85 (Versuchsbewilligung 2022).
				Zudem Offen kurz (OK), Offen Mittel (OM), Offen Lang (OL) und sCOOL
			</td>
		</tr>
		<tr>
			<td>Wettkampfzentrum</td>
			<td>
			Madrisa, Bergstation Klosters-Madrisa-Bahn
			Einfache, gedeckte Garderobe. Keine Duschen.
			</td>
		</tr>
		<tr>
			<td>OL-Weekend</td>
			<td>Am 2. Oktober 2022 findet der 12. Nationale OL (Mitteldistanz) in Davos statt, ebenso organisiert durch OL Zimmerberg/OLG Davos</td>
		</tr>
		<tr>
			<td>Programm Weltcupfinal</td>
			<td>Anschliessend an den 11. Nationalen OL findet auf Madrisa eine Weltcup-Staffel statt.</td>
		</tr>
		</table>

		<p></p>
	</div>

	<!-- Anmeldung Samstag-->

	<div class="content-anchor">
		<span id="anmeldung-samstag"></span>
	</div>
	<h2 class="section-title shadow">
		Anmeldung
	</h2>

	<div class="content">
		<p></p>
		<p>
<i>Noch nicht verfügbar.</i></p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="weisungen-samstag"></span>
	</div>
	<h2 class="section-title shadow">Weisungen</h2>

	<div class="content">
		<p></p>
		<p>
			<i>Noch nicht verfügbar.</i>
		</p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="streckendaten-samstag"></span>
	</div>
	<h2 class="section-title shadow">Streckendaten</h2>

	<div class="content">
		<p></p>
		<p>
			<i>Noch nicht verfügbar.</i>
		</p>
		<p></p>
	</div>

	<!-- Sonntag -->

	<div class="title-anchor">
		<span id="sonntag"></span>
	</div>
	<h2 class="section-title-day section-title sticky-top shadow">
		<div class="first-line">
			Sonntag, 2. Oktober 2022: 12. Nat. OL Davos (Mitteldistanz)
		</div>
		<div class="second-line">
			16. Zimmerberg OL, 29. Davoser OL
		</div>
	</h2>

	<div class="carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}halboffen.jpg" alt="Posten Halboffen">
			</div>
		</div>
		<p>
			<i>Bild noch von der Flumsi.</i>
		</p>
	</div>

	<!-- Laufgebiet Sonntag-->

	<div class="content-anchor">
		<span id="laufgebiet-sonntag"></span>
	</div>
	<h2 class="section-title shadow">
		Laufgebiet
	</h2>

	<div class="content">
		<p></p>
		<p>
Abwechslungsreiches Gelände mit Wald, Alpweiden und teils felsigen Partien; mehrheitlich gut belaufbar; zwischen 1600-1800 m.ü.M		</p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="ausschreibung-sonntag"></span>
	</div>
	<h2 class="section-title shadow">Ausschreibung</h2>

	<div class="content">
		<p></p>

		<table class="info-table">
		<tr>
			<td>Auskunft/Laufleitung</td>
			<td>Martin Gross, m.gross@active.ch, 079 827 59 53</td>
		</tr>
		<tr>
			<td>Wettkampf</td>
			<td>Mitteldistanz, Distanzen gemäss WO</td>
		</tr>
		<tr>
			<td>Karte</td>
			<td>
				Drussetscha, 1:10'000/1:7'500, Äquidistanz 5m, Stand Sommer 2022<p>
				Kartografie: Urs Steiner<p>
				1:7'500 und grössere Postenbeschreibung ab D/H 50 (Versuchsbewilligung 2021/2022 Swiss Orienteering und Unterstützung OL-Gönnerklub)
			</td>
		</tr>
		<tr>
			<td>Kategorien</td>
			<td>
				Alle nach WO plus D80/H85 (Versuchsbewilligung 2022).
				Zudem Offen kurz (OK), Offen Mittel (OM), Offen Lang (OL) und sCOOL
			</td>
		</tr>
		<tr>
			<td>Wettkampfzentrum</td>
			<td>
				Höhwald, Davos Wolfgang<p>
				Garderobe in Klubzelten. Keine Duschen.
			</td>
		</tr>
		<tr>
			<td>OL-Weekend</td>
			<td>
				Am 1. Oktober 2022 findet der 11. Nationale OL (Langdistanz) in Klosters statt, ebenso organisiert durch OL Zimmerberg/OLG Davos
			</td>
		</tr>
		<tr>
			<td>Programm Weltcupfinal</td>
			<td>Anschliessend an den Nationalen OL findet am gleichen Ort ein Weltcup-Mitteldistanz-Lauf statt.</td>
		</tr>
		</table>
		
		<p></p>
	</div>

	<!-- Anmeldung Sonntag-->

	<div class="content-anchor">
		<span id="anmeldung-sonntag"></span>
	</div>
	<h2 class="section-title shadow">
		Anmeldung
	</h2>

	<div class="content">
		<p></p>
		<p>
<i>Noch nicht verfügbar.</i></p>
		<p></p>
	</div>
	
	<div class="content-anchor">
		<span id="weisungen-sonntag"></span>
	</div>
	<h2 class="section-title shadow">
		Weisungen
	</h2>

	<div class="content">
		<p></p>
		<p>
			<i>Noch nicht verfügbar.</i>
		</p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="streckendaten-sonntag"></span>
	</div>
	<h2 class="section-title shadow">Streckendaten</h2>

	<div class="content">
		<p></p>
		<p>
			<i>Noch nicht verfügbar.</i>
		</p>
		<p></p>
	</div>

	<!-- Sponsoren -->

	<div class="title-anchor">
		<span id="sponsoren"></span>
	</div>
	<h2 class="section-title">
		Sponsoren
	</h2>

	<div class="content sponsors">
		<div class="row">
			<div class="col">
				<img src="{$img_root}sponsor-davosklosters.jpg" alt="Davos Klosters"/>
			</div>
			<div class="col">
				<img src="{$img_root}sponsor-swisslos.png" alt="Swisslos"/>
			</div>
			<div class="col">
				<img src="{$img_root}sponsor-migros.png" alt="Migros"/>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<img src="{$img_root}sponsor-graubuenden.jpg" alt="Graubünden"/>
			</div>
			<div class="col">
				<img src="{$img_root}organisator-olgdavos.png" alt="OLG Davos"/>
			</div>
			<div class="col">
				<a href="https://senstech.ch/" target="_blank">
					<img src="{$img_root}sponsor-senstech.jpg" alt="Senstech"/>
				</a>
			</div>
		</div>
		<p class="sponsor-padding"></p>
	</div>
</body>
</html>
ZZZZZZZZZZ;
