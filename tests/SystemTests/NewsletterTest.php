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
final class NewsletterTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testNewsletterScreenshots(): void {
        $browser = $this->getBrowser();

        $this->login('vorstand', 'v0r57and');
        $browser->get($this->getUrl());

        $this->screenshot('newsletter_vorstand');

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->screenshot('newsletter_original');

        $this->click('#telegram-notifications-form input[name="monthly-preview"]');
        $this->click('#telegram-notifications-form input[name="weekly-preview"]');
        $this->click('#telegram-notifications-form input[name="deadline-warning"]');
        $this->selectOption('#telegram-notifications-form select[name="deadline-warning-days"]', '2');
        $this->click('#telegram-notifications-form input[name="daily-summary"]');
        $this->click('#telegram-notifications-form input[name="daily-summary-aktuell"]');
        $this->click('#telegram-notifications-form input[name="daily-summary-blog"]');
        $this->click('#telegram-notifications-form input[name="weekly-summary"]');
        $this->click('#telegram-notifications-form input[name="weekly-summary-forum"]');
        $this->click('#telegram-notifications-form input[name="weekly-summary-galerie"]');
        $this->click('#telegram-notifications-form input[name="weekly-summary-termine"]');
        $this->click('#telegram-notifications-submit');

        $this->click('#email-notifications-form input[name="monthly-preview"]');
        $this->click('#email-notifications-form input[name="weekly-preview"]');
        $this->click('#email-notifications-form input[name="deadline-warning"]');
        $this->selectOption('#email-notifications-form select[name="deadline-warning-days"]', '2');
        $this->click('#email-notifications-form input[name="daily-summary"]');
        $this->click('#email-notifications-form input[name="daily-summary-aktuell"]');
        $this->click('#email-notifications-form input[name="daily-summary-blog"]');
        $this->click('#email-notifications-form input[name="weekly-summary"]');
        $this->click('#email-notifications-form input[name="weekly-summary-forum"]');
        $this->click('#email-notifications-form input[name="weekly-summary-galerie"]');
        $this->click('#email-notifications-form input[name="weekly-summary-termine"]');
        $this->click('#email-notifications-submit');
        $this->waitFor('#email-notifications-success-message');
        $this->screenshot('newsletter_modified');

        $this->resetDb();

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/newsletter";
    }
}
