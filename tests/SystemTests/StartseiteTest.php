<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class StartseiteTest extends SystemTestCase {
    public function testStartseiteHeaders(): void {
        $this->onlyRunInModes($this::$sutModes);

        $url = "{$this->getTargetUrl()}";
        $headers = $this->getHeaders($url);

        $this->assertSame(200, $headers['http_code']);
    }

    public function testStartseiteHeadersLegacy(): void {
        $this->onlyRunInModes($this::$sutModes);

        $url = "{$this->getTargetUrl()}/startseite.php";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
    }

    public function testStartseiteBody(): void {
        $this->onlyRunInModes($this::$sutModes);

        $url = "{$this->getTargetUrl()}";
        $body = file_get_contents($url);

        $this->assertMatchesRegularExpression(
            '/<title>OL Zimmerberg<\/title>/i',
            $body
        );
        $this->assertMatchesRegularExpression(
            '/Startseite/i',
            $body
        );
    }

    public function testStartseiteBodyLegacy(): void {
        $this->onlyRunInModes($this::$sutModes);

        $url = "{$this->getTargetUrl()}/startseite.php";
        $body = file_get_contents($url);

        $this->assertMatchesRegularExpression(
            '/<title>OL Zimmerberg<\/title>/i',
            $body
        );
        $this->assertMatchesRegularExpression(
            '/Startseite/i',
            $body
        );
    }

    public function testStartseiteScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doStartseiteReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testStartseiteScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doStartseiteReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doStartseiteReadOnly(RemoteWebDriver $browser): void {
        $browser->get($this->getUrl());
        $this->screenshot('startseite');
    }

    protected function doStartseiteReadWrite(RemoteWebDriver $browser): void {
        $this->doStartseiteReadOnly($browser);

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#important-banner .olz-editable-text .olz-edit-button');
        sleep(1);

        $this->sendKeys('#edit-snippet-modal #text-input', 'Neue Information!');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-snippet-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        $this->sendKeys('#edit-snippet-modal #files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-snippet-modal #files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });

        $this->screenshot('startseite_banner_edit');

        $this->click('#edit-snippet-modal #submit-button');
        $browser->wait()->until(function () use ($browser) {
            $rendered_html = $browser->findElement(
                WebDriverBy::cssSelector('#important-banner .olz-editable-text .rendered-markdown')
            );
            return strpos($rendered_html->getText(), 'Neue Information!') !== false;
        });
        $this->screenshot('startseite_banner_finished');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }
}
