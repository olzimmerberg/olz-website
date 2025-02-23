<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class FragenUndAntwortenTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw', 'dev', 'staging', 'prod'])]
    public function testFragenUndAntwortenReadOnly(): void {
        $browser = $this->getBrowser();

        $browser->get($this->getUrl());
        $this->screenshot('fragen_und_antworten');

        $browser->get("{$this->getUrl()}/was_ist_ol");
        $this->screenshot('fragen_und_antworten_detail');

        // TODO: Dummy assert
        $this->assertDirectoryExists(__DIR__);
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testEditQuestionData(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');
        $browser->get("{$this->getUrl()}/ausprobieren");

        $this->click('#edit-question-button');
        $this->waitForModal('#edit-question-modal');
        $this->click('#edit-question-modal #categoryId-field #dropdownMenuButton');
        $this->click('#edit-question-modal #categoryId-field #entity-index-1');
        $this->clear('#edit-question-modal #positionWithinCategory-input');
        $this->sendKeys('#edit-question-modal #positionWithinCategory-input', '5');
        $this->clear('#edit-question-modal #ident-input');
        $this->sendKeys('#edit-question-modal #ident-input', 'testen');
        $this->clear('#edit-question-modal #question-input');
        $this->sendKeys('#edit-question-modal #question-input', 'Wie kann ich OL testen?');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($image_path);
        $this->sendKeys('#edit-question-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-question-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 1;
        });
        $this->click('#edit-question-modal #images-upload .olz-upload-image.uploaded #copy-button');
        $this->sendKeys('#edit-question-modal #answer-input', "\n\n".WebDriverKeys::CONTROL.'v');

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        assert($document_path);
        $this->sendKeys('#edit-question-modal #files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-question-modal #files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 1;
        });
        $this->click('#edit-question-modal #files-upload .olz-upload-file.uploaded #copy-button');
        $this->sendKeys('#edit-question-modal #answer-input', "\n\n".WebDriverKeys::CONTROL.'v');

        $this->screenshot('fragen_und_antworten_frage_edit');

        $this->click('#edit-question-modal #submit-button');
        $this->waitUntilGone('#edit-question-modal');

        $this->assertSame(404, $this->getHeaders("{$this->getUrl()}/ausprobieren")['http_code']);
        $this->assertSame(200, $this->getHeaders("{$this->getUrl()}/testen")['http_code']);

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testDeleteQuestion(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');

        $browser->get($this->getUrl());
        $this->assertCount(16, $this->getBrowserElements('.olz-posting-list-item'));
        $this->click('.olz-posting-list-item:nth-of-type(2) .edit-question-list-button');
        $this->waitForModal('#edit-question-modal');
        $this->click('#edit-question-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');

        $browser->get($this->getUrl());
        $this->assertCount(15, $this->getBrowserElements('.olz-posting-list-item'));

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testCreateQuestionCategory(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');

        $browser->get($this->getUrl());
        $this->assertCount(3, $this->getBrowserElements('h2.category'));
        $this->click('#create-question-category-button');
        $this->waitForModal('#edit-question-category-modal');
        $this->sendKeys('#edit-question-category-modal #position-input', '4');
        $this->sendKeys('#edit-question-category-modal #name-input', 'Test');
        $this->screenshot('fragen_und_antworten_category_create');
        $this->click('#edit-question-category-modal #submit-button');
        $this->waitUntilGone('#edit-question-category-modal');

        $browser->get($this->getUrl());
        $this->assertCount(4, $this->getBrowserElements('h2.category'));

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testEditQuestionCategory(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');

        $browser->get($this->getUrl());
        $this->assertSame(
            ['Allgemein', 'Website', 'Leer'],
            array_map(
                fn ($elem) => $elem->getText(),
                $this->getBrowserElements('h2.category'),
            ),
        );
        $this->click('h2.category:nth-of-type(2) .edit-question-category-list-button');
        $this->waitForModal('#edit-question-category-modal');
        $this->clear('#edit-question-category-modal #position-input');
        $this->sendKeys('#edit-question-category-modal #position-input', '4');
        $this->clear('#edit-question-category-modal #name-input');
        $this->sendKeys('#edit-question-category-modal #name-input', 'Test');
        $this->click('#edit-question-category-modal #submit-button');
        $this->waitUntilGone('#edit-question-category-modal');

        $browser->get($this->getUrl());
        $this->assertSame(
            ['Allgemein', 'Leer', 'Test'],
            array_map(
                fn ($elem) => $elem->getText(),
                $this->getBrowserElements('h2.category'),
            ),
        );

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testDeleteQuestionCategory(): void {
        $browser = $this->getBrowser();
        $this->login('vorstand', 'v0r57and');

        $browser->get($this->getUrl());
        $this->assertCount(3, $this->getBrowserElements('h2.category'));
        $this->click('h2.category:nth-of-type(2) .edit-question-category-list-button');
        $this->waitForModal('#edit-question-category-modal');
        $this->click('#edit-question-category-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');

        $browser->get($this->getUrl());
        $this->assertCount(2, $this->getBrowserElements('h2.category'));

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/fragen_und_antworten";
    }
}
