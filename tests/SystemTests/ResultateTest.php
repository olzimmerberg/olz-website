<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ResultateTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testResultateScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doResultateReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testResultateScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doResultateReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doResultateReadOnly(RemoteWebDriver $browser): void {
        $browser->get("{$this->getUrl()}/?file=results.xml#/class0");
        sleep(1);

        $this->click('input#chk-0');
        $this->click('input#chk-1');

        $this->screenshot('resultate');
    }

    protected function doResultateReadWrite(RemoteWebDriver $browser): void {
        $this->doResultateReadOnly($browser);

        $this->login('vorstand', 'v0r57and');
        $browser->get($this->getUrl());
        $this->click('#create-result-button');
        $this->waitForModal('#edit-result-modal');
        $this->sendKeys('#edit-result-modal #name-input', 'system-test.xml');
        $iof_xml_path = __DIR__."/../../src/Utils/data/sample-data/sample-results.xml";
        $this->sendKeys('#edit-result-modal #file-upload input[type=file]', $iof_xml_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-result-modal #file-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });
        $this->click('#edit-result-modal #submit-button');
        $this->waitUntilGone('#edit-result-modal');
        $this->logout();

        $browser->get("{$this->getUrl()}/?file=system-test.xml");
        $this->assertSame('OL-Training', $this->getBrowserElement('#title-box #title')->getText());

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/resultate";
    }
}
