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
        $this->assertSame([
            'SOLV',
            'GO2OL',
            'Online-Trainings',
        ], array_map(
            fn ($elem) => $elem->getText(),
            $this->getBrowserElements('.olz-links li')
        ));

        $this->click('#create-link-button');
        $this->waitForModal('#edit-link-modal');
        $this->sendKeys('#edit-link-modal #name-input', 'OLZ');
        $this->selectOption('#edit-link-modal #position-field #before-after-input', 'vor');
        $this->selectOption('#edit-link-modal #position-field .olz-entity-chooser', 'SOLV');
        $this->sendKeys('#edit-link-modal #url-input', 'https://olzimmerberg.ch');
        $this->screenshot('links_new_edit');

        $this->click('#edit-link-modal #submit-button');
        $this->waitUntilGone('#edit-link-modal');
        $browser->get($this->getUrl());
        $this->assertSame([
            'OLZ',
            'SOLV',
            'GO2OL',
            'Online-Trainings',
        ], array_map(
            fn ($elem) => $elem->getText(),
            $this->getBrowserElements('.olz-links li')
        ));

        $this->resetDb();
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
