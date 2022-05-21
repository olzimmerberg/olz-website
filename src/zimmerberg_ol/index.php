<?php

$img_root = "/img/zol_2022/";

$css_path = "jsbuild/main.min.css";
$css_modified = is_file($css_path) ? filemtime($css_path) : 0;
$js_path = "jsbuild/main.min.js";
$js_modified = is_file($js_path) ? filemtime($js_path) : 0;

require_once __DIR__.'/translations.php';
$selected_lang = $_GET['lang'] === 'fr' ? 'fr' : 'de';
$_ = $translations[$selected_lang];

$lang_selection = array_map(
    function ($language) use ($selected_lang) {
        if ($language == $selected_lang) {
            return "<span class=\"lang selected\">{$language}</span>";
        }
        return "<a href=\"?lang={$language}\" class=\"lang\">{$language}</a>";
    },
    $languages
);
$lang_selection_html = implode(' | ', $lang_selection);

echo <<<ZZZZZZZZZZ
<!DOCTYPE html>
<html lang="{$selected_lang}">

<head>
	<title>{$event_title}</title>

	<link rel="icon" href="./favicon.ico" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="{$css_path}?modified={$css_modified}">
	<script type="text/javascript" src="{$js_path}?modified={$js_modified}"></script>

	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="" />
	<meta name="keywords" content="OL" />
</head>

<body class="olz-override-root">

	<nav class="navbar py-3 fixed-top navbar-expand-lg navbar-light bg-light shadow">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		  <span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item dropdown">
					<a class="nav-link" href="#news" role="button">
						{$_->news}
					</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbar-dropdown-samstag" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						{$_->nat11}
					</a>
					<div class="dropdown-menu" aria-labelledby="navbar-dropdown-samstag">
						<a class="dropdown-item" href="#laufgebiet-samstag">{$_->terrain}</a>
						<a class="dropdown-item" href="#ausschreibung-samstag">{$_->announcement}</a>
						<a class="dropdown-item disabled" href="#anmeldung-samstag">{$_->entry}</a>
						<a class="dropdown-item disabled" href="#weisungen-samstag">{$_->directives}</a>
						<a class="dropdown-item disabled" href="#streckendaten-samstag">{$_->course_data}</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbar-dropdown-sonntag" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						{$_->nat12}
					</a>
					<div class="dropdown-menu" aria-labelledby="navbar-dropdown-sonntag">
						<a class="dropdown-item" href="#laufgebiet-sonntag">{$_->terrain}</a>
						<a class="dropdown-item" href="#ausschreibung-sonntag">{$_->announcement}</a>
						<a class="dropdown-item disabled" href="#anmeldung-sonntag">{$_->entry}</a>
						<a class="dropdown-item disabled" href="#weisungen-sonntag">{$_->directives}</a>
						<a class="dropdown-item disabled" href="#streckendaten-sonntag">{$_->course_data}</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link" href="https://woc2023.ch/wcf-2022/" target="_blank" role="button">
						{$_->wcf2022}
					</a>
				</li>
			</ul>
			<div class="language-selection">
				{$lang_selection_html}
			</div>
		</div>
	</nav>
	<div class="logo-container">
		<a href="#">
			<img src="{$img_root}logo_260.png" alt="" class="logo-img">
		</a>
	</div>

	<!-- Header -->

	<div class="header-carousel carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}madrisa_aussicht_2.jpg" alt="Aussicht von Madrisa, mit Posten">
			</div>
			<div id="header-text">
				<h1>{$_->event_title}</h1>
				<h2>{$_->event_date}</h2>
			</div>
		</div>
	</div>

	<!-- Content -->

	<div class="content">
		<p></p>
		<p><i>{$_->intro1}</i></p>
		<p>{$_->intro2}</p>
		<p>{$_->intro3}</p>
		<p>{$_->intro4}</p>
		<p>{$_->intro5}</p>
		<p>{$_->intro6}</p>
	</div>

	<!-- News -->

	<div class="title-anchor">
		<span id="news"></span>
	</div>
	<h2 class="section-subtitle shadow">
		{$_->news}
	</h2>
	<div class="content">
		<ul class="news">
			<li>{$_->news1}</li>
		</ul>
	</div>


	<!-- Samstag -->

	<div class="title-anchor">
		<span id="samstag"></span>
	</div>
	<h2 class="section-title sticky-top shadow">
		<div class="first-line">
			{$_->nat11title}
		</div>
		<div class="second-line">
			{$_->nat11subtitle}
		</div>
	</h2>

	<div class="carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}madrisa_posten_3.jpg" alt="Posten Heidelbeer">
			</div>
		</div>
		<p>
		</p>
	</div>

	<!-- Laufgebiet Samstag-->

	<div class="content-anchor">
		<span id="laufgebiet-samstag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->terrain}</h2>

	<div class="content">
		<p></p>
		<p>{$_->terrain_sat_text}</p>
		<p></p>
		<div class="img row">
			<div class="col"><img src="{$img_root}madrisa_offen.jpg" alt=""></div>
			<div class="col"><img src="{$img_root}madrisa_alpin.jpg" alt=""></div>
		</div>
		<div class="img row">
			<div class="col"><img src="{$img_root}karte_madrisa_karst.jpg" alt=""></div>
			<div class="col"><img src="{$img_root}karte_madrisa_heide.jpg" alt=""></div>
			<div class="col"><img src="{$img_root}karte_madrisa_weide.jpg" alt=""></div>
		</div>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="ausschreibung-samstag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->announcement}</h2>

	<div class="content">
		<p></p>
		<p>{$_->announcement_pdf_sat}</p>
		<p></p>

		<table class="info-table">
		<tr>
			<td>{$_->organizer}</td>
			<td>{$_->organizer_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->event_lead}</td>
			<td>{$_->event_lead_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->event_lead_co}</td>
			<td>{$_->event_lead_co_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->course_setter}</td>
			<td>{$_->course_setter_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->judge}</td>
			<td>{$_->judge_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->information}</td>
			<td>{$_->information_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->competition}</td>
			<td>{$_->competition_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->map}</td>
			<td>{$_->map_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->classes}</td>
			<td>{$_->classes_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->event_centre}</td>
			<td>{$_->event_centre_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->access}</td>
			<td>{$_->access_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->route_to_start}</td>
			<td>{$_->route_to_start_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->entry_online}</td>
			<td>{$_->entry_online_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->fees}</td>
			<td>{$_->fees_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->deadline}</td>
			<td>{$_->deadline_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->mutations}</td>
			<td>{$_->mutations_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->start_times}</td>
			<td>{$_->start_times_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->start_order}</td>
			<td>{$_->start_order_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->start_lists}</td>
			<td>{$_->start_lists_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->evaluation}</td>
			<td>{$_->evaluation_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->anti_doping}</td>
			<td>{$_->anti_doping_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->insurance}</td>
			<td>{$_->insurance_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->daycare}</td>
			<td>{$_->daycare_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->childrens_o}</td>
			<td>{$_->childrens_o_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->food}</td>
			<td>{$_->food_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->weekend}</td>
			<td>{$_->weekend_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->program_wcf}</td>
			<td>{$_->program_wcf_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->housing}</td>
			<td>{$_->housing_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->special_remarks}</td>
			<td>{$_->special_remarks_sat_text}</td>
		</tr>
		<tr>
			<td>{$_->early_birds}</td>
			<td>{$_->early_birds_sat_text}</td>
		</tr>
		<!--
			<tr>
				<td>{$_->company}</td>
				<td>{$_->company_sat_text}</td>
			</tr>
		-->
		</table>

		<p></p>
	</div>

	<!-- Anmeldung Samstag-->

	<div class="content-anchor">
		<span id="anmeldung-samstag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->entry}</h2>

	<div class="content">
		<p></p>
		<p><i>{$_->not_yet_available}</i></p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="weisungen-samstag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->directives}</h2>

	<div class="content">
		<p></p>
		<p><i>{$_->not_yet_available}</i></p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="streckendaten-samstag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->course_data}</h2>

	<div class="content">
		<p></p>
		<p><i>{$_->not_yet_available}</i></p>
		<p></p>
	</div>

	<!-- Sonntag -->

	<div class="title-anchor">
		<span id="sonntag"></span>
	</div>
	<h2 class="section-title sticky-top shadow">
		<div class="first-line">
			{$_->nat12title}
		</div>
		<div class="second-line">
			{$_->nat12subtitle}
		</div>
	</h2>

	<div class="carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="{$img_root}halboffen.jpg" alt="Posten Halboffen">
			</div>
		</div>
		<p>
		</p>
	</div>

	<!-- Laufgebiet Sonntag-->

	<div class="content-anchor">
		<span id="laufgebiet-sonntag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->terrain}</h2>

	<div class="content">
		<p></p>
		<p>{$_->terrain_sun_text}</p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="ausschreibung-sonntag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->announcement}</h2>

	<div class="content">
		<p></p>
		<p>{$_->announcement_pdf_sun}</p>
		<p></p>

		<table class="info-table">
		<tr>
			<td>{$_->organizer}</td>
			<td>{$_->organizer_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->event_lead}</td>
			<td>{$_->event_lead_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->event_lead_co}</td>
			<td>{$_->event_lead_co_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->course_setter}</td>
			<td>{$_->course_setter_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->judge}</td>
			<td>{$_->judge_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->information}</td>
			<td>{$_->information_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->competition}</td>
			<td>{$_->competition_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->map}</td>
			<td>{$_->map_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->classes}</td>
			<td>{$_->classes_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->event_centre}</td>
			<td>{$_->event_centre_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->access}</td>
			<td>{$_->access_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->route_to_start}</td>
			<td>{$_->route_to_start_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->entry_online}</td>
			<td>{$_->entry_online_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->fees}</td>
			<td>{$_->fees_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->deadline}</td>
			<td>{$_->deadline_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->mutations}</td>
			<td>{$_->mutations_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->start_times}</td>
			<td>{$_->start_times_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->start_order}</td>
			<td>{$_->start_order_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->start_lists}</td>
			<td>{$_->start_lists_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->evaluation}</td>
			<td>{$_->evaluation_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->anti_doping}</td>
			<td>{$_->anti_doping_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->insurance}</td>
			<td>{$_->insurance_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->daycare}</td>
			<td>{$_->daycare_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->childrens_o}</td>
			<td>{$_->childrens_o_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->food}</td>
			<td>{$_->food_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->weekend}</td>
			<td>{$_->weekend_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->program_wcf}</td>
			<td>{$_->program_wcf_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->housing}</td>
			<td>{$_->housing_sun_text}</td>
		</tr>
		<tr>
			<td>{$_->special_remarks}</td>
			<td>{$_->special_remarks_sun_text}</td>
		</tr>
		</table>
		
		<p></p>
	</div>

	<!-- Anmeldung Sonntag-->

	<div class="content-anchor">
		<span id="anmeldung-sonntag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->entry}</h2>

	<div class="content">
		<p></p>
		<p><i>{$_->not_yet_available}</i></p>
		<p></p>
	</div>
	
	<div class="content-anchor">
		<span id="weisungen-sonntag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->directives}</h2>

	<div class="content">
		<p></p>
		<p><i>{$_->not_yet_available}</i></p>
		<p></p>
	</div>

	<div class="content-anchor">
		<span id="streckendaten-sonntag"></span>
	</div>
	<h2 class="section-subtitle shadow">{$_->course_data}</h2>

	<div class="content">
		<p></p>
		<p><i>{$_->not_yet_available}</i></p>
		<p></p>
	</div>

	<!-- Sponsoren -->

	<div class="title-anchor">
		<span id="sponsoren"></span>
	</div>
	<h2 class="section-subtitle">{$_->sponsors}</h2>

	<div class="content sponsors">
		<p><b>{$_->main_sponsor}</b></p>
		<div class="row">
			<div class="col">
				<img src="{$img_root}sponsor-egk.png" alt="EGK"/>
			</div>
			<div class="col"></div>
			<div class="col"></div>
		</div>
		<p><b>{$_->co_sponsors}</b></p>
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
				<img src="{$img_root}sponsor-emmi.png" alt="Emmi"/>
			</div>
			<div class="col">
				<img src="{$img_root}sponsor-focuswater.png" alt="Focus Water"/>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<a href="https://senstech.ch/" target="_blank">
					<img src="{$img_root}sponsor-senstech.jpg" alt="Senstech"/>
				</a>
			</div>
			<div class="col"></div>
			<div class="col"></div>
		</div>
		<p class="sponsor-padding"></p>
	</div>
</body>
</html>
ZZZZZZZZZZ;
