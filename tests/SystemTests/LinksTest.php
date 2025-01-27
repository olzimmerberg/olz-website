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
final class LinksTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testLinksCreate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-link-button');
        $this->waitForModal('#edit-link-modal');
        $this->sendKeys('#edit-link-modal #name-input', 'OLZ');
        $this->sendKeys('#edit-link-modal #position-input', '0');
        $this->sendKeys('#edit-link-modal #url-input', 'https://olzimmerberg.ch');
        $this->screenshot('links_new_edit');

        $this->click('#edit-link-modal #submit-button');
        $this->waitUntilGone('#edit-link-modal');
        $this->screenshot('links_new_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testLinksDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#edit-link-1-button');
        $this->waitForModal('#edit-link-modal');
        $this->click('#edit-link-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-link-modal');

        $browser->get($this->getUrl());
        $this->assertNull($this->getBrowserElement('#edit-link-1-button'));

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service";
    }
}
