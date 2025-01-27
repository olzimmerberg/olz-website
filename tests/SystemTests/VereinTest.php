<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class VereinTest extends SystemTestCase {
    #[OnlyInModes(['dev', 'staging', 'prod'])]
    public function testVereinScreenshotReadOnlyLegacy(): void {
        $browser = $this->getBrowser();
        $this->doVereinReadOnly($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testVereinScreenshotReadWriteLegacy(): void {
        $browser = $this->getBrowser();
        $this->doVereinReadWrite($browser);

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    protected function doVereinReadOnly(RemoteWebDriver $browser): void {
        if (!$this->isInModes('prod')) {
            $this->login('vorstand', 'v0r57and');
        }
        $browser->get($this->getUrl());
        $this->screenshot('verein');

        $browser->get("{$this->getUrl()}/praesi");
        $this->screenshot('verein_praesi');
    }

    protected function doVereinReadWrite(RemoteWebDriver $browser): void {
        $this->doVereinReadOnly($browser);

        $browser->get("{$this->getUrl()}/finanzen");
        $this->screenshot('verein_finanzen');
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testEditRessortData(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');
        $browser->get("{$this->getUrl()}/finanzen");

        $this->click('#edit-role-button');
        $this->waitForModal('#edit-role-modal');
        $this->clear('#edit-role-modal #username-input');
        $this->sendKeys('#edit-role-modal #username-input', 'kassier');
        $this->clear('#edit-role-modal #name-input');
        $this->sendKeys('#edit-role-modal #name-input', 'Kassier');
        $this->sendKeys('#edit-role-modal #title-input', 'Kassier (Finanzen)');
        $this->sendKeys('#edit-role-modal #description-input', 'Für die Abrechnung zuständig.');
        $this->sendKeys('#edit-role-modal #guide-input', 'Buchhaltung halt.');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        $this->sendKeys('#edit-role-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-role-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });
        $this->click('#edit-role-modal #images-upload .olz-upload-image.uploaded #copy-button');
        $this->sendKeys('#edit-role-modal #description-input', "\n\n".WebDriverKeys::CONTROL.'v');
        $this->sendKeys('#edit-role-modal #guide-input', "\n\n".WebDriverKeys::CONTROL.'v');

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        $this->sendKeys('#edit-role-modal #files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-role-modal #files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });
        $this->click('#edit-role-modal #files-upload .olz-upload-file.uploaded #copy-button');
        $this->sendKeys('#edit-role-modal #description-input', "\n\n".WebDriverKeys::CONTROL.'v');
        $this->sendKeys('#edit-role-modal #guide-input', "\n\n".WebDriverKeys::CONTROL.'v');

        $this->assertFalse($this->getBrowserElement('#edit-role-modal #parentRole-field #dropdownMenuButton')->isEnabled());
        $this->assertFalse($this->getBrowserElement('#edit-role-modal #canHaveChildRoles-input')->isEnabled());
        $this->assertFalse($this->getBrowserElement('#edit-role-modal #indexWithinParent-input')->isEnabled());
        $this->assertFalse($this->getBrowserElement('#edit-role-modal #featuredIndex-input')->isEnabled());
        $this->screenshot('verein_ressort_edit');

        $this->click('#edit-role-modal #submit-button');
        $this->waitUntilGone('#edit-role-modal');

        $this->assertSame(404, $this->getHeaders("{$this->getUrl()}/finanzen")['http_code']);
        $this->assertSame(200, $this->getHeaders("{$this->getUrl()}/kassier")['http_code']);

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testAddRessortAssignee(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');

        $browser->get("{$this->getUrl()}/finanzen");
        $this->assertCount(2, $this->getBrowserElements('.olz-user-info-card-list .assignee'));
        $this->click('#add-role-user-button');
        $this->waitForModal('#add-role-user-modal');
        $this->click('#add-role-user-modal #newUser-field #dropdownMenuButton');
        $this->sendKeys('#add-role-user-modal #newUser-field #entity-search-input', 'Nutzer');
        $this->waitABit(); // wait for users
        $this->screenshot('verein_add_assignee');
        $this->click('#add-role-user-modal #newUser-field #entity-index-0');
        $this->click('#add-role-user-modal #submit-button');
        $this->waitUntilGone('#add-role-user-modal');

        $browser->get("{$this->getUrl()}/finanzen");
        $this->assertCount(3, $this->getBrowserElements('.olz-user-info-card-list .assignee'));

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testRemoveRessortAssignee(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');

        $browser->get("{$this->getUrl()}/finanzen");
        $this->assertCount(2, $this->getBrowserElements('.olz-user-info-card-list .assignee'));
        $this->click('.olz-user-info-card-list .assignee:nth-of-type(2) #delete-role-user-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');

        $browser->get("{$this->getUrl()}/finanzen");
        $this->assertCount(1, $this->getBrowserElements('.olz-user-info-card-list .assignee'));

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testAddSubRole(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');
        $browser->get("{$this->getUrl()}/finanzen");

        $this->click('#add-sub-role-button');
        $this->waitForModal('#edit-role-modal');
        $this->clear('#edit-role-modal #username-input');
        $this->sendKeys('#edit-role-modal #username-input', 'rechnungspruefer');
        $this->clear('#edit-role-modal #name-input');
        $this->sendKeys('#edit-role-modal #name-input', 'Rechnungsprüfer');
        $this->sendKeys('#edit-role-modal #description-input', 'Für das Prüfen der Rechnung zuständig.');
        $this->sendKeys('#edit-role-modal #guide-input', 'Buchprüfung halt.');
        $this->click('#edit-role-modal #canHaveChildRoles-input');
        $this->sendKeys('#edit-role-modal #indexWithinParent-input', '1');
        $this->sendKeys('#edit-role-modal #featuredIndex-input', '');
        $this->click('#edit-role-modal #submit-button');
        $this->waitUntilGone('#edit-role-modal');

        $browser->get("{$this->getUrl()}/finanzen");
        $this->assertSame(200, $this->getHeaders("{$this->getUrl()}/rechnungspruefer")['http_code']);
        $this->assertStringContainsString('Rechnungsprüfer', $this->getBrowserElement('#sub-roles')->getText());

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testRemoveSubRole(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');

        $browser->get("{$this->getUrl()}/revisoren");
        $this->click('#edit-role-button');
        $this->waitForModal('#edit-role-modal');
        $this->click('#edit-role-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');

        $browser->get("{$this->getUrl()}/revisoren");
        $this->assertSame(404, $this->getHeaders("{$this->getUrl()}/revisoren")['http_code']);

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/verein";
    }
}
