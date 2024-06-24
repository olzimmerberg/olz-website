<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverSelect;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class NewsletterTest extends SystemTestCase {
    public function testNewsletterScreenshotReadOnlyLegacy(): void {
        $this->onlyRunInModes($this::$readOnlyModes);
        $browser = $this->getBrowser();
        $this->doNewsletterReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    public function testNewsletterScreenshotReadWriteLegacy(): void {
        $this->onlyRunInModes($this::$readWriteModes);
        $browser = $this->getBrowser();
        $this->doNewsletterReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doNewsletterReadOnly(RemoteWebDriver $browser): void {
        $this->login('vorstand', 'v0r57and');
        $browser->get($this->getUrl());

        $this->screenshot('newsletter_vorstand');

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->screenshot('newsletter_original');
    }

    protected function doNewsletterReadWrite(RemoteWebDriver $browser): void {
        $this->doNewsletterReadOnly($browser);

        $this->click('#telegram-notifications-form input[name="monthly-preview"]');
        $this->click('#telegram-notifications-form input[name="weekly-preview"]');
        $this->click('#telegram-notifications-form input[name="deadline-warning"]');
        $telegram_deadline_warning_days_elem = new WebDriverSelect($this->findBrowserElement('#telegram-notifications-form select[name="deadline-warning-days"]'));
        $telegram_deadline_warning_days_elem->selectByVisibleText('2');
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
        $email_deadline_warning_days_elem = new WebDriverSelect($this->findBrowserElement('#email-notifications-form select[name="deadline-warning-days"]'));
        $email_deadline_warning_days_elem->selectByVisibleText('2');
        $this->click('#email-notifications-form input[name="daily-summary"]');
        $this->click('#email-notifications-form input[name="daily-summary-aktuell"]');
        $this->click('#email-notifications-form input[name="daily-summary-blog"]');
        $this->click('#email-notifications-form input[name="weekly-summary"]');
        $this->click('#email-notifications-form input[name="weekly-summary-forum"]');
        $this->click('#email-notifications-form input[name="weekly-summary-galerie"]');
        $this->click('#email-notifications-form input[name="weekly-summary-termine"]');
        $this->click('#email-notifications-submit');
        $this->screenshot('newsletter_modified');

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/apps/newsletter";
    }
}
