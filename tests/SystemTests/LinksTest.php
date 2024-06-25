<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class LinksTest extends SystemTestCase {
    public function testLinksScreenshots(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#create-link-button');
        $this->sendKeys('#edit-link-modal #name-input', 'OLZ');
        $this->sendKeys('#edit-link-modal #position-input', '0');
        $this->sendKeys('#edit-link-modal #url-input', 'https://olzimmerberg.ch');
        $this->screenshot('links_new_edit');

        $this->click('#submit-button');
        sleep(4);
        $this->screenshot('links_new_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/service";
    }
}
