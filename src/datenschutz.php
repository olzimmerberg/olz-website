<?php

require_once __DIR__.'/config/init.php';
require_once __DIR__.'/admin/olz_functions.php';
require_once __DIR__.'/components/users/olz_user_info_card/olz_user_info_card.php';
require_once __DIR__.'/model/index.php';

require_once __DIR__.'/components/page/olz_header/olz_header.php';
echo olz_header([
    'title' => "Datenschutz",
    'description' => "Die Datenschutzerklärung für die Website der OL Zimmerberg.",
]);

$role_repo = $entityManager->getRepository(Role::class);
$webmaster_role = $role_repo->findOneBy(['username' => 'webmaster']);

echo "<div id='content_rechts'>
<h2>Datenschutz-Verantwortliche</h2>
<div>Unsere Webmaster:</div>
<ul>";
$webmaster_assignees = $webmaster_role->getUsers();
foreach ($webmaster_assignees as $webmaster_assignee) {
    echo "<li>";
    echo olz_user_info_card($webmaster_assignee);
    echo "</li>";
}
echo "</ul>
</div>
<div id='content_mitte'>
    <h3>Grundsatz</h3>
    <p>Wir sammeln <b>keine</b> personenbezogenen Daten von <b>nicht eigeloggten</b> Nutzern.</p>
    <p>Von <b>eigeloggten</b> Nutzern sammeln wir nur die personenbezogenen Daten, die für den Orientierungslauf-Vereinsbetrieb <b>notwendig</b> sind.</p>
    <h3>Zweck</h3>
    <p>Von <b>eigeloggten</b> Nutzern bearbeiten wir folgende Personendaten:</p>
    <ul class='bullet-list'>
        <li>Name und E-Mail-Adresse für den <b>Versand des Newsletters</b></li>
        <li>Benutzername und Passwort für das <b>OLZ-Login</b></li>
        <li>Name, Geschlecht, volle Adresse, Telefonnummer, E-Mail-Adresse und Geburtsdatum für die <b>Mitgliederliste</b></li>
        <li>Name, Geschlecht, Wohnort, Geburtsdatum, SI-Card Nummer, Telefonnummer und E-Mail-Adresse für die <b>Anmeldung für Anlässe</b></li>
    </ul>
    <h3>Speicherungsdauer</h3>
    <p>Wir speichern deine Daten, bis du sie löschst, dein Konto löschst, oder eine bestimmte Art Daten nicht mehr für mindestens einen der angegebenen Zwecke benötigt wird.</p>
    <h3>Weitergabe von Daten</h3>
    <p>Wir geben deine Daten nicht weiter, mit Ausnahme der folgenden Fälle:</p>
    <ul class='bullet-list'>
        <li>Wenn du Mitglied der OL Zimmerberg bist, werden wir deine Kontaktdaten (Mitgliederliste) zur <b>Ermöglichung der klubinternen Kommunikation</b> anderen Klubmitgliedern zur Verfügung stellen</li>
        <li>Wenn du Mitglied der OL Zimmerberg bist, werden wir deine Postadresse der Redaktion des HOLZ und gegebenenfalls der Druckerei zur Verfügung stellen, damit sie dir das <b>Klubheftli HOLZ zustellen</b> kann.</li>
        <li>Wenn du dich für einen <b>Anlass anmeldest</b>, werden wir die benötigten Daten dem Veranstalter übermitteln</li>
    </ul>
    <p>Unser Hoster, <a href='https://www.hoststar.ch/de'>Hoststar</a> speichert alle Daten in der Schweiz, Deutschland oder Österreich.</p>
    <h3>Cookies</h3>
    <p>Wenn du dich einloggst, muss aus technischen Gründen in deinem Browser ein Cookie gespeichert werden.</p>
    <p>Das Cookie enthält keine personenbezogenen Daten.</p>
    <h3>Auskunft</h3>
    <p>Um Auskunft über deine Daten zu erhalten kannst du dich an die Datenschutz-Verantwortlichen wenden.</p>
</div>
";

require_once __DIR__.'/components/page/olz_footer/olz_footer.php';
echo olz_footer();
