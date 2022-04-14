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
	<meta name="keywords" content="" />
</head>

<body>

	<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light shadow">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		  <span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item dropdown">
					<a class="nav-link" href="#laufgebiet" role="button">
						Laufgebiet
					</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbar-dropdown-samstag" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						11. Nat. OL (Langdistanz)
					</a>
					<div class="dropdown-menu" aria-labelledby="navbar-dropdown-samstag">
						<a class="dropdown-item" href="#ausschreibung-samstag">Ausschreibung</a>
						<a class="dropdown-item" href="#weisungen-samstag">Weisungen</a>
						<!--
						<a class="dropdown-item" href="#streckendaten-samstag">Streckendaten</a>
						-->
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbar-dropdown-sonntag" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						12. Nat. OL (Mitteldistanz)
					</a>
					<div class="dropdown-menu" aria-labelledby="navbar-dropdown-sonntag">
						<a class="dropdown-item" href="#ausschreibung-sonntag">Ausschreibung</a>
						<a class="dropdown-item" href="#weisungen-sonntag">Weisungen</a>
						<!--
						<a class="dropdown-item" href="#streckendaten-sonntag">Streckendaten</a>
						-->
					</div>
				</li>
				<!--
				<li class="nav-item dropdown">
					<a class="nav-link" href="#kontakt" role="button">
						Kontakt
					</a>
				</li>
				-->
			</ul>
		</div>
		<div class="logo-container">
			<img src="{$img_root}logo_100.png" alt="" class="logo-img">
		</div>
	</nav>

	<!-- Header -->

	<div class="content carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}madrisa_aussicht.jpg" alt="Aussicht von Madrisa, mit Posten">
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
		<p><i>Herzlich willkommen am OL-Weekend Klosters &ndash; Davos</i></p>
		<p>Zum Abschluss der ereignisreichen nationalen Saison 2022 bieten dir die OL Zimmerberg und die OLG Davos zwei weitere OL-Leckerbissen an. Im neuen OL-Gelände Madrisa, hoch über Klosters, organisieren wir am Samstag, 1.10. einen Langdistanz-OL. Der letzte nationale OL in diesem Jahr, ein Mitteldistanzlauf, findet dann am Sonntag, 2.10. im Drussatschawald bei Davos Wolfgang statt.</p>
		<p>Ebenfalls in Klosters und Davos trifft sich am gleichen Wochenende die OL Weltelite zum Weltcupfinale, welches als Hauptprobe für die WM 2023 in Flims-Laax gilt.</p>
		<p>Wir freuen uns auf ein faszinierendes OL-Weekend in den Bündner Bergen.</p>
		<p>Die Co-Laufleiter</p>
		<p>Martin Gross und Thomas Attinger</p>
	</div>

	<!-- News -->

	<div class="title-anchor">
		<span id="news"></span>
	</div>
	<h2 class="section-title navbar sticky-top shadow">
		News
	</h2>
	<div class="content">
		<p></p>
		<p>
			<i>Noch nicht verfügbar.</i>
		</p>
		<!--<p><i>9. September: <b>Die Weisungen sind online!</b></i></p>-->
		<p></p>
	</div>

	<!-- Laufgebiet -->

	<div class="title-anchor">
		<span id="laufgebiet"></span>
	</div>
	<h2 class="section-title navbar sticky-top shadow">
		Laufgebiet
	</h2>

	<div class="content">
		<p></p>
		<p>
			<i>Noch nicht verfügbar.</i>
		</p>
		<p></p>
	</div>

	<!-- Samstag -->

	<div class="title-anchor">
		<span id="samstag"></span>
	</div>
	<h2 class="section-title navbar sticky-top shadow">
		Samstag, 1. Oktober 2022: 11. Nat. OL (Langdistanz)
	</h2>

	<div class="content carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}heidelbeer.jpg" alt="Posten Heidelbeer">
			</div>
		</div>
		<p>
			<i>Bild noch von der Flumsi.</i>
		</p>
	</div>

	<div class="content-anchor">
		<span id="ausschreibung-samstag"></span>
	</div>
	<h3 class="content">Ausschreibung</h3>

	<div class="content">
		<p></p>
		<p>
			<i>Noch nicht verfügbar.</i>
		</p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="weisungen-samstag"></span>
	</div>
	<h3 class="content">Weisungen</h3>

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
	<h2 class="section-title navbar sticky-top shadow">
		Sonntag, 2. Oktober 2022: 12. Nat. OL (Mitteldistanz)
	</h2>

	<div class="content carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}halboffen.jpg" alt="Posten Halboffen">
			</div>
		</div>
		<p>
			<i>Bild noch von der Flumsi.</i>
		</p>
	</div>

	<div class="content-anchor">
		<span id="ausschreibung-sonntag"></span>
	</div>
	<h3 class="content">Ausschreibung</h3>

	<div class="content">
		<p></p>
		<p>
			<i>Noch nicht verfügbar.</i>
		</p>
		<p></p>
	</div>
	
	<div class="content-anchor">
		<span id="weisungen-sonntag"></span>
	</div>
	<h3 class="content">Weisungen</h3>

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
	<h2 class="section-title navbar sticky-top shadow">
		Sponsoren
	</h2>

	<div class="content">
		<p class="sponsor-padding"></p>
		<div class="sponsor row">
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
		<p class="sponsor-padding"></p>
		<div class="sponsor row">
			<div class="col">
				<img src="{$img_root}sponsor-graubuenden.jpg" alt="Graubünden"/>
			</div>
			<div class="col">
				<img src="{$img_root}sponsor-olgdavos.png" alt="OLG Davos"/>
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
