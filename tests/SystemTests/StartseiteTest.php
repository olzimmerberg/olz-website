<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class StartseiteTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'dev_rw', 'staging', 'staging_rw', 'prod'])]
    public function testStartseiteHeaders(): void {
        $url = "{$this->getTargetUrl()}";
        $headers = $this->getHeaders($url);

        $this->assertSame(200, $headers['http_code']);
    }

    #[OnlyInModes(['dev', 'dev_rw', 'staging', 'staging_rw', 'prod'])]
    public function testStartseiteHeadersLegacy(): void {
        $url = "{$this->getTargetUrl()}/startseite.php";
        $headers = $this->getHeaders($url);

        $this->assertSame(301, $headers['http_code']);
    }

    #[OnlyInModes(['dev', 'dev_rw', 'staging', 'staging_rw', 'prod'])]
    public function testStartseiteBody(): void {
        $url = "{$this->getTargetUrl()}";
        $body = file_get_contents($url) ?: '';

        $this->assertMatchesRegularExpression(
            '/<title>OL Zimmerberg<\/title>/i',
            $body
        );
        $this->assertMatchesRegularExpression(
            '/<a href=\'\/\'/i',
            $body
        );
    }

    #[OnlyInModes(['dev', 'dev_rw', 'staging', 'staging_rw', 'prod'])]
    public function testStartseiteBodyLegacy(): void {
        $url = "{$this->getTargetUrl()}/startseite.php";
        $body = file_get_contents($url) ?: '';

        $this->assertMatchesRegularExpression(
            '/<title>Weiterleitung\.\.\. - OL Zimmerberg<\/title>/i',
            $body
        );
        $this->assertMatchesRegularExpression(
            '/Startseite/i',
            $body
        );
    }

    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testStartseiteReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('startseite');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testStartseiteEditSnippet(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#important-banner .olz-editable-text .olz-edit-button');

        $this->waitForModal('#edit-snippet-modal');
        $this->sendKeys('#edit-snippet-modal #text-input', 'Neue Information!');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($image_path);
        $this->sendKeys('#edit-snippet-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-snippet-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        assert($document_path);
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
        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/";
    }
}
