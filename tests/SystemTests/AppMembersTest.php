<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AppMembersTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testMembersImportExport(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-member-import.csv');
        assert($document_path);
        $this->sendKeys('.olz-members #import-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $member_tables = $browser->findElements(
                WebDriverBy::cssSelector('#member-table')
            );
            return count($member_tables) == 1;
        });
        $this->screenshot('app_members_imported');

        $this->assertSame('2000001', $this->getText('#member-table #row-0 .ident'));
        $this->assertSame('2000002', $this->getText('#member-table #row-1 .ident'));
        $this->assertSame('2000003', $this->getText('#member-table #row-2 .ident'));
        $this->assertSame('2000004', $this->getText('#member-table #row-3 .ident'));
        $this->assertSame('2000006', $this->getText('#member-table #row-4 .ident'));
        $this->assertSame('2000007', $this->getText('#member-table #row-5 .ident'));
        $this->assertSame('2000008', $this->getText('#member-table #row-6 .ident'));
        $this->assertSame('2000009', $this->getText('#member-table #row-7 .ident'));
        $this->assertSame('2000010', $this->getText('#member-table #row-8 .ident'));
        $this->assertSame('2000005', $this->getText('#member-table #row-9 .ident'));

        $this->assertSame('admin', $this->getText('#member-table #row-0 .username'));
        $this->assertSame('vorstand', $this->getText('#member-table #row-1 .username'));
        $this->assertSame('kartenverkauf', $this->getText('#member-table #row-2 .username'));
        $this->assertSame('ohne.konto', $this->getText('#member-table #row-3 .username'));
        $this->assertSame('parent', $this->getText('#member-table #row-4 .username'));
        $this->assertSame('one.child', $this->getText('#member-table #row-5 .username'));
        $this->assertSame('another.child', $this->getText('#member-table #row-6 .username'));
        $this->assertSame('elitelaeufer', $this->getText('#member-table #row-7 .username'));
        $this->assertSame('hackerman', $this->getText('#member-table #row-8 .username'));
        $this->assertSame('-', $this->getText('#member-table #row-9 .username'));

        $this->assertSame('Armin 😂 Admin 🤣', $this->getText('#member-table #row-0 .user-info'));
        $this->assertSame('Volker Vorstand', $this->getText('#member-table #row-1 .user-info'));
        $this->assertSame('Karen Karten', $this->getText('#member-table #row-2 .user-info'));
        $this->assertSame('-', $this->getText('#member-table #row-3 .user-info'));
        $this->assertSame('Eltern Teil', $this->getText('#member-table #row-4 .user-info'));
        $this->assertSame('➡️ ?', $this->getText('#member-table #row-5 .user-info'));
        $this->assertSame('➡️ ?', $this->getText('#member-table #row-6 .user-info'));
        $this->assertSame('-', $this->getText('#member-table #row-7 .user-info'));
        $this->assertSame('Hacker Man', $this->getText('#member-table #row-8 .user-info'));
        $this->assertSame('Be Nutzer', $this->getText('#member-table #row-9 .user-info'));

        $this->assertSame('♻️ Aktualisiert', $this->getText('#member-table #row-0 .status'));
        $this->assertSame('🟰 Unverändert', $this->getText('#member-table #row-1 .status'));
        $this->assertSame('🟰 Unverändert', $this->getText('#member-table #row-2 .status'));
        $this->assertSame('🟰 Unverändert', $this->getText('#member-table #row-3 .status'));
        $this->assertSame('🟰 Unverändert', $this->getText('#member-table #row-4 .status'));
        $this->assertSame('♻️ Aktualisiert', $this->getText('#member-table #row-5 .status'));
        $this->assertSame('✨ Eintritt', $this->getText('#member-table #row-6 .status'));
        $this->assertSame('♻️ Aktualisiert', $this->getText('#member-table #row-7 .status'));
        $this->assertSame('✨ Eintritt', $this->getText('#member-table #row-8 .status'));
        $this->assertSame('🚫 Austritt', $this->getText('#member-table #row-9 .status'));

        $this->assertSame('Nachname: "Admin" => "Admin 🤣", Vorname: "Armin" => "Armin 😂"', $this->getText('#member-table #row-0 .updates'));
        $this->assertSame('', $this->getText('#member-table #row-1 .updates'));
        $this->assertSame('Benutzer-Id: "kartenverkauf" => "karten"', $this->getText('#member-table #row-2 .updates'));
        $this->assertSame('', $this->getText('#member-table #row-3 .updates'));
        $this->assertSame('', $this->getText('#member-table #row-4 .updates'));
        $this->assertSame('', $this->getText('#member-table #row-5 .updates'));
        $this->assertSame('', $this->getText('#member-table #row-6 .updates'));
        $this->assertSame('', $this->getText('#member-table #row-7 .updates'));
        $this->assertSame('', $this->getText('#member-table #row-8 .updates'));
        $this->assertSame('', $this->getText('#member-table #row-9 .updates'));

        $this->click('#export-button');
        $download_link = $this->findBrowserElement('#csv-download');
        $csv_export_url = $download_link->getAttribute('href');
        $csv_export_content = file_get_contents("{$this->getTargetUrl()}{$csv_export_url}");
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Nachname,Vorname,Firma,Adresse,PLZ,Ort,"Telefon Privat","Telefon Mobil",Benutzer-Id,Anrede,Titel,Briefanrede,Adress-Zusatz,Land,Nationalität,"Telefon Geschäft",Fax,E-Mail,"E-Mail Alternativ",[Gruppen],Status,[Rolle],Eintritt,Mitgliedsjahre,Austritt,Zivilstand,Geschlecht,Geburtsdatum,Jahrgang,Alter,Bemerkungen,Firmen-Webseite,Rechnungsversand,"Nie mahnen",IBAN,BIC,Kontoinhaber,Mail-MV,"SOLV NR","Badge Nummer",Werbegrund,Geburtsjahr,[Id],"[Zuletzt geändert am]","[Zuletzt geändert von]"
            "Admin 🤣","Armin 😂",,,,,,,admin,Herr,,,,,,,,admin@staging.olzimmerberg.ch,,,E,Administrator,13.01.2006,14,,,,,,,,,E-Mail,Nein,,,,ja,,,,,2000001,"01.05.2020 12:34:56",Clubdesk-Benutzer
            Karten,Karen,,,,,,,karten,Frau,,,,,,,,karen@staging.olzimmerberg.ch,,,A,"Standard Benutzer",13.01.2006,14,,,,,,,,,E-Mail,Nein,,,,ja,,,,,2000003,"01.05.2020 12:34:56",Clubdesk-Benutzer

            ZZZZZZZZZZ, $csv_export_content);

        $this->logout();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/mitglieder/";
    }
}
