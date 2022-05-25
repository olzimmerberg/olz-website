<?php

// =============================================================================
// Informationsseite zum klubeigenen Material.
// =============================================================================

use Olz\Entity\Role;

?>

<h2>Leihmaterial</h2>
<b>Zur Durchführung eines Orientierungslaufes mit Zeitnahme und Auswertung</b>
<table>
<tr>
<td></td>
<td>Kommerzieller OL<br>(mit Startgeld)</td>
<td>Nichtkommerzieller OL</td>
<td>OLZ Mitglieder</td>
<td>Einzelne Schulklassen</td>
<td>Nachwuchskader</td>
</tr>
<tr>
<td colspan='6'><b>Zeitnahme (SportIdent)</b></td>
</tr>
<tr>
<td>1 Set SI-Material: 1 Koffer mit 20 SI-Einheiten, 1 Koffer mit komplettem Druckerset, Bedienungsanleitung. 1 Tasche mit 20 Badges.</td>
<td>100.-</td>
<td>50.-</td>
<td>25.-</td>
<td>25.-</td>
<td>25.-</td>
</tr>
<tr>
<td>2 Sets</td>
<td>150.-</td>
<td>90.-</td>
<td>40.-</td>
<td>40.-</td>
<td>40.-</td>
</tr>
<tr>
<td colspan='6'><b>Kompasse</b></td>
</tr>
<tr>
<td>Kompasse (pro Stück)</td>
<td>2.-</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
</tr>
<tr>
<td colspan='6'><b>Posten</b></td>
</tr>
<tr>
<td>Postenstangen mit Flagge und SI Halterung (pro Stück)</td>
<td>1.50</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
</tr>
<tr>
<td>Zementsteine und Steckhölzer für Stadt-OL</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
</tr>
<tr>
<td>Startband / Zielband (pro Stück)</td>
<td>5.-</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
<td>0.-</td>
</tr>
</table>
<div>Alle Preise: Miete pro Tag; Pro Woche werden 3 Tage berechnet</div>
<br>
<b>Kontakt für Bestellung, Abholung und Rückgabe des Materials:</b>
<?php

require_once __DIR__.'/components/users/olz_user_info_card/olz_user_info_card.php';

$role_repo = $entityManager->getRepository(Role::class);
$sportident_role = $role_repo->findOneBy(['username' => 'sportident']);

$sportident_assignees = $sportident_role->getUsers();
foreach ($sportident_assignees as $sportident_assignee) {
    echo olz_user_info_card($sportident_assignee);
}

?>
<div><b>Bezahlung: </b>Der geschuldete Betrag ist per ESR innerhalb von 30 Tagen zu bezahlen. Ein Einzahlungsschein dafür wird beim Abholen des Materials abgegeben. Die Zahlungsinformationen sind auch auf der <a href='service.php' class='linkint'>Service-Seite</a> zu finden.</div>
<div><b>Verlorenes, beschädigtes Material: </b>Für verlorenes oder beschädigtes Material kommt der Mieter vollumfänglich auf. Die Preise richten sich nach den offiziellen Preisen des Vertreters von SportIdent in der Schweiz. Das Material muss sauber zurückgebracht werden.</div>

<h2>Dienstleistungen</h2>
<div>Die OL Zimmerberg bietet für Schulen und Vereinen oder andere Gruppen geleitete OL-Kurse an.</div>
<div>Das Niveau wird der Gruppe angepasst und kann von Schnupperkursen bis zu einem Fortgeschrittenenkurs varieren.</div>
<div>Die Kurse werden halbtags oder ganztags durchgeführt.</div>
<br/>
<div><b>Package 1: Halbtages Kurs für eine Schulklasse von ca. 25 Personen, CHF 150.--, beinhaltet folgendes:</b></div>
<ul>
<li>Postenmaterial</li>
<li>Elektronisches Auswertungssystem (SI-Material)</li>
<li>Bei einem kl. Wettkampf: Auswertung des Wettkampfs</li>
<li>Kartenmaterial & Kompasse</li>
<li>Fachkundige Betreuung</li>
</ul>
<div>(Grössere Gruppen, Preis auf Anfrage)</div>
<br/>
<div><b>Package 2: Ganztages Kurs für eine Schulklasse von ca. 25 Personen, CHF 250.--, beinhaltet folgendes:</b></div>
<ul>
<li>Postenmaterial</li>
<li>Elektronisches Auswertungssystem (SI-Material)</li>
<li>Bei einem kl. Wettkampf: Auswertung des Wettkampfs</li>
<li>Kartenmaterial & Kompasse</li>
<li>Fachkundige Betreuung</li>
</ul>
<div>(Grössere Gruppen, Preis auf Anfrage)</div>

<h2>Kleider</h2>
<a href='/pdf/olz_kleider_2021.pdf' class='linkpdf'>Kleiderangebot</a>
