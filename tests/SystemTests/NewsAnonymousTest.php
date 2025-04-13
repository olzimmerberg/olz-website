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
final class NewsAnonymousTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsAnonymousCreate(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());

        $this->click('#create-news-button');
        $this->click('#create-anonymous-button');
        $this->waitForModal('#edit-news-modal');
        $this->sendKeys('#edit-news-modal #authorName-input', 'Anonymous Integration Test');
        $this->sendKeys('#edit-news-modal #authorEmail-input', 'anonymous@staging.olzimmerberg.ch');
        $this->sendKeys('#edit-news-modal #title-input', 'Der Eintrag');
        $this->sendKeys('#edit-news-modal #content-input', "Der Inhalt des Eintrags");
        $this->click('#edit-news-modal #captcha-dev');

        $this->screenshot('news_new_anonymous_edit');

        $this->click('#edit-news-modal #submit-button');
        $this->waitUntilGone('#edit-news-modal');
        $this->screenshot('news_new_anonymous_finished');

        $this->resetDb();
        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/news";
    }
}
