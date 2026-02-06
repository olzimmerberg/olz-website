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
final class NewsGalerieTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging'])]
    public function testNewsGalerieReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getGalerieDetailUrl());
        $this->screenshot('news_detail_galerie');
        $this->assertMatchesRegularExpression(
            '/Format\:\s*Galerie/i',
            $this->getBrowserElement('#format-info')?->getText() ?? '',
        );
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsGalerieCreate(): void {
        $browser = $this->getBrowser();

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());

        $this->click('#create-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #dropdown-menu-button');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #user-index-0');
        $this->selectOption('#edit-news-modal #format-input', 'Galerie');
        $this->sendKeys('#edit-news-modal #title-input', 'Das Fotoshooting');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($image_path);
        $this->sendKeys('#edit-news-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $this->screenshot('news_new_galerie_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_new_galerie_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsGalerieUpdate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getGalerieDetailUrl());

        $this->click('#edit-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->sendKeys('#edit-news-modal #title-input', "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
        $this->screenshot('news_update_galerie_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_update_galerie_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsGalerieDetailDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getGalerieDetailUrl());

        $this->click('#edit-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->click('#edit-news-modal #delete-entity-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-news-modal');

        $this->assertSame(404, $this->getHeaders($this->getGalerieDetailUrl())['http_code']);

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }

    protected function getGalerieDetailUrl(): string {
        return "{$this->getTargetUrl()}/news/6";
    }
}
