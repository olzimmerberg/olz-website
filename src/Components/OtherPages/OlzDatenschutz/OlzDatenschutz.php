<?php

namespace Olz\Components\OtherPages\OlzDatenschutz;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;
use Olz\Entity\Role;

class OlzDatenschutz extends OlzComponent {
    public function getHtml($args = []): string {
        $entityManager = $this->dbUtils()->getEntityManager();
        $role_repo = $entityManager->getRepository(Role::class);
        $website_role = $role_repo->findOneBy(['username' => 'website']);
        $out = '';

        $out .= OlzHeader::render([
            'title' => "Datenschutz",
            'description' => "Die Datenschutzerklärung für die Website der OL Zimmerberg.",
        ]);
        $out .= "<div class='content-right'>
        <h2>Datenschutz-Verantwortliche</h2>
        <ul>";
        $website_assignees = $website_role->getUsers();
        foreach ($website_assignees as $website_assignee) {
            $out .= "<li>";
            $out .= OlzUserInfoCard::render(['user' => $website_assignee]);
            $out .= "</li>";
        }
        $out .= "</ul>
        </div>
        <div class='content-middle'>
            <h3>Grundsatz</h3>
            <p>Wir sammeln <b>keine</b> personenbezogenen Daten von <b>nicht eigeloggten</b> Nutzern.</p>
            <p>Von <b>eigeloggten</b> Nutzern sammeln wir nur die personenbezogenen Daten, die für den Orientierungslauf-Vereinsbetrieb oder die Funktionalität dieser Website <b>notwendig</b> sind.</p>
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
                <li>Wenn du ein neues Konto erstellst und wenn du dein Passwort zurücksetzen musst, verwenden wir Google reCaptcha v3 (<a href='https://policies.google.com/privacy?hl=de-CH' target='_blank'>Datenschutzerklärung</a>, <a href='https://policies.google.com/terms?hl=de-CH' target='_blank'>Nutzungsbedingungen</a>), um Spam zu vermeiden.</li>
            </ul>
            <p>Unser Hoster, <a href='https://www.hosttech.ch/webhosting/'>Hosttech</a> ist Mitglied bei swiss hosting, speichert also alle Daten in der Schweiz.</p>
            <h3>Cookies</h3>
            <p>Wenn du dich einloggst, muss aus technischen Gründen in deinem Browser ein Cookie gespeichert werden.</p>
            <p>Das Cookie enthält keine personenbezogenen Daten.</p>
            <h3>Auskunft</h3>
            <p>Um Auskunft über deine Daten zu erhalten kannst du dich an die Datenschutz-Verantwortlichen wenden.</p>
        </div>
        ";

        $out .= OlzFooter::render();
        return $out;
    }
}
