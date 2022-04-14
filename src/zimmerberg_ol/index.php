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
						13. Nat. OL (Mitteldistanz)
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
						14. Nat. OL (Langdistanz)
					</a>
					<div class="dropdown-menu" aria-labelledby="navbar-dropdown-sonntag">
						<a class="dropdown-item" href="#ausschreibung-sonntag">Ausschreibung</a>
						<a class="dropdown-item" href="#weisungen-sonntag">Weisungen</a>
						<!--
						<a class="dropdown-item" href="#streckendaten-sonntag">Streckendaten</a>
						-->
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link" href="#corona" role="button">
						Corona
					</a>
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
				<img class="d-block w-100" src="{$img_root}churfirsten.jpg" alt="Posten und Churfirsten">
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
		<p><i>Liebe OL-Läuferinnen und OL-Läufer,</i></p>
		<p>Die OLG St. Gallen/Appenzell und die OL Zimmerberg laden Euch zum nationalen OL-Wochenende am 2./3. Oktober 2021 am Flumserberg, hoch über dem Walensee, sehr herzlich ein. Das voralpine Gebiet wurde 2018 erstmals kartiert und bisher nur einmal für den jährlichen Wettkampf der Arge Alp im gleichen Jahr benützt. Das abwechslungsreiche Gelände und die erfahrenen Bahnleger versprechen interessante und anforderungsreiche Wettkämpfe. Wir wünschen Euch viel Erfolg beim präzisen Anlaufen der OL Posten und ein erinnerungswürdiges Wochenende in einer herrlichen voralpinen Natur.</p>
		<p><i>Chers coureurs d'orientation,</i></p>
		<p>Les clubs OLG St. Gallen/Appenzell et OL Zimmerberg vous invitent cordialement au week-end national de course d'orientation les 2/3 octobre 2021 à Flumserberg, au-dessus du Walensee. La zone préalpine a été cartographiée pour la première fois en 2018 et n'a jusqu'à présent été utilisée qu'une seule fois pour le concours annuel Arge Alp, la même année. Le terrain varié et l'expérience des traceurs promettent des compétitions intéressantes et passionnantes. Nous vous souhaitons bonne chance dans l'approche précise des postes de course d'orientation et un week-end mémorable dans une belle nature préalpine.</p>
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
		<p><i>9. September: <b>Die Weisungen sind online!</b></i></p>
		<p><i>21. September: <b>Weisungen Langdistanz-OL am 3.10.2021 mit definitiven Bahndaten</b></i></p>
		<p><b>Das Anmeldeportal go2ol ist wieder offen für die Offen-Kategorien (OK, OM, OL) bis 30.9. resp. 1.10.</b></p>
		<p><b>Achtung: Parkgebühr Tannenheim und Tannenbodenalp offiziell eingeführt (Tagestarif CHF 8)</b></p>
		<p><b>Attention: Introduction offizielle de la taxe de stationnement (CHF 8/jour)</b></p>
		<p><b>Camper-Parkplatz Flumserberg: </b>⁣<a href="https://heidiland.com/de/home/map-details/bikercamping-flumserberg-9e793cfa-0a4d-4bfd-ae0d-8b8e255e23a2.html" target="_blank">BikerCamping Flumserberg</a>. (Hier dürfen natürlich auch Nicht-Biker übernachten. 😊)<br /><i>Campen auf den normalen Parkplätzen ist nicht erlaubt.</i></p>
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
		<p>Abwechslungsreiches Gelände mit Alpweiden, Wald und teils felsigen Partien; mehrheitlich gut bis sehr gut belaufbar; zwischen 1400 und 2000 m.ü.M.; in höheren Lagen auch voralpiner Charakter.</p>
		<p><a href="{$img_root}flumserberg_2018_340x297_96dpi.png" target="_blank">Karte von 2018</a></p>
	</div>

	<!-- Samstag -->

	<div class="title-anchor">
		<span id="samstag"></span>
	</div>
	<h2 class="section-title navbar sticky-top shadow">
		Samstag, 2. Oktober 2021: 13. Nat. OL (Mitteldistanz)
	</h2>

	<div class="content carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}heidelbeer.jpg" alt="Posten Heidelbeer">
			</div>
		</div>
	</div>

	<div class="content-anchor">
		<span id="ausschreibung-samstag"></span>
	</div>
	<h3 class="content">Ausschreibung</h3>

	<div class="content">
		<p></p>
		<p>
			<a href="{$pdf_root}2021_07_28_Ausschreibung_OLZ_D.pdf" target="_blank">Update-Ausschreibung (D)</a> - 
			<a href="{$pdf_root}2021_07_28_Ausschreibung_OLZ_FR.pdf" target="_blank">Update-Annonce (F)</a>
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
			<a href="{$pdf_root}2021_09_09_Weisungen_OLZ_D.pdf" target="_blank">Weisungen (D)</a> - 
			<a href="{$pdf_root}2021_09_08_Weisungen_OLZ_F.pdf" target="_blank">Directives (F)</a>
		</p>
		<p><b>Offen-Kategorien</b> haben am Samstag 2.10. einen fliegenden Start, das heisst du kannst einfach zum Start gehen, wann du willst. Letzter Start: 15:00.</p>
		<p></p>
		<p>
			<a href="http://www.olgsga.ch/galerien/13-nationaler-ol-2021/" target="_blank">Fotos</a> - 
			<a href="https://www.youtube.com/watch?v=ySK3uRmAWYM" target="_blank">Video</a>
		</p>
		<p></p>
	</div>

	<!--
	<div class="content-anchor">
		<span id="streckendaten-samstag"></span>
	</div>
	<h3 class="content">Streckendaten</h3>

	<div class="content">
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
	</div>

	-->

	<!-- Sonntag -->

	<div class="title-anchor">
		<span id="sonntag"></span>
	</div>
	<h2 class="section-title navbar sticky-top shadow">
		Sonntag, 3. Oktober 2021: 14. Nat. OL (Langdistanz)
	</h2>

	<div class="content carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}halboffen.jpg" alt="Posten Halboffen">
			</div>
		</div>
	</div>

	<div class="content-anchor">
		<span id="ausschreibung-sonntag"></span>
	</div>
	<h3 class="content">Ausschreibung</h3>

	<div class="content">
		<p></p>
		<p>
			<a href="{$pdf_root}2021_08_09_Ausschreibung_SGA_D.pdf" target="_blank">Ausschreibung (D)</a> - 
			<a href="{$pdf_root}2021_08_09_Ausschreibung_SGA_FR.pdf" target="_blank">Annonce (F)</a>
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
			<a href="{$pdf_root}2021_09_21_Weisungen_SGA_D.pdf" target="_blank">Weisungen (D)</a> - 
			<a href="{$pdf_root}2021_09_21_Weisungen_SGA_F.pdf" target="_blank">Directives (F)</a>
		</p>
		<p><b>Offen-Kategorien</b> beziehen am Sonntag 3.10. die Startnummer zusammen mit der Startzeit am Informationsstand. Letzter Start: 12:30.</p>
		<p></p>
		<p><a href="http://www.olgsga.ch/galerien/14-nationaler-ol-2021/" target="_blank">Fotos</a></p>
		<p></p>
	</div>

	<!--

	<div class="content-anchor">
		<span id="streckendaten-sonntag"></span>
	</div>
	<h3 class="content">Streckendaten</h3>

	<div class="content">
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
	</div>

	-->

	<!-- Corona -->

	<div class="title-anchor">
		<span id="corona"></span>
	</div>
	<h2 class="section-title navbar sticky-top shadow">
		Corona
	</h2>

	<div class="content">
		<p></p>
		<p>Alle Teilnehmenden müssen mittels eines gültigen Covid-Zertifikats einen GGG Status (geimpft, getestet, genesen) vorweisen.</p>
		<p>Wir weisen ausdrücklich darauf hin, dass Personen mit Covid-Symptomen nicht nach Flumserberg anreisen sollen.</p>
		<p>Vor Ort bieten wir keine Testmöglichkeiten an.</p>
		<p><b>Ablauf Corona-Check bei der Ankunft</b></p>
		<ul>
			<li>Wir bitten alle, sich am Anreisetag mit einem gültigen Covid-Zertifikat (geimpft, getestet, genesen) und einer Identitätskarte / Pass / Fahrausweis beim Corona-Check bei der Talstation in Flumserberg-Tannenheim (Samstag) bzw. Flumserberg-Tannenbodenalp (Sonntag) zu melden. Kinder bis und mit der Kategorie D/H 16 (bis Jahrgang 2005) müssen den Ausweis, aber kein Covid-Zertifikat vorweisen.</li>
			<li>Wer nicht geimpft ist, muss mit einem gültigen Test anreisen: PCR Test nicht älter als 72h oder einem Antigentest durchgeführt durch eine autorisierte Person nicht älter als 48h. Der Selbsttest für Zuhause wird nicht akzeptiert.</li>
			<li>Nach dem Vorweisen des Zertifikates und dem Ausweis wird ein Armband abgegeben, welches auch für den Sonntag zum Start berechtigt.</li>
			<li>Auf den Gondelbahnen (SeaJet, BergJet) besteht Maskenpflicht, nicht aber auf den Sesselbahnen.</li>
			<li>Es muss mit Wartezeiten gerechnet werden.</li>
		</ul>
	</div>

	<!-- Kontakt -->
	<!--

	<div class="title-anchor">
		<span id="kontakt"></span>
	</div>
	<h2 class="section-title navbar sticky-top shadow">
		Kontakt
	</h2>

	<div class="content">
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
		<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem unde, at explicabo illo laudantium eligendi. Officia, sapiente? Ipsa nisi et maiores, exercitationem, eaque reiciendis vero voluptatibus mollitia maxime, facilis reprehenderit!</p>
	</div>
	-->

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
				<img src="{$img_root}sponsor-bergbahnen.png" alt="Bergbahnen Flumserberg"/>
			</div>
			<div class="col">
				<img src="{$img_root}sponsor-sport-toto-fonds.jpg" alt="Sport Toto Fonds"/>
			</div>
			<div class="col">
				<img src="{$img_root}sponsor-migros.png" alt="Migros"/>
			</div>
		</div>
		<p class="sponsor-padding"></p>
		<div class="sponsor row">
			<div class="col">
				<img src="{$img_root}sponsor-robotron.png" alt="Robotron"/>
			</div>
			<div class="col">
				<img src="{$img_root}sponsor-flumroc.jpg" alt="Flumroc" style="width: 70%;"/>
			</div>
			<div class="col">
				<a href="https://senstech.ch/" target="_blank">
					<img src="{$img_root}sponsor-senstech.jpg" alt="Senstech"/>
				</a>
			</div>
		</div>
		<p class="sponsor-padding"></p>
		<div class="sponsor row">
			<div class="col">
				<img src="{$img_root}sponsor-quarten.jpg" alt="Quarten"/>
			</div>
			<div class="col">
				<img src="{$img_root}sponsor-molseralp.png" alt="Molseralp"/>
			</div>
		</div>
		<p class="sponsor-padding"></p>
	</div>
</body>
</html>
ZZZZZZZZZZ;
