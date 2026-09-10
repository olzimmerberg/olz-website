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
final class TerminNotificationsTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminNotificationsCreate(): void {
        $this->login('admin', 'adm1n');
        $this->loadUrl($this->getDetailUrl());

        $this->click('#add-termin-notification-button');
        $this->waitForModal('#edit-termin-notification-modal');
        $this->sendKeys('#edit-termin-notification-modal #firesEarlierSeconds-number-input', '1');
        $this->selectOption('#edit-termin-notification-modal #firesEarlierSeconds-unit-input', 'Tage');
        $this->sendKeys('#edit-termin-notification-modal #title-input', 'Whoops, das ist ja schon morgen');
        $this->sendKeys('#edit-termin-notification-modal #content-input', 'Besser mit der Vorbereitung anfangen...');
        $this->click('#edit-termin-notification-modal #recipientUserId-field #dropdown-menu-button');
        $this->click('#edit-termin-notification-modal #recipientUserId-field #entity-index-0');
        $this->click("#edit-termin-notification-modal #recipientRoleId-field #dropdown-menu-button");
        $this->click('#edit-termin-notification-modal #recipientRoleId-field #entity-index-0');
        $this->click('#edit-termin-notification-modal #recipientTerminOrganizer-input');

        $this->screenshot('termin_notifications_new_edit');

        $this->click('#edit-termin-notification-modal #submit-button');
        $this->waitUntilGone('#edit-termin-notification-modal');

        $this->assertSame(<<<'ZZZZZZZZZZ'
            Zeitpunkt Titel Empfänger
            3 Wochen vorher In drei Wochen ist dein Training
            Termin-Organisator
            2 Wochen vorher Fortschritt Trainingsvorbereitung
            Volker Vorstand
            Termin-Organisator
            7 Tage vorher Kartendruck für Training
            Ressort Kartenverkauf
            Termin-Organisator
            24 Stunden vorher Whoops, das ist ja schon morgen
            Armin 😂 Admin 🤣
            Ressort Anlässe🎫, Vizepräsi
            Termin-Organisator
            ZZZZZZZZZZ, $this->getText('#termin-notifications-table'));

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminNotificationsDelete(): void {
        $this->login('admin', 'adm1n');
        $this->loadUrl($this->getDetailUrl());

        $this->click('#edit-termin-notification-button-2');
        $this->waitForModal('#edit-termin-notification-modal');
        $this->click('#edit-termin-notification-modal #delete-entity-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-termin-notification-modal');

        $this->assertSame(<<<'ZZZZZZZZZZ'
            Zeitpunkt Titel Empfänger
            3 Wochen vorher In drei Wochen ist dein Training
            Termin-Organisator
            7 Tage vorher Kartendruck für Training
            Ressort Kartenverkauf
            Termin-Organisator
            ZZZZZZZZZZ, $this->getText('#termin-notifications-table'));

        $this->resetDb();
    }

    protected function getDetailUrl(): string {
        return "{$this->getTargetUrl()}/termine/7";
    }
}
