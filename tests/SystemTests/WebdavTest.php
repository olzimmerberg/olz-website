<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class WebdavTest extends SystemTestCase {
    public function testWebdavScreenshots(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();

        $declare_flaky_elements = <<<'ZZZZZZZZZZ'
                const columnSelector = '.nodeTable tr td:not(.nameColumn):not(.typeColumn)';
                const propTableSelector = '.propTable';
                [
                    ...document.querySelectorAll(columnSelector),
                    ...document.querySelectorAll(propTableSelector),
                ].map(elem => {
                    elem.style.minWidth = '250px';
                    elem.classList.add('test-flaky');
                });
            ZZZZZZZZZZ;

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());
        $browser->executeScript($declare_flaky_elements);
        $this->screenshot('webdav_admin_php_session');
        $this->logout();

        $this->login('vorstand', 'v0r57and');
        $browser->get($this->getUrl());
        $browser->executeScript($declare_flaky_elements);
        $this->screenshot('webdav_vorstand_php_session');
        $this->logout();

        $this->login('karten', 'kar73n');
        $browser->get($this->getUrl());
        $browser->executeScript($declare_flaky_elements);
        $this->screenshot('webdav_karten_php_session');
        $this->logout();

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());
        $browser->executeScript($declare_flaky_elements);
        $this->screenshot('webdav_benutzer_php_session');
        $this->logout();

        $browser->get($this->getUrl());
        $browser->executeScript($declare_flaky_elements);
        $this->screenshot('webdav_anonym_php_session');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/files/webdav/";
    }
}
