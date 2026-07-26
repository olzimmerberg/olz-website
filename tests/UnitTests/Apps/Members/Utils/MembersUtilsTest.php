<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Members\Utils;

use Olz\Apps\Members\Utils\MembersUtils;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Apps\Members\Utils\MembersUtils
 */
final class MembersUtilsTest extends UnitTestCase {
    /** @var array<array<string, string>> */
    protected array $expected_data = [[
        'Nachname' => 'Goscinny',
        'Vorname' => 'René',
        'Firma' => '',
        'Adresse' => 'Dorfstrasse 26',
        'PLZ' => '12345',
        'Ort' => 'Gallisches Dorf',
        'Telefon Privat' => '044 123 45 67',
        'Telefon Mobil' => '079 123 45 67',
        'E-Mail' => 'ren.gosc@dargaud.fr',
        '[Rolle]' => 'Standard Benutzer',
        'Benutzer-Id' => 'rene.goscinny',
        'Anrede' => 'Herr',
        'Titel' => '',
        'Briefanrede' => '',
        'Adress-Zusatz' => '',
        'Land' => '',
        'Nationalität' => '',
        'Telefon Geschäft' => '',
        'Fax' => '',
        'E-Mail Alternativ' => '',
        '[Gruppen]' => 'Ehrenmitglieder (Läufer(in))',
        'Status' => 'E',
        'Eintritt' => '',
        'Mitgliedsjahre' => '',
        'Austritt' => '',
        'Zivilstand' => '',
        'Geschlecht' => 'männlich',
        'Geburtsdatum' => '',
        'Jahrgang' => '',
        'Alter' => '',
        'Bemerkungen' => "Die spinnen,\ndie Römer",
        'Firmen-Webseite' => '',
        'Nie mahnen' => 'Nein',
        'IBAN' => '',
        'BIC' => '',
        'Kontoinhaber' => '',
        'Mail-MV' => 'ja',
        'SOLV NR' => 'AA1-GOR',
        'Badge Nummer' => '',
        'Werbegrund' => '',
        'Geburtsjahr' => '1926',
        'AHV-Nr.' => '',
        'Versandart' => 'E-Mail',
        '[Erstellt am]' => '02.05.2025 10:13',
        '[Erstellt durch]' => 'Attinger Sophie',
        '[Geändert am]' => '04.04.2026 22:03',
        '[Geändert durch]' => 'Huber Bernadette',
        '[Id]' => '1000056',
        '[Zuletzt geändert am]' => '04.04.2026 22:03:05',
        '[Zuletzt geändert von]' => 'Huber Bernadette',
    ]];

    public function testParseCsvWindows(): void {
        $csv_content = file_get_contents(__DIR__.'/data/clubdesk-export-win.csv') ?: '';
        $utils = new MembersUtils();
        $data = $utils->parseCsv($csv_content);
        $this->assertSame([], $this->getLogs());
        $this->assertEquals($this->expected_data, $data);
    }

    public function testParseCsvMac(): void {
        $csv_content = file_get_contents(__DIR__.'/data/clubdesk-export-mac.csv') ?: '';
        $utils = new MembersUtils();
        $data = $utils->parseCsv($csv_content);
        $this->assertSame([], $this->getLogs());
        $this->assertEquals($this->expected_data, $data);
    }
}
