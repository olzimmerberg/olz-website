<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Members\Utils;

use Olz\Apps\Members\Utils\MembersUtils;
use Olz\Entity\Members\Member;
use Olz\Entity\Users\User;
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

    public function testGetMemberStatus(): void {
        $utils = new MembersUtils();
        $this->assertSame('E', $utils->getMemberStatus($this->expected_data[0]));
        $this->assertNull($utils->getMemberStatus(['Vorname' => 'Max']));
    }

    public function testGetMemberStatusEnabled(): void {
        $utils = new MembersUtils();
        // Status 'E' (Ehrenmitglied) is an active member.
        $this->assertTrue($utils->getMemberStatusEnabled($this->expected_data[0]));
        $this->assertTrue($utils->getMemberStatusEnabled(['Status' => 'Aktivmitglied']));
        $this->assertTrue($utils->getMemberStatusEnabled(['Status' => 'Passivmitglied']));
        $this->assertFalse($utils->getMemberStatusEnabled(['Status' => 'Ehemaliges Mitglied']));
        $this->assertFalse($utils->getMemberStatusEnabled(['Status' => 'Verstorben']));
        // Unknown / missing status defaults to "off".
        $this->assertFalse($utils->getMemberStatusEnabled(['Status' => 'Unbekannter Status']));
        $this->assertFalse($utils->getMemberStatusEnabled(['Vorname' => 'Max']));
        $this->assertFalse($utils->getMemberStatusEnabled([]));
    }

    public function testIsMemberActiveAt(): void {
        $utils = new MembersUtils();
        /** @param array<string, string> $data */
        $make_member = function (array $data, ?string $deleted_at): Member {
            $member = new Member();
            $member->setIdent('1000001');
            $member->setData(json_encode($data) ?: '');
            $member->setDeletedAt($deleted_at !== null ? new \DateTime($deleted_at) : null);
            return $member;
        };

        $active = $make_member(['Eintritt' => '13.01.2006', 'Austritt' => ''], null);
        $this->assertTrue($utils->isMemberActiveAt($active, new \DateTime('2020-03-15')));
        $this->assertTrue($utils->isMemberActiveAt($active, new \DateTime('2020-04-15')));

        $left = $make_member(['Eintritt' => '13.01.2006', 'Austritt' => '31.03.2020'], null);
        $this->assertTrue($utils->isMemberActiveAt($left, new \DateTime('2020-03-15')));
        $this->assertFalse($utils->isMemberActiveAt($left, new \DateTime('2020-04-15')));

        $joined_later = $make_member(['Eintritt' => '01.05.2020', 'Austritt' => ''], null);
        $this->assertFalse($utils->isMemberActiveAt($joined_later, new \DateTime('2020-04-15')));
        $this->assertTrue($utils->isMemberActiveAt($joined_later, new \DateTime('2020-05-15')));

        $deleted = $make_member(['Eintritt' => '13.01.2006', 'Austritt' => ''], '2020-02-15 00:00:00');
        $this->assertFalse($utils->isMemberActiveAt($deleted, new \DateTime('2020-03-15')));
        $this->assertTrue($utils->isMemberActiveAt($deleted, new \DateTime('2020-02-15')));
    }

    public function testUpdateUserMembershipDates(): void {
        $utils = new MembersUtils();
        $user = new User();
        $user->setId(1);
        $user->setUsername('user');
        $user->setFirstName('First');
        $user->setLastName('Last');
        $user->setStreet(null);
        $user->setPostalCode(null);
        $user->setCity(null);
        $user->setJoinedOn(null);
        $user->setLeftOn(null);
        $member = new Member();
        $member->setIdent('1000001');
        $member->setUser($user);
        $member->setData(json_encode(['Eintritt' => '13.01.2006', 'Austritt' => '31.03.2020']) ?: '');

        $utils->update($member, $user);

        $this->assertSame('2006-01-13', $user->getJoinedOn()?->format('Y-m-d'));
        $this->assertSame('2020-03-31', $user->getLeftOn()?->format('Y-m-d'));
        $this->assertSame([
            'INFO User user: joined_on was updated.',
            'INFO User user: left_on was updated.',
            'INFO Field Nachname was updated.',
            'INFO Field Vorname was updated.',
            'INFO Field Benutzer-Id was updated.',
        ], $this->getLogs());
    }
}
