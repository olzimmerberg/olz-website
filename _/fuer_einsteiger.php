<?php

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;
use Olz\Entity\Role;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;

require_once __DIR__.'/config/init.php';

session_start_if_cookie_set();

require_once __DIR__.'/admin/olz_functions.php';

$env_utils = EnvUtils::fromEnv();
$code_href = $env_utils->getCodeHref();

$host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
$canonical_url = "https://{$host}{$code_href}fuer_einsteiger.php";

echo OlzHeader::render([
    'title' => "Für Einsteiger",
    'description' => "Das Wichtigste für Neulinge beim Orientierungslauf oder der OL Zimmerberg, dem OL-Sport-Verein am linken Zürichseeufer.",
    'canonical_url' => $canonical_url,
]);

echo "<style>
.olz-override-root .header-box {
    display: none;
}
</style>";

require_once __DIR__.'/config/paths.php';
require_once __DIR__.'/tickers.php';

$entityManager = DbUtils::fromEnv()->getEntityManager();
$role_repo = $entityManager->getRepository(Role::class);
$nachwuchs_role = $role_repo->findOneBy(['username' => 'nachwuchs-kontakt']);

$contact_information = "<div style='padding:0px 10px 0px 10px;'>";
$nachwuchs_assignees = $nachwuchs_role->getUsers();
foreach ($nachwuchs_assignees as $nachwuchs_assignee) {
    $contact_information .= OlzUserInfoCard::render(['user' => $nachwuchs_assignee]);
}
$contact_information .= "</div>";

$trainings_information = OlzEditableText::render(['olz_text_id' => 1]);

ob_start();
termine_ticker([
    "eintrag_laenge" => 80,
    "eintrag_anzahl" => 3,
    "titel" => "Unsere nächsten 3 Trainings. Komm vorbei!",
    "sql_where" => " AND typ LIKE '%training%'",
    "heute_highlight" => false,
]);
$next_three_trainings = ob_get_contents();
ob_end_clean();

$orientierungslauf_001 = get_tile('orientierungslauf_001');
$orientierungslauf_002 = get_tile('orientierungslauf_002');
$orientierungslauf_003 = get_tile('orientierungslauf_003');
$orientierungslauf_004 = get_tile('orientierungslauf_004');
$was_ist_ol_001 = get_tile('was_ist_ol_001', ['lightgallery' => 'off']);
$ol_zimmerberg_001 = get_tile('ol_zimmerberg_001');
$ol_zimmerberg_002 = get_tile('ol_zimmerberg_002');
$ol_zimmerberg_003 = get_tile('ol_zimmerberg_003');
$ol_zimmerberg_004 = get_tile('ol_zimmerberg_004');
$ol_zimmerberg_005 = get_tile('ol_zimmerberg_005');
$ol_zimmerberg_006 = get_tile('ol_zimmerberg_006');
$ol_zimmerberg_007 = get_tile('ol_zimmerberg_007');
$ol_zimmerberg_008 = get_tile('ol_zimmerberg_008');
$ol_zimmerberg_009 = get_tile('ol_zimmerberg_009');
$ol_zimmerberg_010 = get_tile('ol_zimmerberg_010');
$ol_zimmerberg_011 = get_tile('ol_zimmerberg_011');
$ol_zimmerberg_012 = get_tile('ol_zimmerberg_012');
$ol_zimmerberg_013 = get_tile('ol_zimmerberg_013');
$ol_zimmerberg_014 = get_tile('ol_zimmerberg_014');
$ol_zimmerberg_015 = get_tile('ol_zimmerberg_015');
$ol_zimmerberg_016 = get_tile('ol_zimmerberg_016');
$wie_anfangen_001 = get_tile('wie_anfangen_001');
$wie_anfangen_002 = get_tile('wie_anfangen_002');
$wie_anfangen_003 = get_tile('wie_anfangen_003');
$wie_anfangen_004 = get_tile('wie_anfangen_004');
$trainings_001 = get_tile('trainings_001');
$trainings_002 = get_tile('trainings_002');
$trainings_003 = get_tile('trainings_003');
$trainings_004 = get_tile('trainings_004');
$trainings_005 = get_tile('trainings_005');
$trainings_006 = get_tile('trainings_006');
$trainings_007 = get_tile('trainings_007');
$trainings_008 = get_tile('trainings_008');
$trainings_009 = get_tile('trainings_009');
$trainings_010 = get_tile('trainings_010');
$trainings_011 = get_tile('trainings_011');
$trainings_012 = get_tile('trainings_012');
$trainings_013 = get_tile('trainings_013');
$trainings_014 = get_tile('trainings_014');
$trainings_015 = get_tile('trainings_015');
$trainings_016 = get_tile('trainings_016');
$pack_die_chance_001 = get_tile('pack_die_chance_001');
$ansprechperson_001 = get_tile('ansprechperson_001');
$ansprechperson_002 = get_tile('ansprechperson_002');
$ansprechperson_003 = get_tile('ansprechperson_003');
$ansprechperson_004 = get_tile('ansprechperson_004');

echo <<<ZZZZZZZZZZ
<div class='content-full'>
<div class='fuer-einsteiger'>

<div class='intro'>
    <p class='slogan'>Du bist neu beim Orientierungslauf oder bei unserem Verein?</p>
    <p class='important'>Dann ist diese Seite genau für dich!</p>
</div>

<div class='clear-both'></div>
<table class='left pics grid-2'>
    <tr>
        <td>{$orientierungslauf_001}</td>
        <td>{$orientierungslauf_002}</td>
    </tr>
    <tr>
        <td>{$orientierungslauf_003}</td>
        <td>{$orientierungslauf_004}</td>
    </tr>
</table>
<div class='text'>
    <h1>Orientierungslauf (OL)</h1>
    <p class='slogan'>Wird dir das Joggen zu langweilig, die Strassenläufe zu eintönig, die Finnenbahn zu öde?</p>
    <p class='slogan'>Möchtest du die Wälder deiner Region besser kennenlernen, als das vielleicht beim Wandern oder auf dem Vita-Parcours der Fall ist?</p>
    <p class='slogan'>Suchst du eine Outdoor-Sportart, die dich technisch und läuferisch herausfordert?</p>
    <p class='important'>Dann ist OL vielleicht ein Sport für dich!</p>
</div>

<div class='clear-both'></div>
<div class='right pics'>
    <span class='lightgallery'>
        <a
            href='https://www.youtube.com/watch?v=JVL0vgcnM6c'
            rel='noopener noreferrer'
            data-src='https://www.youtube.com/watch?v=JVL0vgcnM6c'
        >
            {$was_ist_ol_001}
        </a>
    </span>
</div>
<div class='text'>
    <h1>Was ist OL?</h1>
    <p class='slogan'>OL ist Spass und Abenteuer in der Natur für alle Altersgruppen!</p>
    <p class='description'>Ausgerüstet mit Karte und Kompass hast du die Mission, möglichst schnell alle Posten (Kontrollpunkte) im Laufgebiet (Wald oder Stadt) zu finden.</p>
    <p class='description'>Wir haben dazu auch ein <a href='https://www.youtube.com/watch?v=JVL0vgcnM6c' rel='noopener noreferrer' target='_blank' class='linkext'>kurzes YouTube-Video</a> erstellt.</p>
    <p class='description'><a href='https://de.m.wikipedia.org/wiki/Orientierungslauf' rel='noopener noreferrer' target='_blank'>Orientierungslauf</a> ist ähnlich wie:</p>
    <ul class='description'>
        <li><a href='https://de.m.wikipedia.org/wiki/Traillauf' rel='noopener noreferrer' target='_blank'>Trailrunning</a>, aber mit Karte und Kompass</li>
        <li><a href='https://de.m.wikipedia.org/wiki/Geocaching' rel='noopener noreferrer' target='_blank'>Geocaching</a>, aber als Sportart</li>
        <li><a href='https://de.m.wikipedia.org/wiki/Foxtrail' rel='noopener noreferrer' target='_blank'>Foxtrail</a> und <a href='https://de.m.wikipedia.org/wiki/Schnitzeljagd' rel='noopener noreferrer' target='_blank'>Schnitzeljagd</a>, aber mit Zeitmessung und nur der Karte als Problemstellung</li>
    </ul>
</div>

<div class='clear-both'></div>
<table class='left pics grid-4'>
    <tr>
        <td>{$ol_zimmerberg_001}</td>
        <td>{$ol_zimmerberg_002}</td>
        <td>{$ol_zimmerberg_003}</td>
        <td>{$ol_zimmerberg_004}</td>
    </tr>
    <tr>
        <td>{$ol_zimmerberg_005}</td>
        <td>{$ol_zimmerberg_006}</td>
        <td>{$ol_zimmerberg_007}</td>
        <td>{$ol_zimmerberg_008}</td>
    </tr>
    <tr>
        <td>{$ol_zimmerberg_009}</td>
        <td>{$ol_zimmerberg_010}</td>
        <td>{$ol_zimmerberg_011}</td>
        <td>{$ol_zimmerberg_012}</td>
    </tr>
    <tr>
        <td>{$ol_zimmerberg_013}</td>
        <td>{$ol_zimmerberg_014}</td>
        <td>{$ol_zimmerberg_015}</td>
        <td>{$ol_zimmerberg_016}</td>
    </tr>
</table>
<div class='text'>
    <h1>OL Zimmerberg</h1>
    <p class='description'>Wir sind ein <b>Orientierungslauf-Sportverein</b> in der Region um den Zimmerberg am <b>linken Zürichseeufer</b> und im Sihltal.</p>
    <p class='description'>Unsere <b>Mitglieder</b> kommen aus Kilchberg, Rüschlikon, Thalwil, Oberrieden, Horgen, Au ZH, Wädenswil, Richterswil, Schönenberg, Hirzel, Langnau am Albis, Gattikon, Adliswil und nahe gelegenen Teilen der Stadt Zürich (Wollishofen, Enge, Leimbach, Friesenberg).</p>
</div>

<div class='clear-both'></div>
<table class='right pics grid-2'>
    <tr>
        <td>{$wie_anfangen_001}</td>
        <td>{$wie_anfangen_002}</td>
    </tr>
    <tr>
        <td>{$wie_anfangen_003}</td>
        <td>{$wie_anfangen_004}</td>
    </tr>
</table>
<div class='text'>
    <h1>Wie anfangen?</h1>
    <p class='slogan'>Du möchtest mal OL-Luft schnuppern?</p>
    <p class='description'>Am besten kommst du in eines unserer <b>Trainings</b> (zur Vorbereitung haben wir ein <a href='https://youtu.be/PjsDAQM1kxA' rel='noopener noreferrer' target='_blank' class='linkext'>Youtube Video</a> erstellt).</p>
    <p class='description'>Jährlich organisieren wir ein <b>OL-Lager</b> und ein <b>Tageslager</b> für Kinder und Jugendliche.</p>
    <p class='description'>Weitere Anlässe findest du bei den <a href='termine.php' onmouseover='olz.highlight_menu(event)' onmouseout='olz.unhighlight_menu(event)' class='linkint'>Terminen</a>.</p>
</div>

<div class='clear-both'></div>
<table class='left pics grid-4'>
    <tr>
        <td>{$trainings_001}</td>
        <td>{$trainings_002}</td>
        <td>{$trainings_003}</td>
        <td>{$trainings_004}</td>
    </tr>
    <tr>
        <td>{$trainings_005}</td>
        <td>{$trainings_006}</td>
        <td>{$trainings_007}</td>
        <td>{$trainings_008}</td>
    </tr>
    <tr>
        <td>{$trainings_009}</td>
        <td>{$trainings_010}</td>
        <td>{$trainings_011}</td>
        <td>{$trainings_012}</td>
    </tr>
    <tr>
        <td>{$trainings_013}</td>
        <td>{$trainings_014}</td>
        <td>{$trainings_015}</td>
        <td>{$trainings_016}</td>
    </tr>
</table>
<div class='text'>
    <h1>Trainings</h1>
    <p class='description'>{$trainings_information}</p>
</div>

<div class='clear-both'></div>
<div class='right pics'>
    {$pack_die_chance_001}
</div>
<div class='text'>
    <h1>Pack die Chance!</h1>
    <p class='slogan'>Komm an eines unserer speziellen <a href='/pdf/2022_schnuppertrainings.pdf' target='_blank' class='linkpdf'>Schnuppertrainings 2022</a>, oder einfach an ein normales Training:</p>
    {$next_three_trainings}
</div>

<div class='clear-both'></div>
<table class='left pics grid-2'>
    <tr>
        <td>{$ansprechperson_001}</td>
        <td>{$ansprechperson_002}</td>
    </tr>
    <tr>
        <td>{$ansprechperson_003}</td>
        <td>{$ansprechperson_004}</td>
    </tr>
</table>
<div class='text'>
    <h1>Ansprechperson</h1>
    <p class='slogan'>Hast du Fragen zum Training oder zu unserem OL-Klub?</p>
    {$contact_information}
    <p class='important'>Wir freuen uns, von dir zu hören!</p>
    <p class='description'>Tipp: Vielleicht findest du auch bei den <a href='fragen_und_antworten.php'>FAQs</a> eine Antwort auf deine Frage.</p>
</div>

</div>
</div>
ZZZZZZZZZZ;

function get_tile($img_name, $options = []) {
    global $data_href;
    $img = <<<ZZZZZZZZZZ
    <picture>
        <source
            srcset='
                {$data_href}img/fuer_einsteiger/thumb/{$img_name}@2x.jpg 2x,
                {$data_href}img/fuer_einsteiger/thumb/{$img_name}.jpg 1x
            '
            type='image/jpeg'
        >
        <img
            src='{$data_href}img/fuer_einsteiger/thumb/{$img_name}.jpg'
            alt=''
            class='tile'
        />
    </picture>
    ZZZZZZZZZZ;
    if ($options['lightgallery'] ?? '' == 'off') {
        return $img;
    }
    return <<<ZZZZZZZZZZ
    <span class='lightgallery'>
        <a 
            href='{$data_href}img/fuer_einsteiger/img/{$img_name}.jpg'
            data-src='{$data_href}img/fuer_einsteiger/img/{$img_name}.jpg'
            aria-label='Bild vergrössern'
        >
            {$img}
        </a>
    </span>
    ZZZZZZZZZZ;
}

echo OlzFooter::render();
