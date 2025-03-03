<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class NewsForumTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging'])]
    public function testNewsForumReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getForumDetailUrl());
        $this->screenshot('news_detail_forum');
        $this->assertMatchesRegularExpression(
            '/Format\:\s*Forum/i',
            $this->getBrowserElement('#format-info')?->getText() ?? '',
        );
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsForumCreate(): void {
        $browser = $this->getBrowser();

        $this->login('benutzer', 'b3nu723r');
        $browser->get($this->getUrl());

        $this->click('#create-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #dropdownMenuButton');
        $this->click('#edit-news-modal #authorUserId-authorRoleId-field #user-index-0');
        $format_select = new WebDriverSelect($this->findBrowserElement('#edit-news-modal #format-input'));
        $format_select->selectByVisibleText('Forum');
        $this->sendKeys('#edit-news-modal #title-input', 'Der Eintrag');
        $this->sendKeys('#edit-news-modal #content-input', "Der Inhalt des Eintrags");

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($image_path);
        $this->sendKeys('#edit-news-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-news-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });

        $this->screenshot('news_new_forum_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_new_forum_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsForumUpdate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getForumDetailUrl());

        $this->click('#edit-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->sendKeys('#edit-news-modal #content-input', "\n\n!!! UPDATE !!!: Dieser Eintrag wurde aktualisiert!");
        $this->screenshot('news_update_forum_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_update_forum_finished');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsForumDetailDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getForumDetailUrl());

        $this->click('#edit-news-button');
        $this->waitForModal('#edit-news-modal');
        $this->click('#edit-news-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-news-modal');

        $this->assertSame(404, $this->getHeaders($this->getForumDetailUrl())['http_code']);

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }

    protected function getForumDetailUrl(): string {
        return "{$this->getTargetUrl()}/news/8";
    }
}
