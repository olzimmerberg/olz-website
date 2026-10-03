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
final class StartseiteTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'dev_rw', 'staging', 'staging_rw', 'prod'])]
    public function testStartseiteHeaders(): void {
        $url = "{$this->getTargetUrl()}";
        $headers = $this->getHeaders($url);

        $this->assertSame(200, $headers['http_code']);
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
            '/olz_logo\.svg/i',
            $body
        );
    }

    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testStartseiteReadOnly(): void {
        $this->loadUrl($this->getUrl());
        $this->screenshot('startseite');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testStartseiteEditSnippet(): void {
        $this->login('admin', 'adm1n');
        $this->loadUrl($this->getUrl());

        $this->click('#important-banner .olz-editable-text .olz-edit-button');

        $this->waitForModal('#edit-snippet-modal');
        $this->sendKeys('#edit-snippet-modal #text-input', 'Neue Information!');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($image_path);
        $this->sendKeys('#edit-snippet-modal #images-upload input[type=file]', $image_path);
        $this->waitFor('#edit-snippet-modal #images-upload .olz-upload-image.uploaded');

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        assert($document_path);
        $this->sendKeys('#edit-snippet-modal #files-upload input[type=file]', $document_path);
        $this->waitFor('#edit-snippet-modal #files-upload .olz-upload-file.uploaded');

        $this->screenshot('startseite_banner_edit');

        $this->click('#edit-snippet-modal #submit-button');
        $this->waitUntil(function () {
            return strpos($this->filter('#important-banner .olz-editable-text .rendered-markdown')->getText(), 'Neue Information!') !== false;
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
