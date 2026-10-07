<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

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
        $this->login('admin', 'adm1n');
        $this->loadUrl($this->getUrl());

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-member-import.csv');
        assert($document_path);
        $this->sendKeys('.olz-members #import-upload input[type=file]', $document_path);
        $this->waitFor('#member-table');
        $this->waitFor('#member-table #row-12 .username');
        $crawler = $this->getCrawler();
        $this->screenshot('app_members_imported');

        $this->assertSame('admin', $crawler->filter('#member-table #row-0 .username')->text(''));
        $this->assertSame('vorstand', $crawler->filter('#member-table #row-1 .username')->text(''));
        $this->assertSame('kartenverkauf', $crawler->filter('#member-table #row-2 .username')->text(''));
        $this->assertSame('ohne.konto', $crawler->filter('#member-table #row-3 .username')->text(''));
        $this->assertSame('parent', $crawler->filter('#member-table #row-4 .username')->text(''));
        $this->assertSame('one.child', $crawler->filter('#member-table #row-5 .username')->text(''));
        $this->assertSame('another.child', $crawler->filter('#member-table #row-6 .username')->text(''));
        $this->assertSame('elitelaeufer', $crawler->filter('#member-table #row-7 .username')->text(''));
        $this->assertSame('hackerman', $crawler->filter('#member-table #row-8 .username')->text(''));
        $this->assertSame('⚠️ Nur Firma 1', $crawler->filter('#member-table #row-9 .username')->text(''));
        $this->assertSame('⚠️ Nur Firma 2', $crawler->filter('#member-table #row-10 .username')->text(''));
        $this->assertSame('⚠️ Ident: 2000013', $crawler->filter('#member-table #row-11 .username')->text(''));
        $this->assertSame('⚠️ Ident: 2000005', $crawler->filter('#member-table #row-12 .username')->text(''));

        $this->assertSame('Armin 😂 Admin 🤣', $this->filter('#member-table #row-0 .user-info')->getText());
        $this->assertSame('Volker Vorstand', $this->filter('#member-table #row-1 .user-info')->getText());
        $this->assertSame('Karen Karten', $this->filter('#member-table #row-2 .user-info')->getText());
        $this->assertSame('-', $this->filter('#member-table #row-3 .user-info')->getText());
        $this->assertSame('Eltern Teil', $this->filter('#member-table #row-4 .user-info')->getText());
        $this->assertSame('➡️  ?', $this->filter('#member-table #row-5 .user-info')->getText());
        $this->assertSame('➡️  ?', $this->filter('#member-table #row-6 .user-info')->getText());
        $this->assertSame('-', $this->filter('#member-table #row-7 .user-info')->getText());
        $this->assertSame('Hacker Man', $this->filter('#member-table #row-8 .user-info')->getText());
        $this->assertSame('-', $this->filter('#member-table #row-9 .user-info')->getText());
        $this->assertSame('-', $this->filter('#member-table #row-10 .user-info')->getText());
        $this->assertSame('-', $this->filter('#member-table #row-11 .user-info')->getText());
        $this->assertSame('Be Nutzer', $this->filter('#member-table #row-12 .user-info')->getText());

        $this->assertSame('♻️ Aktualisiert', $crawler->filter('#member-table #row-0 .status')->text(''));
        $this->assertSame('🟰 Unverändert', $crawler->filter('#member-table #row-1 .status')->text(''));
        $this->assertSame('🟰 Unverändert', $crawler->filter('#member-table #row-2 .status')->text(''));
        $this->assertSame('🟰 Unverändert', $crawler->filter('#member-table #row-3 .status')->text(''));
        $this->assertSame('🟰 Unverändert', $crawler->filter('#member-table #row-4 .status')->text(''));
        $this->assertSame('♻️ Aktualisiert', $crawler->filter('#member-table #row-5 .status')->text(''));
        $this->assertSame('✨ Eintritt', $crawler->filter('#member-table #row-6 .status')->text(''));
        $this->assertSame('♻️ Aktualisiert', $crawler->filter('#member-table #row-7 .status')->text(''));
        $this->assertSame('✨ Eintritt', $crawler->filter('#member-table #row-8 .status')->text(''));
        $this->assertSame('✨ Eintritt', $crawler->filter('#member-table #row-9 .status')->text(''));
        $this->assertSame('✨ Eintritt', $crawler->filter('#member-table #row-10 .status')->text(''));
        $this->assertSame('✨ Eintritt', $crawler->filter('#member-table #row-11 .status')->text(''));
        $this->assertSame('🚫 Austritt', $crawler->filter('#member-table #row-12 .status')->text(''));

        $this->assertSame(<<<'ZZZZZZZZZZ'
            Nachname: "Admin" ➡️ "Admin 🤣"
            Vorname: "Armin" ➡️ "Armin 😂"
            Adresse: "" ➡️ "Administratorweg 1234"
            PLZ: "" ➡️ "8134"
            Ort: "" ➡️ "Admiswil"
            ZZZZZZZZZZ, $this->filter('#member-table #row-0 .updates')->getText());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Adresse: "" ➡️ "Vorstandgasse 9"
            PLZ: "" ➡️ "5200"
            Ort: "" ➡️ "Vorstadt"
            ZZZZZZZZZZ, $this->filter('#member-table #row-1 .updates')->getText());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Benutzer-Id: "kartenverkauf" ➡️ "karten"
            ZZZZZZZZZZ, $this->filter('#member-table #row-2 .updates')->getText());
        $this->assertSame('', $crawler->filter('#member-table #row-3 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-4 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-5 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-6 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-7 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-8 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-9 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-10 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-11 .updates')->text(''));
        $this->assertSame('', $crawler->filter('#member-table #row-12 .updates')->text(''));

        $this->click('#export-button');
        $this->getClient()->waitFor('#csv-download');
        $csv_export_url = $this->filter('#csv-download')->getAttribute('href');
        $csv_export_content = file_get_contents("{$this->getTargetUrl()}{$csv_export_url}");
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Nachname,Vorname,Firma,Adresse,PLZ,Ort,"Telefon Privat","Telefon Mobil",Benutzer-Id,Anrede,Titel,Briefanrede,Adress-Zusatz,Land,Nationalität,"Telefon Geschäft",Fax,E-Mail,"E-Mail Alternativ",[Gruppen],Status,[Rolle],Eintritt,Mitgliedsjahre,Austritt,Zivilstand,Geschlecht,Geburtsdatum,Jahrgang,Alter,Bemerkungen,Firmen-Webseite,Rechnungsversand,"Nie mahnen",IBAN,BIC,Kontoinhaber,Mail-MV,"SOLV NR","Badge Nummer",Werbegrund,Geburtsjahr,[Id],"[Zuletzt geändert am]","[Zuletzt geändert von]"
            "Admin 🤣","Armin 😂",,"Administratorweg 1234",8134,Admiswil,,,admin,Herr,,,,,,,,admin@staging.olzimmerberg.ch,,,E,Administrator,13.01.2006,14,,,,,,,,,E-Mail,Nein,,,,ja,,,,,2000001,"01.05.2020 12:34:56",Clubdesk-Benutzer
            Vorstand,Volker,,"Vorstandgasse 9",5200,Vorstadt,,,vorstand,Herr,,,,,,,,,,,A,"Standard Benutzer",13.01.2006,14,,,,,,,,,E-Mail,Nein,,,,ja,,,,,2000002,"01.05.2020 12:34:56",Clubdesk-Benutzer
            Karten,Karen,,,,,,,karten,Frau,,,,,,,,karen@staging.olzimmerberg.ch,,,A,"Standard Benutzer",13.01.2006,14,,,,,,,,,E-Mail,Nein,,,,ja,,,,,2000003,"01.05.2020 12:34:56",Clubdesk-Benutzer

            ZZZZZZZZZZ, $csv_export_content);

        $this->logout();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/mitglieder/";
    }
}
