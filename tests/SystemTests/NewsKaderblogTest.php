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
final class NewsKaderblogTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging'])]
    public function testNewsKaderblogReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getKaderblogDetailUrl());
        $this->screenshot('news_detail_kaderblog');
        $this->assertMatchesRegularExpression(
            '/Format\:\s*Kaderblog/i',
            $this->getBrowserElement('#format-info')?->getText() ?? '',
        );
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsKaderblogCreate(): void {
        $browser = $this->getBrowser();

        $this->login('kaderlaeufer', 'kad3rla3uf3r');
        $browser->get($this->getUrl());

        $this->click('#create-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #dropdown-menu-button');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #role-index-0');
        $this->selectOption('#edit-news-modal #format-input', 'Kaderblog');
        $this->sendKeys('#edit-news-modal #title-input', 'Das Training');
        $this->sendKeys('#edit-news-modal #content-input', "<BILD1>Detailierte Schilderung des Trainings.\n<DATEI1 text='Artikel als PDF'>");

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($image_path);
        $this->sendKeys('#edit-news-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        assert($document_path);
        $this->sendKeys('#edit-news-modal #files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-news-modal #files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });

        $this->screenshot('news_new_kaderblog_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_new_kaderblog_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsKaderblogUpdate(): void {
        $browser = $this->getBrowser();

        $this->login('kaderlaeufer', 'kad3rla3uf3r');
        $browser->get($this->getKaderblogDetailUrl());

        $this->click('#edit-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->sendKeys('#edit-news-modal #content-input', "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
        $this->screenshot('news_update_kaderblog_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_update_kaderblog_finished');

        $this->resetDb();
        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsKaderblogDetailDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getKaderblogDetailUrl());

        $this->click('#edit-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->click('#edit-news-modal #delete-entity-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-news-modal');

        $this->assertSame(404, $this->getHeaders($this->getKaderblogDetailUrl())['http_code']);

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }

    protected function getKaderblogDetailUrl(): string {
        return "{$this->getTargetUrl()}/news/10";
    }
}
