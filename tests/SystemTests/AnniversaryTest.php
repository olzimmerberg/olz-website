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
final class AnniversaryTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testAnniversaryReadOnly(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->screenshot('anniversary');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testAnniversaryRunCreate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Datum Quelle Distanz Höhenmeter Steigung Art
            15.08.2020 16:51:00 ✍️ manuell 12.34km 123m 1.00% Test Lauf
            ZZZZZZZZZZ, $this->getBrowserElement('.activities-manual')?->getText());

        $this->click('#create-run-button');
        $this->waitForModal('#edit-run-modal');
        $this->sendKeys('#edit-run-modal #runAt-input', '2020-08-01 12:00:00');
        $this->sendKeys('#edit-run-modal #distanceKm-input', '3.21');
        $this->sendKeys('#edit-run-modal #elevationMeters-input', '321');

        $this->screenshot('anniversary_run_new_edit');

        $this->click('#edit-run-modal #submit-button');
        $this->waitUntilGone('#edit-run-modal');
        $browser->get($this->getUrl());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Datum Quelle Distanz Höhenmeter Steigung Art
            15.08.2020 16:51:00 ✍️ manuell 12.34km 123m 1.00% Test Lauf
            01.08.2020 12:00:00 ✍️ manuell 3.21km 321m 10.00% Lauf
            ZZZZZZZZZZ, $this->getBrowserElement('.activities-manual')?->getText());

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testAnniversaryRunUpdate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Datum Quelle Distanz Höhenmeter Steigung Art
            15.08.2020 16:51:00 ✍️ manuell 12.34km 123m 1.00% Test Lauf
            ZZZZZZZZZZ, $this->getBrowserElement('.activities-manual')?->getText());

        $this->click('#edit-run-1-button');
        $this->waitForModal('#edit-run-modal');
        $this->clear('#edit-run-modal #runAt-input');
        $this->sendKeys('#edit-run-modal #runAt-input', '2020-08-01 12:00:00');
        $this->clear('#edit-run-modal #distanceKm-input');
        $this->sendKeys('#edit-run-modal #distanceKm-input', '3.21');
        $this->clear('#edit-run-modal #elevationMeters-input');
        $this->sendKeys('#edit-run-modal #elevationMeters-input', '321');
        $this->clear('#edit-run-modal #sportType-input');
        $this->sendKeys('#edit-run-modal #sportType-input', 'Test run');

        $this->screenshot('anniversary_run_new_edit');

        $this->click('#edit-run-modal #submit-button');
        $this->waitUntilGone('#edit-run-modal');
        $browser->get($this->getUrl());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Datum Quelle Distanz Höhenmeter Steigung Art
            01.08.2020 12:00:00 ✍️ manuell 3.21km 321m 10.00% Test run
            ZZZZZZZZZZ, $this->getBrowserElement('.activities-manual')?->getText());

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testAnniversaryRunDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Datum Quelle Distanz Höhenmeter Steigung Art
            15.08.2020 16:51:00 ✍️ manuell 12.34km 123m 1.00% Test Lauf
            ZZZZZZZZZZ, $this->getBrowserElement('.activities-manual')?->getText());

        $this->click('#edit-run-1-button');
        $this->waitForModal('#edit-run-modal');
        $this->click('#edit-run-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-run-modal');

        $browser->get($this->getUrl());
        $this->assertSame(<<<'ZZZZZZZZZZ'
            Datum Quelle Distanz Höhenmeter Steigung Art
            ZZZZZZZZZZ, $this->getBrowserElement('.activities-manual')?->getText());
        $this->assertNull($this->getBrowserElement('#edit-run-1-button'));

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/2026";
    }
}
